<?php
/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * This class will used to introduce dynamic content to each of the static HTML 
 * administration templates:
 *  - __construct: Assign the dynamic variables for the template
 *  - top: Import and build the template's beginning
 *  - bottom: Import and build the template's ending
*/

class TemplateAdmin {
//Defined from database or script
	private $template;
	private $footer;
	
//Defined on as-needed basis
	public $title;
	public $byLine;
	
//Setup the database-driven variables
	public function __construct() {
	/*
	 * There is no longer anything for this constructor method to run because of a serious bug
	 * it caused in files, mainly in dynamic JS files, which included the system's super core,
	 * but did not utilize this self-instantiating class. Since this class relied on the 
	 * "Config" class created during the setup, any files which used the core, but did not rely
	 * on the "Config" class, were giving errors, and disrupting normal operations.
	 * 
	 * Now, these dynamic variables will only be pulled from pages which call the "top" method,
	 * and are certain to have access to the "Config" class.
	*/
	}
	
//Import the template beginning
	public function top() {
		global $db, $config;
		
	//Site information
		$templateData = $db->quick("SELECT * FROM `config` WHERE `id` = '1'");
		$siteName = $templateData['name'];
		$slogan = $templateData['slogan'];
		$this->template = $templateData['template'];
		$lang = " lang=\"en-US\"";
		$this->footer = $templateData['footer'];
		
	//Grab the logo URL
		$logoGrabber = glob($config->installRoot . "system/images/logo.{png,jpg,jpeg,gif,bmp}", GLOB_BRACE);
		$logoInfo = pathinfo($logoGrabber[0]);
		$logo = ROOT . "system/images/" . $logoInfo['basename'];
		
	//Generate the template root URL
		$templateRoot = ROOT . "system/templates/desktop/" . $this->template . "/";
		
	//Check if this is the home page
		/*$pageData = $db->query("SELECT * FROM `pages` WHERE `position` = '1' AND `parent` = ''");
		
		if (ROOT == PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] || ROOT . $pageData['url'] == PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) {
			$this->homePage = true;
		} else {
			$this->homePage = false;
		}*/
		
	//Create the bread crumb navigation
		/*$breadCrumb = "<ul>\n";
		
		if (Authentication::loggedIn()) {
			//To-DO
		} else {				
			if ($homePage) {
				$breadCrumb .= "<li class=\"current\"><a href=\"" . ROOT . $pageData['url'] . "\">" . $pageData['title'] . "</a></li>\n";
			}
		}
		
		$breadCrumb .= "</ul>";*/
		
	//SEO information
		$SEOData = $db->query("SELECT * FROM `seo`");
		$SEOMeta = "";
		
		while($data = $db->fetch($SEOData)) {
			$SEOMeta .= $data['meta'] . "\n";
		}
		
		$headers = "<base href=\"" . ROOT . "\">
<meta charset=\"UTF-8\">
<meta name=\"robots\" content=\"index,follow\">
<meta name=\"googlebot\" content=\"index,follow\">
" . $SEOMeta . "
<link rel=\"shortcut icon\" href=\"" . ROOT . "system/images/favicon.ico\">
<link rel=\"stylesheet\" href=\"" . ROOT . "system/stylesheets/superpackage.desktop.css\">
<script src=\"" . ROOT . "system/javascripts/superpackage.desktop.js\"></script>
";
		
		$title = $this->title;
		$byLine = $this->byLine;
		
		require_once($config->installRoot . "system/templates/desktop/" . $this->template . "/top.php");
	}
	
//Import the template ending
	public function bottom() {
		global $config;
		
		$footer = $this->footer;
		
		require_once($config->installRoot . "system/templates/desktop/" . $this->template . "/bottom.php");
	}
}

//Instantiate this class
	$templateAdmin = new TemplateAdmin();
?>