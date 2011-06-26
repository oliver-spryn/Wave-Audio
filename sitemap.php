<?php
//Include the system core and classes
	require_once("system/server/index.php");
	
//Output as an XML file
	header("Content-type:text/xml");
	
//The question marks are confusing the PHP server, so echo it manually
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
//Grab all of the dynamically created pages
	$pages = $db->query("SELECT * FROM `pages`", "raw");
	
	while($page = $db->fetch($pages)) {
		" <url>
  <loc>" . ROOT . "page.php</loc>
  <priority>1.000</priority>
 </url>\n";
	}
?>
</urlset>