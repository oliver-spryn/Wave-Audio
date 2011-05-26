<?php
/*
 * This is a simple class which calculates the absolute URL to the directory holding all of the log files, two directories up.
 * Dependant classes will extend this one.
 */

class LogInfo {
	protected $directory;
	
	protected function __construct() {
		$this->directory = strstr(dirname(__FILE__), "\\") ? str_replace("system\server\security", "", dirname(__FILE__)) : str_replace("system/server/security", "", dirname(__FILE__));
	}
}