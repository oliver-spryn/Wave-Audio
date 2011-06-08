/*
 * This JavaScript file contains declarations which will be used system-wide, largely to manipulate the 
 * appearance of a user's experience.
*/

(function($) {
	$(document).ready(function() {
	//Turn all of the HTML button elements into jQuery UI styled buttons
		$(':button, :reset, :submit').button();
	});
})(jQuery);