<?php
/*
 * This is a simple class which is used to ease the implementation of file uploads. In addition to handling
 * the uploader process, it also integrates a simple validator, security settings, and error handling 
 * capibilities:
 *  - varsSet: Make sure the required variables have been set
 *  - inputPrepare: A helper method, to prepare the given input for processing in the "isAllowed" method
 *  - isAllowed: Ensure the uploaded file type is allowed
 *  - errors: Check for file upload errors, and display a friendly warning if so
 *  - process: Upload the file
*/

class Upload {
//User defined variables
	public $fileField;
	public $directory;
	public $required = false;
	public $allowedExt = false; //Only file-types listed here may be uploaded
	public $deniedExt = false;  //File-types listed here are deined, all others are permitted
	public $addHashSuffix = true;
	public $renameTo = false;
	
//System defined variables
	private $neverAllowed = array("php", "php3", "php4", "php5", "tpl", "php-dist", "phtml", "phtm", "htaccess", "htpassword", "asp", "asa", "ashx", "aspx", "ascx", "asmx", "cs", "vb", "config", "master", "shtm", "shtml", "stm", "ssi", "inc", "cfm", "cfml", "cfc", "jsp", "jst", "pl", "cgi");
	private $alwaysAllowed = array();
	
	public function Upload() {
	//Nothing to do!
	}
	
//Make sure the required variables have been set
	private function varsSet() {
		$required = array("fileField", "directory");
		
		foreach($required as $variable) {
			if (empty($this->$variable)) {
				die("&quot;" . $variable . "&quot; was not set in the uploader processing class.");
				break;
			}
		}
		
		return true;
	}
	
//A helper method, to prepare the given input for processing in the "isAllowed" method
	private function inputPrepare($input) {
		if ($input == "" || $input == "*" || !$input) {
			$input = array();
		} elseif (is_array($input)) {
			//Nothing to do, it is already ready
		} else {
			$input = array($input);
		}
		
		return $input;
	}
	
/*
 * This method must take multiple inputs into consideration, the developer-defined "allowedExt" and 
 * "deniedExt" inputs, as well as the system defined "neverAllowed" and "alwaysAllowed" variables.
 * For security purposes, the system will have priority over the developer-defined values, should a 
 * conflict arise.
 * 
 * The developer-provided inputs can be either a string, for single extension, an array for multiple
 * extensions, or an empty string or a "*" for no restrictions. The algorithm must take these known
 * rules into consideration when allowing or restricting an upload.
 * 
 * If a developer supplies input types for both allowed and denied, then one must take precedence. 
 * For security purposes, the "allowedExt" is used, because it further restricts the types of 
 * potentially harmful files a user may upload.
 * 
 * The meat of the decision process takes place inside of the latter conditional statement. If the
 * above code detirmined that the "allowed" array was being analyzed, then *all* of the following
 * must be be true for the extension:
 *  - the extension is listed in "allowedExt" or "alwaysAllowed"
 *  - the extension is not listed "neverAllowed"
 *  
 * If the above code detirmined that the "denied" array was being analyzed, then *one* of the
 * following must be be true for each extension:
 *  - the extension is listed in "deniedExt"
 *  - the extension is listed in "neverAllowed"
*/

//Ensure the uploaded file type is allowed
	private function isAllowed($extension) {
	//If both input types are defined, then one must take precedence
	//Prepare the developer-defined input for processing
		if ($this->allowedExt && $this->deniedExt) {
			$process = $this->inputPrepare($this->allowedExt);
			$processType = "allowed";
		} elseif ($this->allowedExt && !$this->deniedExt) {
			$process = $this->inputPrepare($this->allowedExt);
			$processType = "allowed";
		} else {
			$process = $this->inputPrepare($this->deniedExt);
			$processType = "denied";
		}

	//Evaluate the given file
		if ($processType == "allowed" && (in_array($extension, $process) || in_array($extension, $this->alwaysAllowed)) && !in_array($extension, $this->neverAllowed)) {
			return true;
		} elseif ($processType == "denied" && !in_array($extension, $process) && !in_array($extension, $this->neverAllowed)) {
			return true;
		} else {
			die("The file you attempted to upload is not allowed.");
		}
	}
	
//Check for file upload errors, and display a friendly warning if so
	private function errors() {
		switch($_FILES[$this->fileField]['error']) {
			case UPLOAD_ERR_OK : 
			//This error code means, well, nothing went wrong!
				break;
				
			case UPLOAD_ERR_INI_SIZE : 
				die("<p>The file you attempted to upload was larger than the " . ini_get('upload_max_filesize') . " file size the site is configured accept, and thus, the file upload could not complete.
<br /><br />
You have several ways of working around this:</p>
<ul>
<li>Try to make the file smaller. For example, if you were trying to upload an image, you can compress the size of the image with a free program, such as <a href=\"http://gimp.org/\" target=\"_blank\">GIMP</a>. If you are uploading a video, try splitting the video into smaller parts.</li>
<li>Contact your administrator to have the maximum upload file size increased.</li>
<li>There are a number of free solutions which will host large files for you at no charge, such as <a href=\"http://box.net/\" target=\"_blank\">Box.net</a> for any type of file, <a href=\"https://picasaweb.google.com/\" target=\"_blank\">Picasa Web</a> for images, and <a href=\"http://vimeo.com\" target=\"_blank\">Vimeo</a> for videos. These services are specialized to deliver high-volume content, and may serve your content faster and more efficiently then uploading directly to this site.</li>
</ul>");
				break;
				
			case UPLOAD_ERR_FORM_SIZE : 
				die("<p>The file you attempted to upload was larger than the " . $_POST['MAX_FILE_SIZE'] . " bytes the form is configured accept, and thus, the file upload could not complete.
<br /><br />
You have several ways of working around this:</p>
<ul>
<li>Try to make the file smaller. For example, if you were trying to upload an image, you can compress the size of the image with a free program, such as <a href=\"http://gimp.org/\" target=\"_blank\">GIMP</a>. If you are uploading a video, try splitting the video into smaller parts.</li>
<li>Contact your administrator to have the maximum upload file size on this page increased. When making this request, you will want to share this URL with him or her: <strong>" . PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "</strong>.</li>
<li>There are a number of free solutions which will host large files for you at no charge, such as <a href=\"http://box.net/\" target=\"_blank\">Box.net</a> for any type of file, <a href=\"https://picasaweb.google.com/\" target=\"_blank\">Picasa Web</a> for images, and <a href=\"http://vimeo.com\" target=\"_blank\">Vimeo</a> for videos. These services are specialized to deliver high-volume content, and may serve your content faster and more efficiently then uploading directly to this site.</li>
</ul>");
				break;
				
			case UPLOAD_ERR_PARTIAL :
				die("<p>The file you attempted to upload was only partially uploaded. Please ensure that you did not cancel the upload operation before it was able to complete. If you did not cancel the upload, then you may have temporarily lost your internet connection, which interfered with the upload. Please try the uploading the file again, without interfering with the upload. If you are having difficulties with the upload, contact you internet service provider or the site administrator.</p>");
				break;
				
			case UPLOAD_ERR_NO_FILE : 
				die("<p>No file upload took place, because a file wasn't provided! If you had meant to upload a file, please try again.</p>");
				break;
				
			case UPLOAD_ERR_NO_TMP_DIR : 
				die("<p>The file upload process was not able to complete because of a server error. Unfortunately, this issue can only be resolved by the site administrator. Please notify the site administrator of this issue. When doing so, please share this piece of information: <strong>UPLOAD_ERR_NO_TMP_DIR</strong>.</p>");
				break;
				
			case UPLOAD_ERR_CANT_WRITE : 
				die("<p>The file upload process was not able to complete because of a server error. Unfortunately, this issue can only be resolved by the site administrator. Please notify the site administrator of this issue. When doing so, please share this piece of information: <strong>UPLOAD_ERR_CANT_WRITE</strong>.</p>");
				break;
				
			case UPLOAD_ERR_EXTENSION : 
				die("<p>The file upload process was not able to complete because of a server error. Unfortunately, this issue can only be resolved by the site administrator. Please notify the site administrator of this issue. When doing so, please share this piece of information: <strong>UPLOAD_ERR_EXTENSION</strong>.</p>");
				break;
				
			default : 
				die("<p>The file upload process was not able to complete because of an unknown server error. You can try uploading your file again, and see if you succeed. If this problem persists, then please notify the site administrator of this issue. When doing so, please share this piece of information: <strong>An unknown error caused a file upload to stop</strong>.</p>");
				break;
		}
	}

//Upload the file
	public function process() {
	//Make sure the required variables have been set
		$this->varsSet();
		
	//Validate this file
		$this->required == true ? Validate::isUploaded($this->fileField) : null;
		
	//Grab all of the data relative to the file
		$tempFile = $_FILES[$this->fileField]['tmp_name'];
		$fileName = $this->renameTo == true ? FileMisc::getFileName($this->renameTo) : FileMisc::getFileName($_FILES[$this->fileField]['name']);
		$randomHash = $this->addHashSuffix == true ? "_" . Misc::randomValue() : "";
		$extension = FileMisc::getExtension($_FILES[$this->fileField]['name']);
		$targetFile = $fileName . $randomHash . "." . $extension;
		$directory = rtrim($this->directory, "/") . "/";
		
	//Ensure the uploaded file type is allowed
		$this->isAllowed($extension);
		
	//Check for file upload errors, and display a friendly warning if so
		$this->errors();
		
	//Move the uploaded file to its destination
		if (move_uploaded_file($tempFile, $directory . $targetFile)) {
			$MIME = new Mime($directory . $targetFile);
			
			return array(
				"status" => "success",
				"URL" => $directory . $targetFile,
				"directory" => $directory,
				"fileName" => $targetFile,
				"fileExt" => $extension,
				"fileMIME" => $MIME,
				"fileSize" => filesize($directory . $targetFile)
			);
		} else {
		//If the requested directory is not writable, then try changing its permission, and trying again
			$permissions = FileMisc::getPerms($file);
			
			if (chmod($directory, 0777)) {
				if (move_uploaded_file($tempFile, $directory . $targetFile)) {
					$MIME = new Mime($directory . $targetFile);
			
					return array(
						"status" => "success",
						"URL" => $directory . $targetFile,
						"directory" => $directory,
						"fileName" => $targetFile,
						"fileExt" => $extension,
						"fileMIME" => $MIME,
						"fileSize" => filesize($directory . $targetFile)
					);
				} else {
					chmod($directory, $permissions);
					die("<p>The file upload process was not able to complete because of a server error. Unfortunately, this issue can only be resolved by the site administrator. Please notify the site administrator of this issue. When doing so, please share this piece of information: <strong>An uploaded file could not be moved to its destination folder</strong>.</p>");
				}
			} else {
				die("<p>The file upload process was not able to complete because of a server error. Unfortunately, this issue can only be resolved by the site administrator. Please notify the site administrator of this issue. When doing so, please share this piece of information: <strong>An uploaded file could not be moved to its destination folder</strong>.</p>");
			}
		}
	}
}
?>