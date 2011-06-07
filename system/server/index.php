<?php
//This script is the super core of the system, which prepares the values from the configuration script for use within the system, as well as define a few constants, and import all of the system core files.

//This system requires a minimum of PHP 5, so ensure that this condition is true before doing anything!
	strnatcmp(phpversion(), '5.0.0') >= 0 ? NULL : die("Please install PHP 5 in order to use this application.");

/*
 * This is the only script which does not use the "$installRoot" instance variable from the "Config" class to include necessary files from unknown directories.
 * The "$installRoot" instaince variable will be made avaliable to all other other PHP scripts once they have included "index.php".
*/
	strstr(dirname(__FILE__), "\\") ? $configScript = str_replace("system\server", "", dirname(__FILE__)) . "data\system\config.php" : $configScript = str_replace("system/server", "", dirname(__FILE__)) . "data/system/config.php";
	require_once($configScript);
	
//Instantiate the "Config" class
	$config = new Config();
	
//Detirmine the root address for the entire site, and include the "http://" if SSL is not active and "https://" if SSL is active
	$_SERVER['HTTPS'] == "on" ? define("PROTOCOL", "https://") : define("PROTOCOL", "http://");
	define("ROOT", PROTOCOL . $config->installDomain);
	define("STRIPPED_ROOT", $config->installDomain);
	
//Include the rest of the system's core. The order of the files in the "$include" array are important! Do not rearrange the order!
	$include = array("core/Database.php", "users/Authentication.php", "layout/Template.php");
	
	foreach($include as $script) {
		require_once($config->installRoot . "system/server/" . $script);
	}
	
//Start the session
	session_save_path($config->installRoot . "data/system/sessions");
	session_name("CMS_" . $config->sessionSuffix);
	session_start();
	
//Set server configurations
	set_time_limit(3600);
	ini_set("expose_php", "Off");
?>