/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * Data Package is an advanced Rich Internet Application User Interface (RIA UI)
 * library for reading and displaying data in real-time in a grid. It allows
 * the user to create, read, paginate, update, and delete entries in a single
 * screen, interactive interface. The following plugins are utilized to create 
 * this experience:
*/

(function($) {
	$(document).ready(function() {
	/*
	The Data Package plugin core
	---------------------------------------
	*/
	//Extend the scope of these variables
		var defaults; //The variable containing the plugin's custom configuration
		var timer; //The variable containing the setTimeout method for the updater alert messages
		
	//The Data Package class definition
		$.datapackage = function(options) {
		//Extend the option defaults to allow a more flexible plugin
			defaults = $.extend($.datapackage.defaults, options);
			
		//Run initialization scripts
			$.datapackage.init(this);
			
		//Show or hide an existing item
			$(defaults.visibilityTrigger).live('click', function() {
				$.datapackage.visibility(this);
			});
			
		//Call the add dialog
			$(defaults.addTrigger).live('click', function() {
				$.datapackage.add();
			});
			
		//Call the edit dialog
			$(defaults.editTrigger).live('click', function() {
				$.datapackage.edit(this);
			});
			
		//Delete an exisiting item
			$(defaults.deleteTrigger).live('click', function() {
				$.datapackage.deleter(this);
			});
		};
		
	/*
	The Data Package plugin configuration
	---------------------------------------
	*/
		
	//The default options for this plugin
		$.datapackage.defaults = {
		//General options
			width : $(window).width() - ($(window).width() * 0.2), //Centered at 80% width
			height : $(window).height() - ($(window).height() * 0.2), // Centered at 80% height
			
		//Data retrieval options
			tableContainer : '.content',
			getData : document.location.href,
			messageOnFail : '<p class="center">The data you have requested could not be found. Please click <a href="#">here</a> to go back.</p>',
			messageEmpty : '<p class="spacer">There aren\'t currently any items in this system. You may <a class="create" href="javascript:;">add one now</a>.</p>',
			
		//Reordering options
			reorderTrigger : '.dragger',
			reorderProcessor : document.location.href,
			reorderUpdateSuccess : 'The items have been reordered',
			reorderUpdateError : 'There was a problem when reordering the items',
			
		//Visibility options
			visibilityTrigger : '.visibilityTrigger',
			visibilityProcessor : document.location.href,
			visibleClass : 'eyeShow',
			invisibleClass : 'eyeHide',
			visibleUpdateSuccess : 'The item\'s visibility has been changed',
			visibleUpdateError : 'There was a problem when changing the item\'s visibility',
			
		//Add dialog options
			addTrigger : '.new, .create',
			addProcessor : document.location.href,
			addTitle : 'Insert New Data',
			addContent : '.addContent',
			addSuccess : 'The item has been created',
			addError : 'There was a problem when creating the item',
			
		//Edit dialog options
			editTrigger : '.edit',
			editProcessor : document.location.href,
			editTitle : 'Edit Data Entry',
			editContent : '.editContent',
			editSuccess : 'The item has been updated',
			editError : 'There was a problem when updating the item',
			
		//Delete options
			deleteTrigger : '.delete',
			deleteProcessor : document.location.href,
			deleteTitle : 'Delete Item',
			deletePrompt : '<p>You are about to delete this item. This action is permanent and cannot be undone.<br /><br />Do you wish to continue?</p>',
			deleteSuccess : 'The item has been deleted',
			deleteError : 'There was a problem when deleting the item',
			
		//Event listeners
			addDialogCreated : $.noop,
			addDialogClosed : $.noop,
			addBeforeSubmit : $.noop,
			addAfterSubmit : $.noop,
			
			editDialogCreated : $.noop,
			editDialogClosed : $.noop,
			editBeforeSubmit : $.noop,
			editAfterSubmit : $.noop
		};
		
	/*
	The Data Package plugin event handlers
	---------------------------------------
	*/
		
	//On page load events
		$.datapackage.init = function() {
		//Add a hash, if none is present, to prepare for hash-based navigation
			if (window.location.hash == '') {
				window.location.hash = '#/';
			}
			
		//Obtain a reference to the object containing the table
			var object = $(defaults.tableContainer);
			
		//Request the data via AJAX
			$.ajax({
				url : defaults.getData,
				type : 'POST',
				
			//The parameters the server is expecting
				data : {
					'action' : 'tableFetch', //The action which is being performed
					'URL' : 'nice'
				},
				
			//Once the data has been retrieved, we can decide whether or not it was successful
				success : function(data) {
					if (data != 'failure') {
					//Add the fetched table to the document
						object.removeClass('spacer').html(data);
						
					//Make each row sortable
						$('.dataTable tbody').sortable({
							axis : 'y',
							containment : 'document',
							handle : defaults.reorderTrigger,
							
						//Prevents the table cells from squashing together
							helper : function(event, ui) {
								 ui.children().each(function() {
									 $(this).width($(this).width());
								 });
								 
								 return ui;
							},
							
						//Update the database, and reset the striping
							update : function(event, ui) {
								$.datapackage.reorder(event, ui);
							}
						});
					} else {
						object.empty().html(defaults.messageOnFail);
					}
				}
			});
		};
		
	//Add data events
		$.datapackage.add = function() {
		//Grab the content from the targeted element and place it inside of a dialog
			$('<div id="dataPackageDialog" />').html($(defaults.addContent).html()).dialog({
				width : defaults.width,
				height : defaults.height,
				title : defaults.addTitle,
				modal : true,
				buttons : {
					'Submit' : function() {
						$.datapackage.addSubmit();
					},
					'Reset' : function() {
						$('#dataPackageDialog').children('form').trigger('reset');
					},
					'Cancel' : function() {
						$(this).dialog('close').remove();
					}
				},
				create : function() {
				//Fire the dialog creation complete event listener
					defaults.addDialogCreated();
				},
				close : function() {
				//Fire the after dialog out complete event listener
					defaults.addDialogClosed();
					
				//Remove the dialog to avoid future conflicts
					$(this).remove();
				}
			});
		};
		
	//Edit data events
		$.datapackage.edit = function(object) {
		//Grab the content from the targeted element and place it inside of a dialog
			$('<div id="dataPackageDialog" />').html($(defaults.editContent).html()).dialog({
				width : defaults.width,
				height : defaults.height,
				title : defaults.editTitle,
				modal : true,
				buttons : {
					'Submit' : function() {
						$.datapackage.editSubmit(object);
					},
					'Reset' : function() {
						$('#dataPackageDialog').children('form').trigger('reset');
					},
					'Cancel' : function() {
						$(this).dialog('close').remove();
					}
				},
				create : function() {
				//Fire the dialog creation complete event listener
					defaults.editDialogCreated(object);
				},
				close : function() {
				//Fire the after dialog out complete event listener
					defaults.editDialogClosed(object);
					
				//Remove the dialog to avoid future conflicts
					$(this).remove();
				}
			});
		};
		
	//Delete data events
		$.datapackage.deleter = function(object) {
		//Display a dialog containing a confirmation prompt before any action takes place
			$('<div id="dataPackageDeleteConfirm" />').html(defaults.deletePrompt).dialog({
				width : '500',
				height: '250',
				title : defaults.deleteTitle,
				modal : true,
				buttons : {
					'Yes' : function() {
						$.datapackage.deleterSubmit(object);
					},
					'No' : function() {
						$(this).dialog('close').remove();
					}
				}
			});
		};
		
	/*
	The Data Package plugin processors
	---------------------------------------
	*/
	
	//Reorder items 
		$.datapackage.reorder = function(event, ui) {
		//Send the request to the server
			$.ajax({
				url : defaults.reorderProcessor,
				type : 'POST',
			
			//The ordering data to send to the server
				data : {
					'action' : 'reorder', //The action which is being performed
					'id' : ui.item.attr('id'),
					'currentPosition' : ui.item.attr('name'),
					'newPosition' : ui.item.index() + 1
				},
				success : function(data) {
				//Check to see if the reordering was a success
					if (data == 'success') {
					//Display a success message
						$.datapackage.success(defaults.reorderUpdateSuccess);
						
					//Update the current position for each of the elements
						var position = 1;
						
						$('.dataTable tbody tr').each(function() {
							$(this).attr('name', position);
							position ++;
						});
				//Show a dialog with an error message from the server on error
					} else {
						$.datapackage.error('Reordering Error', data, defaults.reorderUpdateError);
					}
				}
			});
			
		//Update the striping
			$('.dataTable tbody tr:even').removeClass('even odd').addClass('odd');
			$('.dataTable tbody tr:odd').removeClass('even odd').addClass('even');
		};
		
	//Toggle items visibility 
		$.datapackage.visibility = function(object) {
		//Transform the object into a jQuery object
			object = $(object);
			
		//Calculate whether this item will be hidden or shown
			var visibility = object.hasClass(defaults.visibleClass) ? '0' : '1';
			
		//Update the class
			var visibilityClass = object.hasClass(defaults.visibleClass) ? defaults.invisibleClass : defaults.visibleClass;
			
			object.removeClass(defaults.visibleClass + ' ' + defaults.invisibleClass).addClass(visibilityClass);
			
		//Send the request to the server
			$.ajax({
				url : defaults.visibilityProcessor,
				type : 'POST',
				data : {
					'action' : 'visibility', //The action which is being performed
					'id' : object.parent().parent().attr('id'), //Grab the ID from the parent row
					'visibility' : visibility
				},
				success : function(data) {
				//Check to see if toggling the visibility was a success
					if (data == 'success') {
						$.datapackage.success(defaults.visibleUpdateSuccess);
				//Show a dialog with an error message from the server on error
					} else {
						$.datapackage.error('Visibility Error', data, defaults.visibleUpdateError);
					}
				}
			});
		};
		
	//Add an item
		$.datapackage.addSubmit = function() {
		//Fire the before add submit event listener
			defaults.addBeforeSubmit();
			
		//Grab the necessary data
			var dialog = $('#dataPackageDialog');
			var form = dialog.children('form').serialize();
			
		//Submit this from via AJAX
			$.ajax({
				url : defaults.addProcessor,
				type : 'POST',
				data : form,
				success : function(data) {
				//Check to see if adding the item was a success, SERVER WILL RETURN AN ID IN THIS CASE!!!
					if (!isNaN(data)) {
						$.datapackage.success(defaults.addSuccess);
						dialog.dialog('close').remove();
						
					//Fire the after add submit event listener
						defaults.addAfterSubmit(data);
				//Show a dialog with an error message on error
					} else {
						$.datapackage.error('Form Submission Error', '<p>The form you have attempted to submit contained an error. This error could be any number of things: a blank field, an invalid entry (such as text being entered where numbers should have), an entry which was too long, etc.... Please double-check your input before resubmitting.</p>', defaults.addError);
					}
				}
			});
		};
		
	//Edit an item
		$.datapackage.editSubmit = function(object) {
		//Fire the before edit submit event listener
			defaults.editBeforeSubmit(object);
			
		//Grab the necessary data
			var dialog = $('#dataPackageDialog');
			
		//Quickly inject an ID input field into the form to send to the server
			$('<input type="hidden" name=\"id\" />').appendTo('#dataPackageDialog form').attr('value', $(object).parent().parent().attr('id'));
			
		//Now grab the data from the form
			var form = dialog.children('form').serialize();
			
		//Submit this from via AJAX
			$.ajax({
				url : defaults.editProcessor,
				type : 'POST',
				data : form,
				success : function(data) {
				//Check to see if editing the item was success
					if (data == 'success') {
						$.datapackage.success(defaults.editSuccess);
						dialog.dialog('close').remove();
						
					//Fire the after edit submit event listener
						defaults.editAfterSubmit(object);
				//Show a dialog with an error message on error
					} else {
						$.datapackage.error('Form Submission Error', '<p>The form you have attempted to submit contained an error. This error could be any number of things: a blank field, an invalid entry (such as text being entered where numbers should have), an entry which was too long, etc.... Please double-check your input before resubmitting.</p>', defaults.editError);
					}
				}
			});
		};
		
	//Delete an item
		$.datapackage.deleterSubmit = function(object) {
		//Transform the object into a jQuery object
			object = $(object);
			
		//Send the request to the server
			$.ajax({
				url : defaults.deleteProcessor,
				type : 'POST',
				data : {
					'action' : 'delete', //The action which is being performed
					'id' : object.parent().parent().attr('id') //Grab the ID from the parent row
				},
				success : function(data) {
				//Check to see if deleting the item was success
					if (data == 'success') {
						$.datapackage.success(defaults.deleteSuccess);
						$('#dataPackageDeleteConfirm').dialog('close').remove();
						
					//An animation which will fade out the deleted row, then delete it
						object.parent().parent().children().fadeTo(1000, 0, function() {
						//Delete the old row
							$(this).parent().remove();
							 
						//Check to see if any more items in the list exist
							if ($('.dataTable tbody tr').length > 0) {
							//Update the striping
								$('.dataTable tbody tr:even').removeClass('even odd').addClass('odd');
								$('.dataTable tbody tr:odd').removeClass('even odd').addClass('even');
							} else {
							//Otherwise remove the table and display an empty message
								$(defaults.tableContainer).empty().html(defaults.messageEmpty);
							}
						}); 
						
				//Show a dialog with an error message on error
					} else {
						$.datapackage.error('Deletion Error', data, defaults.deleteError);
					}
				}
			});
		};
		
	/*
	The Data Package message handlers
	---------------------------------------
	*/
			
	//Display a success message
		$.datapackage.success = function(message) {
		//Remove any old messages and timers
			if ($('div.alertFix').length > 0) {
				$('div.alertFix').remove();
				clearTimeout(timer);
			}
			
		//Create the alert
			$('<div />').appendTo(document.body).addClass('alertFix').append('<div class="success">' + message + '</div>');
			
		//Fade out and remove the alert after a given amount of time
			timer = setTimeout(function() {
				$('div.alertFix').fadeOut(3000, function() {
					$(this).remove();
				});
			}, 3000);
		};
		
	//Display an error message
		$.datapackage.error = function(dialogTitle, dialogContent, windowMessage) {
		//Remove any old messages and timers
			if ($('div.alertFix').length > 0) {
				$('div.alertFix').remove();
				clearTimeout(timer);
			}
			
		//Create the alert
			$('<div />').appendTo(document.body).addClass('alertFix').append('<div class="error">' + windowMessage + '</div>');
			
		//Create the modal
			$('<div />').html(dialogContent).dialog({
				width : '500',
				height: '175',
				title : dialogTitle,
				modal : true,
				closeOnEscape: false, //Don't let the user close this dialog
				open: function(event, ui) {
					$('.ui-dialog-titlebar-close').hide(); //Don't let the user close this dialog
				}
			});
			
		//Fade out and remove the alert after a given amount of time
			timer = setTimeout(function() {
				$('div.alertFix').fadeOut(3000, function() {
					$(this).remove();
				});
			}, 3000);
		};
	
	/*
	The Data Package misc enhancements
	---------------------------------------
	*/
		
	//Expand a hidden section within a form
		$('.expand').live('click', function() {
			$(this).removeClass('expand').addClass('fold').parent().next().removeClass('hide');
		});
		
	//Fold a visible section within a form
		$('.fold').live('click', function() {
			$(this).removeClass('fold').addClass('expand').parent().next().addClass('hide');
		});
		
	/*
	Class instantiation
	---------------------------------------
	*/
		
	//Global variables used during the instantiation process
		var helper;
		
	//A custom method to generate the URL of a given page
		$.datapackage.URLGen = function() {
		//Get the root URL of the site with respect to the plugin's root
			var rootArray = document.location.href.split('cms');
			var root = rootArray[0];
			
		//Get the parent directory structure of the page with repsect to the hash
			var parentArray = document.location.href.split('#/');
			var parent = parentArray[1];
			
		//Place the generated URL beside the "Page URL" field
			$('span.parentURL', '#dataPackageDialog').empty().text(root + parent);
		};
		
	//A custom method to listen for input from the URL input field, convert them into a valid SEO URL, and place them inside of the URL field
		$.datapackage.URLClean = function() {
			var URL = $('.URLInput', '#dataPackageDialog');
			var alert = $('.alertInfo', '#dataPackageDialog');
			var alertTimer;
			
			URL.keyup(function() {
				var input = URL.val();
				input = input.replace(/[^A-Za-z0-9\-_\s]/g, '');
				
			//Alert the user if any invalid characters are stripped
				if (URL.val() !== input) {
					alert.text('Valid characters are letters, numbers, an underscore, a dash, and a space');
					clearTimeout(alertTimer);
					
					alertTimer = setTimeout(function() {
						alert.empty();
					}, 5000);
				}
				
			//Check for any "_" and spaces, convert them into a "-", and let the user know about this conversion
				if (/[_\s]/g.test(input)) {
					input = input.replace(/[_\s]/g, '-');
					
					alert.text('Underscores and spaces are converted to a dash');
					clearTimeout(alertTimer);
					
					alertTimer = setTimeout(function() {
						alert.empty();
					}, 5000);
				}
				
			//Check to see if all of the text is in lower case
				if (input !== input.toLowerCase()) {
					input = input.toLowerCase();
					
					alert.text('Text is converted to lowercase');
					clearTimeout(alertTimer);
					
					alertTimer = setTimeout(function() {
						alert.empty();
					}, 5000);
				}
				
			//Assign the URL field the new value
				URL.val(input);
			});
		};
		
	//A custom method to generate an SEO URL on submit
		$.datapackage.URLSubmit = function() {
		//If the URL field is empty, then use the title for the URL, and clean it up
			if ($('.URLInput', '#dataPackageDialog').val() == '') {
				var input = $('.titleInput', '#dataPackageDialog').val();
				input = input.replace(/[^A-Za-z0-9\-_\s]/g, '');
					
			//Check for any "_" and spaces, convert them into a "-", and let the user know about this conversion
				if (/[_\s]/g.test(input)) {
					input = input.replace(/[_\s]/g, '-');
				}
					
			//Assign the URL field the new value
				$('.URLInput', '#dataPackageDialog').val(input.toLowerCase());
			}
		};
		
	//Instantiate the Data Package plugin
		$.datapackage({
		/*
		Add content section
		---------------------------------------
		*/
			addTitle : 'Create New Page',
			addDialogCreated : function() {
			//Generate the URL of a given page
				$.datapackage.URLGen();
				
			//Listen for input from the URL input field, convert them into a valid SEO URL, and place them inside of the URL field 
				$.datapackage.URLClean();
				
			//Convert the hidden field's value to "add", so the server will know that the content it will recieve is for adding purposes
				$('.typeInput', '#dataPackageDialog').val('add');
			},
			addBeforeSubmit : function() {
			//Generate an SEO URL on submit
				$.datapackage.URLSubmit();
				
			//Before the data is transmitted to the server, hurry and grab the values of each of the required inputs
				helper = new Array();
				helper.push($('.titleInput', '#dataPackageDialog').val()); //Grab the title input field
				helper.push($('.bodyInput', '#dataPackageDialog').val()); //Grab the body text area
				helper.push($('.URLInput', '#dataPackageDialog').val()); //Grab the URL input field
			},
			addAfterSubmit : function(data) {
			//Check to see if any pages currently exist
				if ($('.content table tbody tr').length > 0)  {
				//Grab the class of the previous row in the table to assign to the one being created
					var newClass = $('.content table tbody tr:last').hasClass('even') ? 'odd' : 'even';
					
				//Grab the index of the last row, and add "1" for the new row
					var position = $('.content table tbody tr').length + 1;
					
				//Create the row after the form has been successfully processed
					$('<tr />')
					.attr('id', data) //The row ID from the server
					.attr('name', position) //The position of this item
					.addClass('newHighlight') //Highlight the background of the newly created item
					.appendTo('.content table tbody') //Add this row to the table
					.append('<td class="center width50"><a class="dragger"></a><a class="visibilityTrigger eyeShow\"></a></td>')
					.append('<td class="center width150">' + helper['0'] + '</td>')  //"helper['0']" is the title input field
					.append('<td class="fixMe"><div class="clipContainer">' + helper['1'] + '</div><div class="hide URLContainer">' + helper['2'] + '</div><div class="hide contentContainer>' + helper['1'] + '</div></td>') //"helper['1']" is the body text area, and "helper['2']" is the URL input field
					.append('<td class="center width75"><a class="edit"></a><a class="delete"></a></td>');
					
				//Strip all of the HTML out of the cell with a class of "fixMe", by getting then resetting just it's text (no HTML) contents
					$('.fixMe:last').children('.clipContainer').text($('.fixMe:last').children('.clipContainer').text());
					
				//Scroll to the newly inserted row
					$('html, body').animate({
						'scrollTop' : $('.content table tbody tr:last').offset().top
					}, 1000, function() {
					//Animate the zebra striping overtop of the highlighted row
						$('.content table tbody tr:last').addClass(newClass, 3000, function() {
						//Remove the "newHighlight" class
							$(this).removeClass('newHighlight');
						});
					});
			//Otherwise request them new from the server
				} else {
				//Show a preloader message
					$('.content').addClass('spacer').html('<h1 class="loader">Loading data...</h1>');
					
				//Request the data from the server
					$.ajax({
						url : document.location.href,
						type : 'POST',
						data : {
							'action' : 'tableFetch' //The action which is being performed
						},
						success : function(data) {
						//Populate the container with the data
							$('.content').removeClass('spacer').html(data);
							
						//Make each row sortable
							$('.dataTable tbody').sortable({
								axis : 'y',
								containment : 'document',
								handle : defaults.reorderTrigger,
								
							//Prevents the table cells from squashing together
								helper : function(event, ui) {
									 ui.children().each(function() {
										 $(this).width($(this).width());
									 });
									 
									 return ui;
								},
								
							//Update the database, and reset the striping
								update : function(event, ui) {
									$.datapackage.reorder(event, ui);
								}
							});
						}
					});
				}
			},
			
		/*
		Edit content section
		---------------------------------------
		*/
			editTitle : 'Edit Page',
			editDialogCreated : function(data) {
			//Generate the URL of a given page
				$.datapackage.URLGen();
				
			//Listen for input from the URL input field, convert them into a valid SEO URL, and place them inside of the URL field 
				$.datapackage.URLClean();
				
			//Convert the hidden field's value to "edit", so the server will know that the content it will recieve is for editing purposes
				$(':hidden', '#dataPackageDialog').val('edit');
				
			//Obtain a reference to the edit link which was clicked
				var data = $(data);
				
			//Fill up all of the input fields, all of which can be grabbed from different sections of the same row
				$('.titleInput', '#dataPackageDialog').val(data.parent().parent().children('td:eq(1)').text());
				$('.bodyInput', '#dataPackageDialog').val(data.parent().parent().children('td:eq(2)').children('.contentContainer').html());
				$('.URLInput', '#dataPackageDialog').val(data.parent().parent().children('td:eq(2)').children('.URLContainer').text());
			},
			editBeforeSubmit : function(data) {
			//Generate an SEO URL on submit
				$.datapackage.URLSubmit();
				
			//Before the data is transmitted to the server, hurry and grab the values of each of the required inputs
				helper = new Array();
				helper.push($('.titleInput', '#dataPackageDialog').val()); //Grab the title input field
				helper.push($('.bodyInput', '#dataPackageDialog').val()); //Grab the body text area
				helper.push($('.URLInput', '#dataPackageDialog').val()); //Grab the URL input field
			},
			editAfterSubmit : function(data) {
			//Obtain a reference to the edit link which was clicked
				var data = $(data);
				
			//Grab the class of the modified row in the table
				var oldClass = data.parent().parent().hasClass('even') ? 'even' : 'odd';
				
			//Highlight the background of the edited item
				data.parent().parent().removeAttr('class').addClass('editHighlight');
				
			//Edit the contents of the updated row
				data.parent().parent().children('td:eq(1)').text(helper['0']);
				data.parent().parent().children('td:eq(2)').children('.clipContainer').html(helper['1']);
				data.parent().parent().children('td:eq(2)').children('.contentContainer').html(helper['1']);
				data.parent().parent().children('td:eq(2)').children('.URLContainer').text(helper['2']);
				
			//Strip all of the HTML out of the content snapshot container, by getting then resetting just it's text (no HTML) contents
				data.parent().parent().children('td:eq(2)').children('.clipContainer').text(data.parent().parent().children('td:eq(2)').children('.clipContainer').text());
				
			//Scroll to the edited row
				$('html, body').animate({
					'scrollTop' : data.parent().parent().offset().top
				}, 1000, function() {
				//Animate the zebra striping overtop of the highlighted row
					data.parent().parent().addClass(oldClass, 3000, function() {
					//Remove the "editHighlight" class
						$(this).removeClass('editHighlight');
					});
				});
			}
		});
	});
})(jQuery);