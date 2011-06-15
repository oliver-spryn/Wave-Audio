<?php
/*
 * This class is solely intended to create, read, update, and delete the contents of a file:
 *  - write: Write to a given file, if it exists, otherwise it will be created and written to
 *  - read: Read the contents of a file
 *  - clean: Delete all of the contents of a file, but preserve the file itself
 */

class FileManipulate {
//Write to a given file, if it exists, otherwise it will be created and written to
	public static function write($file, $contents, $entryDivider = false) {
	//Check to see if the directory exists. Don't check for the file, because it will be created if it is not found.
		if (is_dir(dirname($file))) {
		//Check if the existing file is writable, or doesn't exist at all
			if ((file_exists($file) && is_writable($file)) || !file_exists($file)) {
			//Add a divider, if one is requested, and there is content to divide
				if ($entryDivider) {
					$contents = self::read($file) == "" ? $contents : $entryDivider . $contents;
				}
				
				$fileOpen = fopen($file, "c");
				fwrite($fileOpen, $contents);
				fclose($fileOpen);
			} else {
			//If the existing file is not writable, then try changing its permission, and trying again
				$permissions = FileMisc::getPerms($file);
				
				if (chmod($file, 0777)) {
					self::write($file, $contents);
					chmod($file, $permissions);
				} else {
					die("The requested file is not writable.");
				}
			}
		} else {
			die("The given directory path could not be found.");
		}
	}
	
//Read the contents of a file
	public static function read($file, $entryDivider = false) {
	//Check to see if the file exists
		if (file_exists($file)) {
		//Check to see if the file can be read
			if (is_readable($file)) {
				$fileOpen = fopen($file, "r");
				$contents = fread($fileOpen, filesize($file));
				fclose($fileOpen);
				
			//If there is an entry divider (such as a "/" breaking up each entry) then return an array of entries
				if ($entryDivider) {
					return explode($entryDivider, $contents);
				} else {
					return $contents;
				}
			} else {
			//If the file cannot be read, try changing it permissions and reading it again
				$permissions = FileMisc::getPerms($file);
				
				if (chmod($file, 0777)) {
					return self::read($file, $entryDivider);
					chmod($file, $permissions);
				} else {
					die("The requested file is not readable.");
				}
			}
		} else {
			die("The given file could not be found.");
		}
	}
	
//Delete all of the contents of a file, but preserve the file itself
	public static function clean($file) {
	//Check to see if the file
		if (file_exists($file)) {
		//Check if the existing file is writable
			if (is_writable($file)) {
				$fileOpen = fopen($file, "w");
				fwrite($fileOpen, "");
				fclose($fileOpen);
			} else {
			//If the existing file is not writable, then try changing its permission, and trying again
				$permissions = FileMisc::getPerms($file);
				
				if (chmod($file, 0777)) {
					self::clean($file);
					chmod($file, $permissions);
				} else {
					die("The requested file is not writable.");
				}
			}
		} else {
			die("The given file could not be found.");
		}
	}
}
?>