/**
 * Epoch Cloud Management Platform
 * 
 * LICENSE
 * 
 * By viewing, using, or actively developing this application in any way, you are
 * henceforth bound the license agreement, and all of its changes, set forth by
 * ForwardFour Innovations. The license can be found, in its entirety, at this 
 * address: http://forwardfour.com/license.
 * 
 * @category   Core
 * @package    templates
 * @copyright  Copyright (c) 2011 and Onwards, ForwardFour Innovations
 * @license    http://forwardfour.com/license    [Proprietary/Closed Source] 
 * 
*/

/*
 * This JavaScript file contains declarations which will be used system-wide, largely to manipulate the 
 * appearance of a user's experience, and to introduce fallback support for non-HTML5 compliant browsers. 
 */

(function($) {
	/*
	 * Yes, this needs to run BEFORE the DOM is ready.
	 * 
	 * Internet Explorer doesn't render any element it doesn't recognise. (Why???)
	 * Fortunately, if these elements are declared via JavaScript, IE will render
	 * and style them. CSS will take care of the rest to ensure these "unknown"
	 * elements will render properly on older browsers.
	 * 
	 * Thanks to: http://html5doctor.com/how-to-get-html5-working-in-ie-and-firefox-2/
	 */
	
	var HTML5 = new Array('abbr', 'article', 'aside', 'audio', 'bb', 'canvas', 'datagrid', 'datalist', 'details', 'dialog', 'eventsource', 'figure', 'footer', 'header', 'hgroup', 'mark', 'menu', 'meter', 'nav', 'output', 'progress', 'section', 'time', 'video');
	
	$.each(HTML5, function(index, value) {
		$('<' + value + ' />').remove();
	});
	
	$(document).ready(function() {
	/*
	User-interface
	---------------------------------------
	*/	
	
	//Turn all of the HTML button elements into jQuery UI styled buttons
		$(':button, :reset, :submit').button();
		
	//Listen for clicks on the navigation bar's **left-side** links, to display a pop-down menu
		$('.pluginBarLeft li span').click(function() {
		//Hide all other menus
			$('.pluginBar li div:visible').parent().removeAttr('style').removeClass('lockLI')
			.children('div').hide().removeClass('lockContainer');
			
		//Obtain a reference to the trigger and it's content container
			var trigger = $(this);
			var container = trigger.siblings('div');
			
		//Give the list-item a unique class, to show it is open
			trigger.parent().addClass('pluginBarIsOpen');
			trigger.addClass('pluginBarIsOpen');
			
		//Apply the open styles to the menu
			trigger.parent().addClass('lockLI');
			container.addClass('lockContainer');
			
		//Cover-up the default CSS hover effect
			trigger.parent().css('background-color', container.css('background-color'));
			
		//Modify the CSS to best display the pop-down on the right side of the screen, then show it
			var offset = trigger.parent().offset().left;
			
			container.css({
				'left' : offset,
				'min-width' : trigger.parent().width() //Go no smaller than the triggering list-item's width
			}).show();
		});
		
	//Listen for clicks on the navigation bar's **right-side** links, to display a pop-down menu
		$('.pluginBarRight li span').click(function() {
		//Hide all other menus
			$('.pluginBar li div:visible').parent().removeAttr('style').removeClass('lockLI')
			.children('div').hide().removeClass('lockContainer');
			
		//Obtain a reference to the trigger and it's content container
			var trigger = $(this);
			var container = trigger.siblings('div');
			
		//Give the list-item a unique class, to show it is open
			trigger.parent().addClass('pluginBarIsOpen');
			trigger.addClass('pluginBarIsOpen');
			
		//Apply the open styles to the menu
			trigger.parent().addClass('lockLI');
			container.addClass('lockContainer');
			
		//Cover-up the default CSS hover effect
			trigger.parent().css('background-color', container.css('background-color'));
			
		/*
		 * Modify the CSS to best display the pop-down on the right side of the screen, then show it
		 * 
		 * Here are the steps taken to mark the location of the pop-down menu:
		 *  [1] Get the width of the visible window
		 *  [2] Subtract the left offset of the triggering list-item
		 *  [3] Subtract the width of the triggering list-item
		 *  [4] Add 2px to compensate for the 1px border added to both sides of the list-item trigger
		 */
			var offset = $(window).width() - trigger.parent().offset().left - trigger.parent().width() - 2;
			
			container.css({
				'right' : offset,
				'min-width' : trigger.parent().width() //Go no smaller than the triggering list-item's width
			}).show();
		});
		
	//Listen for clicks to hide navigation bar menus
		$(document).click(function(e) {
			var hasAncestor = false;
			
			$(e.target).parents().each(function() {
				if ($(this).hasClass('pluginBarIsOpen')) {
					hasAncestor = true;
					return false;
				}
			});
			
			if (!$(e.target).parent().hasClass('pluginBarIsOpen') && !hasAncestor) {
				$('.pluginBar li div:visible').parent().removeAttr('style').removeClass('lockLI pluginBarIsOpen')
				.children('span').removeClass('pluginBarIsOpen')
				.siblings('div').hide().removeClass('lockContainer');
			}
		});
	});
})(jQuery);