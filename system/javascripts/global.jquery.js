/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
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
	});
})(jQuery);