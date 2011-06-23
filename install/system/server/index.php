<?php
/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * This script is the core of the "install" module. The structure of this script is very similar to
 * the application super-core, however, it is focused on a much smaller scope, and is not dependant
 * on the global configuration files which will be created later. 
*/

//This system requires a minimum of PHP 5, so ensure that this condition is true before doing anything!
	strnatcmp(phpversion(), '5.0.0') >= 0 ? NULL : die("Please install PHP 5 in order to use this application.");
	
/*
 * This is the only module which does not use the "$installRoot" instance variable from the "Config" class
 * to include necessary files from unknown directories. The "$installRoot" instaince variable will be made 
 * avaliable to all other other PHP modules once the installer creates the "Config" class.
 * 
 * The below code is a way for this module obtain an absoulte URL reference.
*/
	strstr(dirname(__FILE__), "\\") ? $scriptRoot = str_replace("system\server", "", dirname(__FILE__)) : $scriptRoot = str_replace("system/server", "", dirname(__FILE__));
	
//Include the rest of the module's core
	require_once($scriptRoot . "system/server/templates/Setup.php");
	
//Include other non-database dependant classes from the system's core
	require_once("../system/server/core/Validate.php");
	require_once("../system/server/misc/Misc.php");
	require_once("../system/server/files/FileManipulate.php");
	require_once("../system/server/files/FileMisc.php");
	require_once("../system/server/files/Mime.php");
	require_once("../system/server/files/Upload.php");
	
//Start the session
	session_start();
	
//Set server configurations
	set_time_limit(3600);
	ini_set("expose_php", "Off");
?>