/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * "Fun tip" is a simple plugin which adds fancy CSS3 styles to plain text, password, and textarea inputs.
 * In addition to the styles, animated tips can be displayed on the right side of the input, which will 
 * give the web designer an easy to add helpful hints to add front-end validation.
*/

(function($) {
	$(document).ready(function() {
	//A helper plugin, which will detect if an element has an attribute
		$.fn.hasAttr = function(attr) {
			var attrVal = this.attr(attr);
			
			return (attrVal !== undefined) && (attrVal !== false);
		}; 
		
	//The fun tip plugin itself
		$.fn.funtip = function(options) {
		/*
		Field settings 
		--------------------------------------------- 
		*/	
		
		//Add flexibility to this plugin by extending the default options
			var defaults = $.extend($.fn.funtip.options, options);
			
		//These are the styles which will be applied to the text input controls, containers, and tips
			var glowBox = {
				'font-size' : '14px',
				'border' : '1px solid ' + defaults.borderDefaultColor,
				'border-radius' : defaults.borderRadius,
				'padding' : '0px 10px 0px 0px', 
				'width' : defaults.inputWidth,
				'display' : 'block',
				'background' : defaults.borderBackgroundColor
			};

			var glowBoxHover = {
				'border' : '1px solid ' + defaults.borderHoverColor,
				'-webkit-box-shadow' : '0 0 ' + defaults.borderGlowSpread + ' ' + defaults.borderGlowHover,
				'-moz-box-shadow' : '0 0 ' + defaults.borderGlowSpread + ' ' + defaults.borderGlowHover,
				'box-shadow' : '0 0 ' + defaults.borderGlowSpread + ' ' + defaults.borderGlowHover
			};

			var glowBoxActive = {
				'border' : '1px solid ' + defaults.borderActiveColor,
				'-webkit-box-shadow' : '0 0 ' + defaults.borderGlowSpread + ' ' + defaults.borderGlowActive,
				'-moz-box-shadow' : '0 0 ' + defaults.borderGlowSpread + ' ' + defaults.borderGlowActive,
				'box-shadow' : '0 0 ' + defaults.borderGlowSpread + ' ' + defaults.borderGlowActive
			};

			var inputStyle = {
				'background' : defaults.inputBackgroundColor,
				'border' : 'none',
				'width' : defaults.inputWidth,
				'font-size' :'14px',
				'padding' : '11px 0px 11px 10px',
				'border-radius' : defaults.borderRadius,
				'color' : defaults.inputTextColor,
				'resize' : 'none' //Disable textarea resize handles
			};

			var tip = {
				'position' : 'absolute',
				'top' : 'inherit',
				'padding' : '11px 0px 12px 10px',
				'display' : 'none',
				'width' : defaults.tipWidth,
				'max-width' : defaults.tipWidth,
				'border-bottom-right-radius' : defaults.borderRadius,
				'border-top-right-radius' : defaults.borderRadius,
				'color' : defaults.tipTextColor
			};
			
		//Calculate the overall width of the container when stretched to fit the tip box
		//Add 12px to accommodate for the padding
			var width = parseFloat(defaults.inputWidth.replace(/[^0-9]/g, '')) + parseFloat(defaults.tipWidth.replace(/[^0-9]/g, '')) + 12 + 'px';
			 
		//The variable will track whether or not the system triggered the focus/blur event, and slide the tips accordingly
			var autoTriggered = false;
			
		//Apply this plugin to each of the selected objects
			this.each(function() {
			/*
			Build and gather necessary components
			--------------------------------------------- 
			*/	
				
			//Assign a variable name and build unbuilt parts of interest
				var input = $(this);
				input.wrap('<div></div>');
				var container = input.parent();
				container.append('<span></span>');
				var tipBox = container.children('span');
				
			//Grab all of the tip box messages
				var message = $.parseJSON(input.attr('title')) ? $.parseJSON(input.attr('title')) : '';
				
			//Clear the "title" attribute so the JSON array isn't displayed on mouse over
				input.removeAttr('title');
				
			//Add the respective styles to each element
				input.css(inputStyle);
				container.css(glowBox);
				tipBox.css(tip);
				
			//Convert each class to the respective HTML5 component, and vice versa
			//Requirements
				if (input.hasClass('required')) {
					input.attr('required', 'required');
				}
				
				if (input.hasAttr('required') && !Modernizr.input.required) {
					input.addClass('required');
				} else {
					input.removeClass('required');
				}
				
			//Email fields
			//The field type attribute cannot be changed for security purposes, and there isn't a work-around :(
				if (input.attr('type') == 'email' && !Modernizr.inputtypes.email) {
					input.addClass('email');
				} else {
					input.removeClass('email');
				}
				
			/*
			Input field event listeners
			--------------------------------------------- 
			*/	
				
			//Listen for mouse and click events to change the container UI
				input.focus(function() {
				//Do not slide the tip out if the system triggered this focus event
					if (!autoTriggered) {
					//Adjust the input field
						input.css({
							'border-top-right-radius' : '0px',
							'border-bottom-right-radius' : '0px',
							'border-right' : '2px solid #5F5F5F'
						});
						
					//Stretch the container
						container.css(glowBoxActive).animate({
							'width' : width
						}, defaults.borderStretchTime);				
					
					//Show the tip
						if (!input.hasClass('funtipInvalidField')) {
							var showMessage = $.fn.funtip.message(message, message.standard, defaults.standard);
							tipBox.text(showMessage).css(tip).fadeIn(defaults.borderStretchTime);
							container.css({
								'background-color' : defaults.tipColorDefault
							});
						}
					}
				}).blur(function() {
				/*
				Input field event listeners
				--------------------------------------------- 
				*/
										
				//Hiding the tip will depend on whether or not the field passes validation
					var showMessage;
					
				//Check if this field is required
					if (input.hasClass('required') || input.hasAttr('required')) {
					//Use this plugin for validation if the browser does not support this kind of HTML5 validation
						if (!Modernizr.input.required) {
							$.fn.funtip.validate.required(input);
							showMessage = $.fn.funtip.message(message, message.required, defaults.required);
					//Remove the invalid message (if any), and use browser support
						} else {
							input.removeClass('funtipInvalidField');
						}
					//The same pattern follows in the ones below...
					}
					
				//Check if this field is numeric
					if ((!input.hasClass('funtipInvalidField') || (!input.hasClass('required') && !input.hasAttr('required'))) && input.hasClass('numeric')) {
						$.fn.funtip.validate.numeric(input);
						showMessage = $.fn.funtip.message(message, message.error, defaults.error);
					}
					
				//Check if this field will carry an email address
					if ((!input.hasClass('funtipInvalidField') || (!input.hasClass('required') && !input.hasAttr('required'))) && (input.hasClass('email')  || input.attr('type') == "email")) {
						if (!Modernizr.inputtypes.email) {
							$.fn.funtip.validate.email(input);
							showMessage = $.fn.funtip.message(message, message.error, defaults.error);
						} else {
							input.removeClass('funtipInvalidField');
						}
					}
					
				//Check if this field has a minimium input limit
					if ((!input.hasClass('funtipInvalidField') || (!input.hasClass('required') && !input.hasAttr('required'))) && input.hasAttr('class') && input.attr('class').indexOf('min') !== -1) {
						$.fn.funtip.validate.min(input);
						showMessage = $.fn.funtip.message(message, message.error, defaults.error);
					}
					
				//Check if this field has a maxmium input limit
					if ((!input.hasClass('funtipInvalidField') || (!input.hasClass('required') && !input.hasAttr('required'))) && input.hasAttr('class') && input.attr('class').indexOf('max') !== -1) {
						$.fn.funtip.validate.max(input);
						showMessage = $.fn.funtip.message(message, message.error, defaults.error);
					}
					
				//Adjust the elements as required
					if (!input.hasClass('funtipInvalidField')) {
					//Do not slide the tip in (since it wasn't slid out) if the system triggered this blur event
						if (!autoTriggered) {
						//Keep the tip out if the plugin is set to show it on validation success
							if (input.hasClass('noHide') || !defaults.hideTipOnSuccess) {
								showMessage = $.fn.funtip.message(message, message.success, defaults.success);
								
							//Change the tip background and message
								tipBox.text(showMessage);
								container.removeAttr('style').css(glowBox).css({
									'background-color' : defaults.tipColorGood,
									'width' : width
								});
						//Slide the tip in if the plugin is set to hide it on validation success
							} else {
							//Adjust the input field
								input.css({
									'border-top-right-radius' : defaults.borderRadius,
									'border-bottom-right-radius' : defaults.borderRadius,
									'border-right' : '0px solid #FFFFFF'
								});
								
							//Retract the container
								container.removeAttr('style').css(glowBox).css({
									'width' : width
								}).animate({
									'width' : defaults.inputWidth
								}, defaults.borderStretchTime);
								
							//Hide the tip
								tipBox.text('').fadeOut(defaults.borderStretchTime).removeAttr('style');
							}
						}
					} else {
					//These actions will occur if the *user* triggered the validation message display
						if (!autoTriggered) {
						//Remove the container focus color, but maintain its width
							container.removeAttr('style').css(glowBox).css({
								'width' : width
							});
							
						//Highlight the tip and display the required message
							tipBox.text(showMessage);
							container.css({
								'background-color' : defaults.tipColorBad
							});
					//These actions will occur if the *system* triggered the validation message display
						} else {							
						//Adjust the input field
							input.css({
								'border-top-right-radius' : '0px',
								'border-bottom-right-radius' : '0px',
								'border-right' : '2px solid #5F5F5F'
							});
							
						//Stretch the container
							container.css(glowBox).css({
								'width' : width
							}, defaults.borderStretchTime);				
						
						//Show the tip
							tipBox.text(showMessage).css(tip).fadeIn(defaults.borderStretchTime);
							container.css({
								'background-color' : defaults.tipColorBad
							});
						}
					}
				});
				
			/*
			Container event listeners
			--------------------------------------------- 
			*/	
				
				container.mouseover(function() {
				//If the input field is focused, then do not add the hover style
					if (!input.is(':focus')) {
					//If the field is invalid, then preserve the error tip box color
						if (input.hasClass('funtipInvalidField')) {
							container.css(glowBoxHover).css({
								'background-color' : defaults.tipColorBad
							});
						} else {
						//If the field is valid, then preserve the success tip box color
							if (tipBox.is(':visible') && (input.hasClass('noHide') || !defaults.hideTipOnSuccess)) {
								container.css(glowBoxHover).css({
									'background-color' : defaults.tipColorGood,
									'width' : width
								});
							} else {
								container.css(glowBoxHover);
							}
						}
					}
				}).mouseout(function() {
				//If the input field is focused, then do not remove the focus style
					if (!input.is(':focus')) {
					//If the field is invalid, then preserve the error tip box color
						if (input.hasClass('funtipInvalidField')) {
							container.removeAttr('style').css(glowBox).css({
								'background-color' : defaults.tipColorBad
							});
						} else {
						//If the field is valid, then preserve the success tip box color
							if (tipBox.is(':visible') && (input.hasClass('noHide') || !defaults.hideTipOnSuccess)) {
								container.removeAttr('style').css(glowBox).css({
									'background-color' : defaults.tipColorGood,
									'width' : width
								});
							} else {
								container.removeAttr('style').css(glowBox);
							}
						}
					}
					
				//If the form field is invalid and the input field is focused, then remove the focus style, but preserve the width
					if (input.hasClass('funtipInvalidField') && !input.is(':focus')) {						
						container.removeAttr('style').css(glowBox).css({
							'width' : width,
							'background-color' : defaults.tipColorBad
						});
					}
				});
			});
			
		/*
		Submit event listeners
		--------------------------------------------- 
		*/
			var fields = this;
			
			defaults.parentForm.submit(function(event) {				
				var canSubmit = true;
				autoTriggered = true;
				
			//Find all invalid fields
				fields.each(function() {
					var field = $(this);
					
				//The easiest way to check to invalid fields it to manually focus and blur them all, and the above code will handle them
					fields.focus().blur();
					
				//Check to see if an invalid field was reached and stop there
					if (field.hasClass('required') && (field.hasClass('funtipInvalidField') || field.val() == '')) {
						canSubmit = false;
						return false;
					}
				});
				
				autoTriggered = false;
				
			//Prevent form submission if validation failed
				if (!canSubmit) {
					event.preventDefault();
				}
			});
		
		//Auto-select the first input field
			if (defaults.autoFocusFirst) {
				$(this).first().focus();
			}
		};
		
	/*
	Supporting methods and objects
	--------------------------------------------- 
	*/	
		
	//Form element validation
		$.fn.funtip.validate = {
		//A base method for handling optional validations with an supplied condition
			base : function(input, condition) {
				if ((!input.hasClass('required') && !input.hasAttr('required') && input.val().length == 0) || (input.val().length > 0 && condition)) {
					input.removeClass('funtipInvalidField');
					return true;
				} else {
					input.addClass('funtipInvalidField');
					return false;
				}
			},
			
		//Check if a value is provided
			required : function(input) {
				if (input.val() == "") {
					input.addClass('funtipInvalidField');
					return false;
				} else {
					input.removeClass('funtipInvalidField');
					return true;
				}
			},
		
		//Check if only numbers are provided
			numeric : function(input) {
				return $.fn.funtip.validate.base(input, !isNaN(input.val()));
			},
			
		//Check if an email address is provided
			email : function(input) {
				var filter = /^([a-zA-Z0-9])(([a-zA-Z0-9])*([\._\+-])*([a-zA-Z0-9]))*@(([a-zA-Z0-9\-])+(\.))+([a-zA-Z]{2,4})+$/;
				var value = input.val();
				
				return $.fn.funtip.validate.base(input, value.search(filter) !== -1);
			},
			
		//Check for a minimium provided value
			min : function(input) {
				var regex = /min\[(.*?)\]/;
				var match = regex.exec(input.attr('class'));
				var min = match[1];
				var value = input.val();
				
				return $.fn.funtip.validate.base(input, value.length >= min);
			},
			
		//Check for a maxmium provided value
			max : function(input) {
				var regex = /max\[(.*?)\]/;
				var match = regex.exec(input.attr('class'));
				var max = match[1];
				var value = input.val();
				
				
				return $.fn.funtip.validate.base(input, value.length <= max);
			}
		};
		
	//Check to see whether the user provided a custom message, then generate one accordingly
		$.fn.funtip.message = function(message, customMessage, fallbackMessage) {
			if (customMessage == null || customMessage == undefined) {
				return fallbackMessage;
			} else {
				return customMessage;
			}
		};
		
	//The fun tip plugin default options
		$.fn.funtip.options = {
		//Global options
			borderRadius : '5px',
			parentForm : $('form'),
			autoFocusFirst : true,
				
		//Border container options
			borderDefaultColor : '#22C3EB', //Baby blue
			borderHoverColor : '#22C3EB', //Baby blue
			borderActiveColor : '#972324', //Maroon
			borderGlowHover : '#22C3EB', //Baby blue
			borderGlowActive : '#972324', //Maroon
			borderBackgroundColor : '#FFF', //White
			borderGlowSpread : '10px',
			borderStretchTime : 200,
			
		//Input field options
			inputBackgroundColor : '#FFF', //White
			inputWidth : '200px',
			inputTextColor : '#000', //Black
			
		//Tip options
			tipWidth : '200px',
			tipColorDefault : '#FFF', //White
			tipColorGood : '#F0FEE9', //Light green
			tipColorBad : '#FFCFCF', //Light red
			tipTextColor : '#000', //Black
			hideTipOnSuccess : true,
			
		//Default error messages
			standard : 'Please fill out this field',
			required : 'This field is required',
			error : 'This field is not valid',
			success : 'This field is valid'
		};
	});
})(jQuery);