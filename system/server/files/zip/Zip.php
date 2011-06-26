<?php
/*
 * This class is meant to simplify the process of creating a ZIP archive. Through the use
 * of composition, the "ZipArchive" extension is called and used in its most common manner, 
 * which will satisfy and simply the common creation of ZIP archives:
 *  - compile: Gather and zip all of the files together
 * 
*/

class Zip {
//User defined variables
	public $ZIPName;
	public $destination;
	public $addFiles;   //These files must exist!
	public $addFolders; //These folders must exist, otherwise an empty one will be !
	public $deleteAddedFiles = false;
	public $deleteAddedFolders = false;
	
	public function __construct() {
	//Nothing to do!
	}
	
//Make sure the required variables have been set
	private function varsSet() {
	//All of these need to be set
		$required = array("ZIPName", "destination");
		
		foreach($required as $variable) {
			if (empty($this->$variable)) {
				die("&quot;" . $variable . "&quot; was not set in the zip class");
				break;
			}
		}
		
	//Only one of these need to be set
		$this->addFiles || $this->addFolders ? true : die("At least one file or folder must be passed into the zip class");
		
		return true;
	}
	
//Check to see if all of the files exist, and clean out any folders
	private function filesClean() {
		try {
			if (!is_array($this->addFiles)) {
				throw new Exception("The supplied value is a string");
			}
			
			foreach ($this->addFiles as $key => $value) {
				if (!file_exists($value) || is_dir($value)) {
					unset($this->addFiles[$key]);
				}
			}
		} catch (Exception $e) {
			if (!file_exists($this->addFiles) || is_dir($this->addFiles)) {
				unset($this->addFiles);
			}
		}
	}
	
//Clean out any files in the directory list
	private function foldersClean() {
		foreach ($this->addFolders as $key => $value) {
			if (!is_dir($value) || is_file($value)) {
				unset($this->addFolders[$key]);
			}
		}
	}
	
//Gather and zip all of the files together
//This is not called "Zip", even though it would make sense, because it may be considered a constructor method
	public function compile() {
	//Make sure the required variables have been set
		$this->varsSet();
		
	//Make sure the destination folder exists
		is_dir($this->destination) ? true : die("The destination folder does not exist!");
		
	//Create the ZIP archive
		$zipper = new ZipArchive();
		
		try {
			if ($zipper->open($filename, ZipArchive::CREATE) === true) {
				$this->addFiles($zipper);
				$this->addFolders($zipper);
				
				return true;
			} else {
				throw new Exception("The ZIP archive could not be created");
			}
		} catch (Exception $e) {
			try {
				$permissions = FileMisc::getPerms($this->destination);
				
				if (chmod($this->destination, 0777) && $zipper->open($filename, ZipArchive::CREATE) === true) {
					$this->addFiles($zipper);
					$this->addFolders($zipper);
					
					return true;
				} else {
					chmod($this->destination, $permissions);
					
					throw new Exception("The ZIP archive could not be created");
				}
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
	}
}
?>