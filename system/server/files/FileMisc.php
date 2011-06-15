<?php
/*
 * This class contains a number of static file-processing-related methods:
 *  - extension: Return the extension of a file
*/

class FileMisc {
//Return the name a file
	public static function getFileName($file) {
	//Check to see if the file has an extension
		if (strpos($file, ".") === false) {
			return $file;
		} else {
		//Parse the file path
			$fileInfo = pathinfo($file);
			
			return $fileInfo['filename'];
		}
	}
	
//Return the extension of a file
	public static function getExtension($file) {
	//Check to see if the extension can be processed
		strpos($file, ".") === false ? die("The system could not determine the extension of the given path.") : "";
		
	//Parse the file path
		$fileInfo = pathinfo($file);
		
		return $fileInfo['extension'];
	}
	
//Return the permissions of a file
	public static function getPerms($file) {
		return substr(sprintf('%o', fileperms($file)), -4);
	}
	
//Have PHP imitate the MIME type of another file
	public static function imitate($extension) {
	//The MIME class requires a file-name to be associated with an extension, so provide a fake one
		$MIME = new Mime("fakefile." . $extension);
		
		header("Content-type:" . $MIME->MIMEType);
	}
}
?>