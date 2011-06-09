/*
 * "Fun tip" is a simple plugin which adds fancy CSS3 styles to plain text, password, and textarea inputs.
 * In addition to the styles, animated tips can be displayed on the right side of the input, which will 
 * give the web designer an easy to add helpful hints to add front-end validation.
*/

(function($) {
	$(document).ready(function() {
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
				var message = $.parseJSON(input.attr('id')) ? $.parseJSON(input.attr('id')) : '';
				
			//Add the respective styles to each element
				input.css(inputStyle);
				container.css(glowBox);
				tipBox.css(tip);
				
			/*
			Input field event listeners
			--------------------------------------------- 
			*/	
				
			//Listen for mouse and click events to change the container UI
				input.focus(function() {										
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
				}).blur(function() {
				/*
				Input field event listeners
				--------------------------------------------- 
				*/
										
				//Hiding the tip will depend on whether or not the field passes validation
					var showMessage;
					
				//Check if this field is required
					if (input.hasClass('required')) {
						$.fn.funtip.validate.required(input);
						showMessage = $.fn.funtip.message(message, message.required, defaults.required);
					}
					
				//Check if this field is numeric
					if (!input.hasClass('funtipInvalidField') && input.hasClass('numeric')) {
						$.fn.funtip.validate.numeric(input);
						showMessage = $.fn.funtip.message(message, message.error, defaults.error);
					}
					
				//Adjust the elements as required
					if (!input.hasClass('funtipInvalidField')) {						
					//Adjust the input field, retract the container, and hide the tip, if told to do so
						if (input.hasClass('noHide') || !defaults.hideTipOnSuccess) {
							showMessage = $.fn.funtip.message(message, message.success, defaults.success);
							
							tipBox.text(showMessage);
							container.removeAttr('style').css(glowBox).css({
								'background-color' : defaults.tipColorGood,
								'width' : width
							});
						} else {
							input.css({
								'border-top-right-radius' : defaults.borderRadius,
								'border-bottom-right-radius' : defaults.borderRadius,
								'border-right' : '0px solid #FFFFFF'
							});
							
							container.removeAttr('style').css(glowBox).css({
								'width' : width
							}).animate({
								'width' : defaults.inputWidth
							}, defaults.borderStretchTime);
							
							tipBox.text('').fadeOut(defaults.borderStretchTime).removeAttr('style');
						}
					} else {
					//Remove the container focus color, but maintain its width
						container.removeAttr('style').css(glowBox).css({
							'width' : width
						});
						
					//Highlight the tip and display the required message
						tipBox.text(showMessage);
						container.css({
							'background-color' : defaults.tipColorBad
						});
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
				
			//Find all empty required fields
				fields.each(function() {
					var field = $(this);
					
					if (field.hasClass('required') && (field.hasClass('funtipInvalidField') || field.val() == '')) {
						canSubmit = false;
						return false;
					}
				});
				
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
				if ($.fn.funtip.validate.required(input) && !isNaN(input.val())) {
					input.removeClass('funtipInvalidField');
					return true;
				} else {
					input.addClass('funtipInvalidField');
					return false;
				}
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