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
 * about and communicate with any or all of the templates installed on this system.
 * 
 * @category   Core
 * @package    API
 * @since      v0.1 Dev
 */
 
class Templates {
/**
 * Get information about the currently used administration template
 * 
 * @return     array       An array of values which contain key pieces of information the current template
 * @since      v0.1 Dev
 */
	public static function currentAdminTemplateInfo() {
		global $db;
		
	//Grab the current template's information from the database
		return $db->quick("SELECT * FROM `template-admin` WHERE `selected` = '1'");
	}
}
?>