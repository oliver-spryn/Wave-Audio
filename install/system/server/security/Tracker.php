<?php
/*
 * This class will act as a simple way to write to and read from a fake "database".
 * However, since this is accessed during the setup, no connection to an actual database has been made yet.
 * Use a simple log file to track information as needed, then.
 * 
 * This "database" will track the IP address and times which users have accessed the installer, and alert this information to inform genuine users of suspicious activity.
 */

class Tracker extends LogInfo {
//Add an entry into the log
	public static function NewLog() {
	//Grab the information from the super class
		parent::__construct();
		
	//Characters to divide each entry will be added if the log file previously existed
		$log = $this->directory . "tracker.log";
		
		if (file_exists($log)) {
			$divider = "
~";
		} else {
			$divider = "";
		}
		
	//Generate details for the log
		$IPAddress = $_SERVER['REMOTE_ADDR'];
		$date = date("l, F j, Y, g:i a T");
		
	//Create or add to the previous log entry
		$logOpen = fopen($log, "c");
		fwrite($logOpen, $divider . $IPAddress . ":" . $date);
		fclose($logOpen);
	}
	
//Retrieve an entry from the log
	public static function GetLogs() {
	//Grab the information from the super class
		parent::__construct();
		
	//If the log file does not exist, then return false
		$log = $this->directory . "tracker.log";
		
		if (file_exists($log)) {
		//Read the file
			$logHandle = fopen($log, "r");
			$contents = fread($logHandle, filesize($log));
			fclose($logHandle);
			
		//Apply tidy formatting
			$tidy = "";
			
			foreach(explode("~", $contents) as $entry) {
				$entry = explode(":", $entry);
				
				$tidy .= "<strong>IP Address:</strong> " . $entry['0'] . ", <strong>Accessed on:</strong> " . $entry['1'] . "<br />";
			}		
			
			return rtrim($tidy. "<br />");
		} else {
			return false;
		}
	}
}