<?php
//Include the system core
	require_once("../server/index.php");

//Output this file as JavaScript
	FileMisc::imitate("js");
?>
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

//This file has been modified to allow the use of validation. Search for "Developer Enhancement" to see all changes.

if(jQuery)(
	function(jQuery){
		jQuery.extend(jQuery.fn,{
			uploadify:function(options) {
				jQuery(this).each(function(){
					var settings = jQuery.extend({
					id                : jQuery(this).attr('id'), // The ID of the object being Uploadified
					expressInstall    : null, // The path to the express install swf file
					folder            : '', // The path to the upload folder
					scriptAccess      : 'sameDomain', // Set to "always" to allow script access across domains
					method            : 'POST', // The method for sending variables to the backend upload script
					queueSizeLimit    : 999, // The maximum size of the file queue
					simUploadLimit    : 1, // The number of simultaneous uploads allowed
					queueID           : false, // The optional ID of the queue container
					displayData       : 'percentage', // Set to "speed" to show the upload speed in the default queue item
					onInit            : function() {}, // Function to run when uploadify is initialized
					onSelect          : function() {}, // Function to run when a file is selected
					onSelectOnce      : function() {}, // Function to run once when files are added to the queue
					onQueueFull       : function() {}, // Function to run when the queue reaches capacity
					onCheck           : function() {}, // Function to run when script checks for duplicate files on the server
					onCancel          : function() {}, // Function to run when an item is cleared from the queue
					onClearQueue      : function() {}, // Function to run when the queue is manually cleared
					onError           : function() {}, // Function to run when an upload item returns an error
					onProgress        : function() {}, // Function to run each time the upload progress is updated
					onComplete        : function() {}, // Function to run when an upload is completed
					onAllComplete     : function() {}, // Function to run when all uploads are completed
					
					form              : jQuery(this).parents('form'), // The form containing the uploader, part of the validation
					fileDataName      : jQuery(this).attr('name'), // The name of the file collection object in the backend upload script
					script            : document.location.href, // The path to the uploadify backend upload script
					wmode             : 'transparent', // The wmode of the flash file
					rollover          : true, //Whether or not to display a rollover and active image state. Use with 'buttonImg'.
					auto              : true, //Whether or not to automatically upload the file when one is added to the queue
					removeCompleted   : true, // Set to true if you want the queue items to be removed when a file is done uploading
					height            : 41, // The height of the flash button
					width             : 134, // The width of the flash button
					uploader          : '<?php echo STRIPPED_ROOT; ?>system/flash/uploadify.swf', // The path to the uploadify swf file
					cancelImg         : '<?php echo STRIPPED_ROOT; ?>system/images/common/cancel.png', // The path to the cancel image for the default file queue item container
					buttonImg         : '<?php echo STRIPPED_ROOT; ?>system/images/common/uploadify_sprite.png', //The pat to the uploader background image
					scriptData        : {
						'sessionID' : '<?php echo session_id(); ?>'
					},
					
					showLinkOnComplete: true, //Developer Enhancement: Whether or not to provide the link to the file on upload complete
					required          : true, //Developer Enhancement: Whether or not this field is required
					onRequiredFailed  : function() {}, //Developer Enhancement: Function to run when validation fails
					onRequiredSuccess : function() {} //Developer Enhancement: Function to run when validation passes
				}, options);
			//Developer Enhancement, which will privately track whether or not a file is in queue
				var queued = 0;
				
			//Developer Enhancement, which will privately track whether or not a queued file has been uploaded
				var uploaded = 0;
				
			//Developer Enhancement, to obtain a reference to uploader
				var uploader = jQuery(this);
					
				jQuery(this).data('settings',settings);
				var pagePath = location.pathname;
				pagePath = pagePath.split('/');
				pagePath.pop();
				pagePath = pagePath.join('/') + '/';
				var data = {};
				data.uploadifyID = settings.id;
				data.pagepath = pagePath;
				if (settings.buttonImg) data.buttonImg = escape(settings.buttonImg);
				if (settings.buttonText) data.buttonText = escape(settings.buttonText);
				if (settings.rollover) data.rollover = true;
				data.script = settings.script;
				data.folder = escape(settings.folder);
				if (settings.scriptData) {
					var scriptDataString = '';
					for (var name in settings.scriptData) {
						scriptDataString += '&' + name + '=' + settings.scriptData[name];
					}
					data.scriptData = escape(scriptDataString.substr(1));
				}
				data.width          = settings.width;
				data.height         = settings.height;
				data.wmode          = settings.wmode;
				data.method         = settings.method;
				data.queueSizeLimit = settings.queueSizeLimit;
				data.simUploadLimit = settings.simUploadLimit;
				if (settings.hideButton)   data.hideButton   = true;
				if (settings.fileDesc)     data.fileDesc     = settings.fileDesc;
				if (settings.fileExt)      data.fileExt      = settings.fileExt;
				if (settings.multi)        data.multi        = true;
				if (settings.auto)         data.auto         = true;
				if (settings.sizeLimit)    data.sizeLimit    = settings.sizeLimit;
				if (settings.checkScript)  data.checkScript  = settings.checkScript;
				if (settings.fileDataName) data.fileDataName = settings.fileDataName;
				if (settings.queueID)      data.queueID      = settings.queueID;
				if (settings.onInit() !== false) {
					jQuery(this).css('display','none');
					jQuery(this).after('<div id="' + jQuery(this).attr('id') + 'Uploader"></div>');
					swfobject.embedSWF(settings.uploader, settings.id + 'Uploader', settings.width, settings.height, '9.0.24', settings.expressInstall, data, {'quality':'high','wmode':settings.wmode,'allowScriptAccess':settings.scriptAccess},{},function(event) {
						if (typeof(settings.onSWFReady) == 'function' && event.success) settings.onSWFReady();
					});
					if (settings.queueID == false) {
						jQuery("#" + jQuery(this).attr('id') + "Uploader").after('<div id="' + jQuery(this).attr('id') + 'Queue" class="uploadifyQueue"></div>');
					} else {
						jQuery("#" + settings.queueID).addClass('uploadifyQueue');
					}
				}
				if (typeof(settings.onOpen) == 'function') {
					jQuery(this).bind("uploadifyOpen", settings.onOpen);
				}
				jQuery(this).bind("uploadifySelect", {'action': settings.onSelect, 'queueID': settings.queueID}, function(event, ID, fileObj) {
				//Developer Enhancement, which tells the validator that there is at least one file in queue
					queued++;
					
					if (event.data.action(event, ID, fileObj) !== false) {
						var byteSize = Math.round(fileObj.size / 1024 * 100) * .01;
						var suffix = 'KB';
						if (byteSize > 1000) {
							byteSize = Math.round(byteSize *.001 * 100) * .01;
							suffix = 'MB';
						}
						var sizeParts = byteSize.toString().split('.');
						if (sizeParts.length > 1) {
							byteSize = sizeParts[0] + '.' + sizeParts[1].substr(0,2);
						} else {
							byteSize = sizeParts[0];
						}
						if (fileObj.name.length > 20) {
							fileName = fileObj.name.substr(0,20) + '...';
						} else {
							fileName = fileObj.name;
						}
						queue = '#' + jQuery(this).attr('id') + 'Queue';
						if (event.data.queueID) {
							queue = '#' + event.data.queueID;
						}
						
						jQuery(queue).append('<div id="' + jQuery(this).attr('id') + ID + '" class="uploadifyQueueItem">\
								<div class="cancel">\
									<a href="javascript:jQuery(\'#' + jQuery(this).attr('id') + '\').uploadifyCancel(\'' + ID + '\')"><img src="' + settings.cancelImg + '" border="0" title="Remove box" /></a>\
								</div>\
								<span class="fileName">' + fileName + ' (' + byteSize + suffix + ')</span><span class="percentage"></span>\
								<div class="uploadifyProgress">\
									<div id="' + jQuery(this).attr('id') + ID + 'ProgressBar" class="uploadifyProgressBar"><!--Progress Bar--></div>\
								</div>\
							</div>');
					}
				});
				jQuery(this).bind("uploadifySelectOnce", {'action': settings.onSelectOnce}, function(event, data) {
					event.data.action(event, data);
					if (settings.auto) {
						if (settings.checkScript) { 
							jQuery(this).uploadifyUpload(null, false);
						} else {
							jQuery(this).uploadifyUpload(null, true);
						}
					}
				});
				jQuery(this).bind("uploadifyQueueFull", {'action': settings.onQueueFull}, function(event, queueSizeLimit) {
					if (event.data.action(event, queueSizeLimit) !== false) {
						alert('The queue is full.  The max size is ' + queueSizeLimit + '.');
					}
				});
				jQuery(this).bind("uploadifyCheckExist", {'action': settings.onCheck}, function(event, checkScript, fileQueueObj, folder, single) {
					var postData = new Object();
					postData = fileQueueObj;
					postData.folder = (folder.substr(0,1) == '/') ? folder : pagePath + folder;
					if (single) {
						for (var ID in fileQueueObj) {
							var singleFileID = ID;
						}
					}
					jQuery.post(checkScript, postData, function(data) {
						for(var key in data) {
							if (event.data.action(event, data, key) !== false) {
								var replaceFile = confirm("Do you want to replace the file " + data[key] + "?");
								if (!replaceFile) {
									document.getElementById(jQuery(event.target).attr('id') + 'Uploader').cancelFileUpload(key,true,true);
								}
							}
						}
						if (single) {
							document.getElementById(jQuery(event.target).attr('id') + 'Uploader').startFileUpload(singleFileID, true);
						} else {
							document.getElementById(jQuery(event.target).attr('id') + 'Uploader').startFileUpload(null, true);
						}
					}, "json");
				});
				jQuery(this).bind("uploadifyCancel", {'action': settings.onCancel}, function(event, ID, fileObj, data, remove, clearFast) {					
					if (event.data.action(event, ID, fileObj, data, clearFast) !== false) {
						if (remove) {
						//Developer Enhancement, which tells the validator that there is one less file in queue
							queue--;
							
							var fadeSpeed = (clearFast == true) ? 0 : 250;
							
							jQuery("#" + jQuery(this).attr('id') + ID).fadeOut(fadeSpeed, function() { jQuery(this).remove() });
						}
					}
				});
				jQuery(this).bind("uploadifyClearQueue", {'action': settings.onClearQueue}, function(event, clearFast) {					
					var queueID = (settings.queueID) ? settings.queueID : jQuery(this).attr('id') + 'Queue';
					if (clearFast) {
						jQuery("#" + queueID).find('.uploadifyQueueItem').remove();
					}
					if (event.data.action(event, clearFast) !== false) {
					//Developer Enhancement, which tells the validator that there are no files in queue
						queue = 0;
						
						jQuery("#" + queueID).find('.uploadifyQueueItem').each(function() {
							var index = jQuery('.uploadifyQueueItem').index(this);
							jQuery(this).delay(index * 100).fadeOut(250, function() { jQuery(this).remove() });
						});
					}
				});
				var errorArray = [];
				jQuery(this).bind("uploadifyError", {'action': settings.onError}, function(event, ID, fileObj, errorObj) {
					if (event.data.action(event, ID, fileObj, errorObj) !== false) {
						var fileArray = new Array(ID, fileObj, errorObj);
						errorArray.push(fileArray);
						jQuery("#" + jQuery(this).attr('id') + ID).find('.percentage').text(" - " + errorObj.type + " Error");
						jQuery("#" + jQuery(this).attr('id') + ID).find('.uploadifyProgress').hide();
						jQuery("#" + jQuery(this).attr('id') + ID).addClass('uploadifyError');
					}
				});
				if (typeof(settings.onUpload) == 'function') {
					jQuery(this).bind("uploadifyUpload", settings.onUpload);
				}
				jQuery(this).bind("uploadifyProgress", {'action': settings.onProgress, 'toDisplay': settings.displayData}, function(event, ID, fileObj, data) {
					if (event.data.action(event, ID, fileObj, data) !== false) {
						jQuery("#" + jQuery(this).attr('id') + ID + "ProgressBar").animate({'width': data.percentage + '%'},250,function() {
							if (data.percentage == 100) {
								jQuery(this).closest('.uploadifyProgress').fadeOut(250,function() {jQuery(this).remove()});
							}
						});
						if (event.data.toDisplay == 'percentage') displayData = ' - ' + data.percentage + '%';
						if (event.data.toDisplay == 'speed') displayData = ' - ' + data.speed + 'KB/s';
						if (event.data.toDisplay == null) displayData = ' ';
						jQuery("#" + jQuery(this).attr('id') + ID).find('.percentage').text(displayData);
					}
				});
				jQuery(this).bind("uploadifyComplete", {'action': settings.onComplete}, function(event, ID, fileObj, response, data) {
					var response = unescape(response);
					
					if (event.data.action(event, ID, fileObj, unescape(response), data) !== false) {
					//Developer Enhancement, removes the "- Completed" message and "100%"
						//jQuery("#" + jQuery(this).attr('id') + ID).find('.percentage').text(' - Completed');
						jQuery("#" + jQuery(this).attr('id') + ID).find('.percentage').text('');
						
					//Developer Enhancement, adds a custom message and color to the queue when complete
					//This is most likely a JSON array if it begins with a "{" and ends with "}"
						if (response.charAt(0) == '{' && response.charAt(response.length - 1) == '}') {
							var JSONResponse = $.parseJSON(response);
							
						//Check whether or not to add the link to the newly uploaded file
							if (settings.showLinkOnComplete) {
							//Change the container background color
								$('#' + jQuery(event.target).attr('id') + ID).animate({
									'background-color' : '#00FF66'
								})
								
							//Add the link to the newly uploaded file
								.children('span.fileName').html('<a href="' + JSONResponse.URL + '" target="_blank">' + JSONResponse.fileName + '</a> - Completed');
							} else {
							//Change the container background color
								$('#' + jQuery(event.target).attr('id') + ID).animate({
									'background-color' : '#00FF66'
								});
								
							//Add the "- Completed" message
								jQuery("#" + jQuery(this).attr('id') + ID).find('.percentage').text(' - Completed');
							}
						} else {
						//Change the container background color
							$('#' + jQuery(event.target).attr('id') + ID).animate({
								'background-color' : '#993333'
							})
							
						//Create a hidden "div" with the error content
							.append('<div class="uploadifyErrorMessage hide"></div>').children('div.uploadifyErrorMessage').html(response)
							
						//Notify the user of the problem
							.siblings('span.fileName').html('There was an error during uploading. [<a href="javascript:;" class="uploadifyErrorTrigger">Details</a>]');
							
						//Listen for a request to view the error content
							$('.uploadifyErrorTrigger').click(function() {
								var content = $(this).parent().siblings('div.uploadifyErrorMessage').html();
								
								$('<div></div>').html(content).analog({
									title : 'Upload Error',
									width : 600,
									height : 400,
									buttons : {
										'Close' : function() {
											$(this).dialog('close');
										}
									}
								});
							});
						}
						
					//Developer Enhancement, do not hide the box if an error is returned
					//This is most likely a JSON array (which contains a success message) if it begins with a "{" and ends with "}"
						//if (settings.removeCompleted) {
						if (settings.removeCompleted && response.charAt(0) == '{' && response.charAt(response.length - 1) == '}') {
							jQuery("#" + jQuery(event.target).attr('id') + ID).fadeOut(250,function() {jQuery(this).remove()});
						}
						jQuery("#" + jQuery(event.target).attr('id') + ID).addClass('completed');
					}
				});
				
				if (typeof(settings.onAllComplete) == 'function') {
					jQuery(this).bind("uploadifyAllComplete", {'action': settings.onAllComplete}, function(event, data) {
						if (event.data.action(event, data) !== false) {
						//Developer Enhancement, which tells the validator how many files were uploaded
							uploaded = data.filesUploaded;
							
							errorArray = [];
						}
					});
				}
				
			//Developer Enhancement, the form validator
				settings.form.submit(function(event) {
					if (settings.required == true && (queued !== uploaded || queued == 0)) {
						event.preventDefault();
						settings.onRequiredFailed(event, uploader);
						
					//Add the validation error message
						var flash = uploader.parent().children('object');
						
						$('<span></span>').appendTo(document.body).css({
							'position' : 'absolute',
							'top' : flash.offset().top - 3,
							'left' : flash.offset().left - 3,
							'width' : flash.width() - 3,
							'height' : flash.height() - 3,
							'border' : 'red solid 3px'
						}).animate({
							'top' : flash.offset().top - 13,
							'left' : flash.offset().left - 13,
							'width' : flash.width() + 20,
							'height' : flash.height() + 20,
							'border' : 'red solid 3px',
							'opacity' : '0'
						}, 1500, function() {
							$(this).remove();
						});
					} else {
						settings.onRequiredSuccess(event, uploader);
					}
				});
			});
		},
		uploadifySettings:function(settingName, settingValue, resetObject) {
			var returnValue = false;
			jQuery(this).each(function() {
				if (settingName == 'scriptData' && settingValue != null) {
					if (resetObject) {
						var scriptData = settingValue;
					} else {
						var scriptData = jQuery.extend(jQuery(this).data('settings').scriptData, settingValue);
					}
					var scriptDataString = '';
					for (var name in scriptData) {
						scriptDataString += '&' + name + '=' + scriptData[name];
					}
					settingValue = escape(scriptDataString.substr(1));
				}
				returnValue = document.getElementById(jQuery(this).attr('id') + 'Uploader').updateSettings(settingName, settingValue);
			});
			if (settingValue == null) {
				if (settingName == 'scriptData') {
					var returnSplit = unescape(returnValue).split('&');
					var returnObj   = new Object();
					for (var i = 0; i < returnSplit.length; i++) {
						var iSplit = returnSplit[i].split('=');
						returnObj[iSplit[0]] = iSplit[1];
					}
					returnValue = returnObj;
				}
			}
			return returnValue;
		},
		uploadifyUpload:function(ID,checkComplete) {
			jQuery(this).each(function() {
				if (!checkComplete) checkComplete = false;
				document.getElementById(jQuery(this).attr('id') + 'Uploader').startFileUpload(ID, checkComplete);
			});
		},
		uploadifyCancel:function(ID) {
			jQuery(this).each(function() {
				document.getElementById(jQuery(this).attr('id') + 'Uploader').cancelFileUpload(ID, true, true, false);
			});
		},
		uploadifyClearQueue:function() {
			jQuery(this).each(function() {
				document.getElementById(jQuery(this).attr('id') + 'Uploader').clearFileUploadQueue(false);
			});
		}
	})
})(jQuery);
