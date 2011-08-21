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
 * @package    API
 * @copyright  Copyright (c) 2011 and Onwards, ForwardFour Innovations
 * @license    http://forwardfour.com/license    [Proprietary/Closed Source]  
 */

/**
 * This class is used to allow any part of this application to get information 
 * about and communicate with any or all of the plugins installed on this system.
 * 
 * @category   Core
 * @package    API
 * @since      v0.1 Dev
 */
 
class Plugins {
/**
 * Get information about all of the plugins installed on this system
 * 
 * @return     array       An array of values which contain key pieces of information about all installed plugins
 * @since      v0.1 Dev
 */
	public static function allPluginData() {
		global $db;
		
	//Grab the plugins from the database
		$plugins = $db->query("SELECT * FROM `plugins` ORDER BY `position` ASC");
		
	//Generate the array of data to return
		$return = array();
		
		while($plugin = $db->fetch($plugins)) {
			array_push($return, $plugin);
		}
		
	//Output the array
		return $return;
	}
}
?>