<?php
/*
 * By viewing, using, or actively developing this application in any way, you are
 * henceforth bound the license agreement, and all of its changes, set forth on by
 * ForwardFour Innovations. The license can be found, in its entirety, at this 
 * address: http://forwardfour.com/license
 * 
 * This script is the super core of the system, which prepares the values from the
 * configuration script for use within the system. Here is an overview of this
 * relatively simple script:
 *  [1] The server checks to see if at least PHP 5 is running, then defines several
 *      constants which will make the major and minor versions avaliable to the
 *      system.
 *  [2] Check to see if the configuration script exists, and redirect to the setup
 *  	if it does not.
 *  [3] Instantiate the "Config" class for use through out this script and system.
 *  [4] Windows directory paths use a backslash. However, all other operating systems
 *      use a forward slash. This step uses the configuration file to set whether or
 *      slashes should be forward or back. 
 *  [5] Define several constants which will define local and CDN-based URLs for system-
 *  	wide use.
 *  [6] Include the essential classes within the system's core.
 *  [7] Start a session.
 *  [8] Set several several configurations which will boost performance, improve
 *  	security, and allow certain actions.
 *  [9] Create a function which be used to import additional classes and packages into
 *  	a script for parsing, using ECMAScript standards.
 * 
*/

//This system requires a minimum of PHP 5, so ensure that this condition is true before doing else anything!
	$PHPVersionInfo = phpversion();
	define('PHP_MAJOR_VERSION', current(explode($PHPVersionInfo, ".")));
	define('PHP_MINOR_VERSION', $PHPVersionInfo);
	PHP_MAJOR_VERSION < 5 ? NULL : die("Please install PHP 5 or greater in order to use this application.");
	
//Check to see if the system has been setup, and handle this accordingly
	$configDirectory = str_replace("system/server", "", dirname(__FILE__)) . "data/system/config.php";	

	if (file_exists($configDirectory)) {
		require_once($configDirectory);
		unset($configDirectory); //Unset this variable for security purposes
	} else {
		header("Location: " . str_replace("system/server", "", dirname(__FILE__)) . "install/index.php");
		exit;
	}
	
//Instantiate the "Config" class
	$config = new Config();
	
//Set the directory slashes based on the server's operating system
	define("SLASHES", $config->operatingSystem == "unix" ? "/" : "\\");
	
//Detirmine the root address for the entire site, and include the "http://" if SSL is not active and "https://" if SSL is active
	in_array('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on" ? define("PROTOCOL", "https://") : define("PROTOCOL", "http://");
	define("ROOT", PROTOCOL . $config->installDomain);
	
	PROTOCOL == "https://" ? define("CDN_PROTOCOL", "https://") : define("CDN_PROTOCOL", "http://");
	define("CDN_ROOT", CDN_PROTOCOL . $config->CDNRoot);
	
//Include the essential classes within the system's core, with the exception of the configuration script
	require_once($config->installRoot . "system/server/core/Database.php");
	require_once($config->installRoot . "system/server/core/Validate.php");
	
//Start the session	
	if (session_id() == "") {
		session_save_path($config->installRoot . "data/system/sessions");
		session_name("CMS_" . $config->sessionSuffix);
		session_start();
	}
	
//Set server configurations
	set_time_limit(3600);
	ini_set("expose_php", "Off");
	
//This function will be used to import additional classes and packages into a script for parsing, using ECMAScript standards
	function import($classes) {
	//Convert ECMAScript style directory structures to Unix style
		$address = str_replace(".", "/", $classes);
		
	//Check to see if all of the classes in a package should be imported
		$all = end(explode("/", $address)) == "*" ? true : false;
		
	//Import the requested classes
		if ($all) {
			$address = implode("/", array_pop(explode("/", $address)));
			
		//Check to see if this is a directory
			if (is_dir($address)) {
				$handle = opendir($address);
				
				while (false !== ($file = readdir($handle))) {
				//Include only files, no directories
					if ($file != "." && $file != ".." && is_file($file)) {
						require_once($config->installRoot . "system/server/" . $address . $file);
					}
				}
				
				return true;
			} else {
				die("&quot;" . $classes . "&quot; does not link to an existing package");
			}
	//Import only a single class
		} else {
			if (file_exists($file) && id_file($file)) {
				require_once($classes);
			} else {
				die("&quot;" . $classes . "&quot; does not link to an existing class");
			}
		}
	}
?>