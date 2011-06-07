<?php
/*
 * This class will act as a simple way to write to and read from a fake "database".
 * However, since this is accessed during the setup, no connection to an actual database has been made yet.
 * Use a simple log file to track information as needed, then.
 * 
 * This "database" will track IP address who have been banned from the application setup, for security reasons.
 */

class Ban extends LogInfo {
//Add an entry into the ban list
	public static function AddIP($IPAddress) {
	//Grab the information from the super class
		$this->__construct();
		
	//Characters to divide each entry will be added if the log file previously existed
		$log = $this->directory . "ban.log";
		
		if (file_exists($log)) {
			$divider = "~";
		} else {
			$divider = "";
		}
		
	//Create or add to the previous log entry
		$logHandle = fopen($log, "c");
		fwrite($logHandle, $divider . $IPAddress);
		fclose($logHandle);
	}
	
//Retrieve an entry from the ban list
	public static function IsBanned($IPAddress) {
	//Grab the information from the super class
		$this->__construct();
		
	//Read the file
		$log = $this->directory . "ban.log";
		$logHandle = fopen($log, "r");
		$contents = fread($logHandle, filesize($log));
		fclose($logHandle);
		
	//Explode the contents into an array, then check if the supplied IPAddress is included in the log
		foreach(explode("~", $contents) as $address) {
			if ($address === $IPAddress) {
				return true;
				break;
			}
		}
		
		return false;
	}
}