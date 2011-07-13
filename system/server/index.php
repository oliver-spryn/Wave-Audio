<?php
/**
 * Epoch Cloud Management Platform
 * 
 * LICENSE
 * 
 * By viewing, using, or actively developing this application in any way, you are
 * henceforth bound the license agreement, and all of its changes, set forth by
 * ForwardFour Innovations. The license can be found, in its entirety, at this 
 * address: http://forwardfour.com/license.
 * 
 * @category   Core
 * @copyright  Copyright (c) 2011 and Onwards, ForwardFour Innovations
 * @license    http://forwardfour.com/license    [Proprietary/Closed Source]  
 */

/*
 * This script is the super core of the system, which prepares the values from the
 * configuration script for use within the system. Here is an overview of this
 * relatively simple script:
 *  [1] The server checks to see if at least PHP 5 is running, then defines several
 *      constants which will make the major and minor versions avaliable to the
 *      system.
 *  [2] Check to see if the configuration script exists, and display a message if
 *  	it does not.
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
	!defined("PHP_MAJOR_VERSION") ? define("PHP_MAJOR_VERSION", current(explode(".", $PHPVersionInfo))) : NULL;
	!defined("PHP_MINOR_VERSION") ? define("PHP_MINOR_VERSION", $PHPVersionInfo) : NULL;
	PHP_MAJOR_VERSION < 5 ? die("Please install PHP 5 or greater in order to use this application.") : NULL;
	
//Check to see if the system has been setup, and handle this accordingly
	if (strpos(dirname(__FILE__), "/") === true) {
		$configDirectory = str_replace("system/server", "", dirname(__FILE__)) . "/data/system/config.php";
	} else {
		$configDirectory = str_replace("system\\server", "", dirname(__FILE__)) . "\\data\\system\\config.php";
	}

	if (file_exists($configDirectory)) {
		require_once($configDirectory);
		unset($configDirectory); //Unset this variable for security purposes
	} else {
		die("This application requires installation.");
	}
	
//Instantiate the "Config" class
	$config = new Config();
	
//Set the directory slashes based on the server's operating system
	define("SLASHES", $config->operatingSystem == "unix" ? "/" : "\\");
	
//Detirmine the root address for the entire site on the local server
	in_array('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on" ? define("PROTOCOL", "https://") : define("PROTOCOL", "http://");
	define("ROOT", PROTOCOL . $config->installDomain);
	define("INSTALL_ROOT", $config->installRoot);
	
//Detirmine the root address for the entire site on the CDN server
	PROTOCOL == "https://" ? define("CDN_PROTOCOL", "https://") : define("CDN_PROTOCOL", "http://");
	define("CDN_ROOT", CDN_PROTOCOL . $config->CDNRoot);
	
//Detirmine the root address for the entire site on the local or CDN server, based on configruation settings
	$config->useCDN ? define("STATIC_ROOT", CDN_ROOT) : define("STATIC_ROOT", ROOT);
	
//Include the essential classes within the system's core, with the exception of the configuration script
	require_once(INSTALL_ROOT . "system/server/core/Database.php");
	require_once(INSTALL_ROOT . "system/server/core/Validate.php");
	require_once(INSTALL_ROOT . "system/server/templates/TemplateBase.php");
	
//Start the session	
	if (session_id() == "") {
		session_save_path($config->installRoot . "data/system/sessions");
		session_name("CMS_" . $config->sessionSuffix);
		session_start();
	}
	
//Set server configurations
	set_time_limit(3600);
	ini_set("expose_php", "Off");
	
/**
 * Import additional classes and packages into a script for parsing, by using
 * ECMAScript standards
 * 
 * @param      string      $classes     The ECMAScript-style path to a given class or package to import
 * @return     boolean     A short message to return when importing was a success
 * @since      v0.1 Dev
 */
	function import($classes) {
	//Convert ECMAScript style directory structures to Unix style
		$address = str_replace(".", SLASHES, $classes);
		
	//Check to see if all of the classes in a package should be imported
		$all = end(explode(SLASHES, $address)) == "*" ? true : false;
		
	//Generate the operating system specific path to the packages container
		$container = SLASHES == "/" ? "system/server/" : "system\\server\\"; 
		
	//Import the requested classes
		if ($all) {
			$address = explode(SLASHES, $address);
			array_pop($address);
			$address = INSTALL_ROOT . $container . implode(SLASHES, $address) . SLASHES;
			
		//Check to see if this is a directory
			if (is_dir($address)) {
				$handle = opendir($address);
				
				while (false !== ($file = readdir($handle))) {
				//Include only files, no directories
					if ($file != "." && $file != ".." && is_file($address . $file)) {
						require_once($address . $file);
					}
				}
				
				return true;
			} else {
				die("&quot;" . $classes . "&quot; does not link to an existing package");
			}
	//Import only a single class
		} else {
			$address = INSTALL_ROOT . $container . $address . ".php";
			
			if (file_exists($address) && is_file($address)) {
				require_once($address);
				
				return true;
			} else {
				die("&quot;" . $classes . "&quot; does not link to an existing class");
			}
		}
	}
?>