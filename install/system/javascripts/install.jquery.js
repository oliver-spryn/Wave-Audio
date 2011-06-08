/*
 * This JavaScript file contains references which will be used throughout the system installation
 * process, mainly to speed up performance, and introduce a more interactive experience.
*/

(function($) {
	$(document).ready(function() {
	//Test a connection to the database
		$('#dbTest').live('click', function() {
			var tester = $(this);
			
			tester.button('disable').button('option', 'label', 'Testing...');
			
			$.ajax({
				url : document.location.href,
				type : 'POST',
				data : {
					'test' : 'dbConnection',
					'dbHost' : $('.dbHost').val(),
					'dbPort' : $('.dbPort').val(),
					'dbUsername' : $('.dbUsername').val(),
					'dbPassword' : $('.dbPassword').val(),
					'dbName' : $('.dbName').val()
				},
				success : function(data) {
					if (data == 'success') {
						tester.button('option', 'label', 'Connection was successful!');
						
						$(':text, :password').attr({
							readonly : 'readonly'
						});
						$('#dbContinue').button('enable');
					} else {
						tester.button('option', 'label', 'Test connection').button('enable');
						
						$('<div></div>')
						.html('<p>The system was unable to make a connection and log into the database. Please make sure that the database connection, credentials, and name are correct. The database connection values supplied by default are usually correct, however, there are cases where they will need to be changed. If you are still having difficulties, contact your hosting provider for assistance.</p>')
						.analog({
							width : '650',
							height : '300',
							title : 'Database Connection Error',
							buttons : {
								'Ok' : function() {
									$(this).dialog('close');
								}
							}
						});
					}
				}
			});
		});
	});
})(jQuery);