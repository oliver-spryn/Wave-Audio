<?php
//Include the system core and classes
	require_once("system/server/index.php");
	
//Output as a text file
	header("Content-type:text/plain");
?>
User-agent: *
Disallow: 
Sitemap: <?php echo ROOT; ?>sitemap.xml