/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * "Analog" is a simple animated dialog box. "Analog" comes from the word "animated" and "dialog".
 * This plugin utilizes the jQuery UI dialog, however, it adds a fly effect upon entry and exit.
*/

(function($) {
	$(document).ready(function() {
	//The analog plugin itself
		$.fn.analog = function(options) {
		//Add flexibility to this plugin by extending the default options
			var defaults = $.extend($.fn.analog.options, options);
			
		//Capture the element to transform into a dialog
			var dialog = this;
			
		//Manipulate the animation settings based on user input
			if (defaults.overlayAnimated) {
				var fadeTime = defaults.overlayFadeTime;
			} else {
				var fadeTime = 0;
			}
			
		//Calculate the dialog's distance from the top of the window based on the supplied values
			var distance = $(window).height() - (($(window).height() / 2) + (defaults.height / 2));
			
		//Manually build the overlay, great for visual animation			
			$('<div class="ui-widget-overlay"></div>').appendTo(document.body).hide().width($(document).width()).height($(document).height()).fadeTo(fadeTime, defaults.overlayOpacity, function() {				
			//Run the "overlayInComplete" event
				defaults.overlayInComplete(this);
				
			//Build the dialog
				$(dialog).dialog({
				//Dialog visual settings
					width : defaults.width,
					height : defaults.height,
					title : defaults.title,
					buttons : defaults.buttons,
					resizable : false,
					position : 'top',
					
				//Run the "dialogCreateComplete" event
					create : function() {
						defaults.dialogCreateComplete(this);
					},
					
				//Move the dialog out of the screen, and fade the overlay away when the dialog close method is called
					beforeClose : function() {
					//Move the dialog
						$(this).dialog('widget').animate({
							'top' : $(document).height() + 50
						}, defaults.dialogSlideTime, function() {
						//Run the "dialogOutComplete" event 
							defaults.dialogOutComplete(this);
							
						//Fade out the overlay
							$('.ui-widget-overlay').fadeTo(fadeTime, 0, function() {
								$(this).remove();
								
							//Run the "overlayOutComplete" event 
								defaults.overlayOutComplete(this);
							});
						});	
						
						return false;
					}
			//Prepare the dialog for animation
				}).dialog('widget').css({
					position : 'fixed',
					top : $(document).height() + 50
			//Slide the dialog into view
				}).animate({
					top : distance
				}, defaults.dialogSlideTime, function() {
				//Run the "dialogInComplete" event
					defaults.dialogInComplete(this);
				});
			});
		};
		
	//The analog plugin default options
		$.fn.analog.options = {
		//Overlay options
			overlayAnimated : true,
			overlayFadeTime : 1000,
			overlayOpacity : .5,
			
		//Dialog options
			title : '',
			width : $(document).width() - ($(document).width() * 0.2), //Centered at 80% width
			height : $(document).height() - ($(document).height() * 0.2), // Centered at 80% height
			buttons : {},
			dialogSlideTime : 1000,
			
		//Events
			overlayInComplete : $.noop,
			overlayOutComplete : $.noop,
			dialogCreateComplete : $.noop,
			dialogInComplete : $.noop,
			dialogOutComplete : $.noop
		};
	});
})(jQuery);