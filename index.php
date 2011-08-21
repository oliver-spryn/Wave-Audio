<?php
//Include the system core
	require_once("system/server/index.php");

//Find the module which is intended to display content on the public website area, and call its API class
	$default = $db->select("SELECT * FROM `users` WHERE", array(
		"default" => "1"
	));
	
	require_once($default['root'] . "system/server/API/Default.php");
?>