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
	private $lang;
	private $name;
	private $slogan;
	private $logo;
	private $templateRoot;
	private $template;
	private $headers;
	private $homePage;
	private $breadCrumb;
	
//Defined on as-needed basis
	public $title;
	public $includeTop;
	public $includeBottom;
	
//Setup the database-driven variables
	public function __construct() {
		global $db, $config;
		
	//Site information
		$templateData = $db->quick("SELECT * FROM `config` WHERE `id` = '1'");
		$this->name = $templateData["name"];
		$this->slogan = $templateData["slogan"];
		$this->template = $templateData["template"];
		$this->lang = " lang=\"en-US\"";
		
	//Grab the logo URL
		$logoGrabber = glob($config->installRoot . "system/images/logo.{png,jpg,jpeg,gif,bmp}", GLOB_BRACE);
		$logoInfo = pathinfo($logoGrabber[0]);
		$this->logo = ROOT . "system/images/" . $logoInfo['basename'];
		
	//Generate the template root URL
		$this->templateRoot = ROOT . "system/templates/desktop/" . $this->template . "/";
		
	//Check if this is the home page
		/*$pageData = $db->query("SELECT * FROM `pages` WHERE `position` = '1' AND `parent` = ''");
		
		if (ROOT == PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] || ROOT . $pageData['url'] == PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) {
			$this->homePage = true;
		} else {
			$this->homePage = false;
		}*/
		
	//Create the bread crumb navigation
		$this->breadCrumb = "<ul>\n";
		
		if (Authentication::loggedIn()) {
			//To-DO
		} else {				
			if ($this->homePage) {
				$this->breadCrumb .= "<li class=\"current\"><a href=\"" . ROOT . $pageData['url'] . "\">" . $pageData['title'] . "</a></li>\n";
			}
		}
		
		$this->breadCrumb .= "</ul>";
		
	//SEO information
		$SEOData = $db->query("SELECT * FROM `seo`");
		$SEOMeta = "";
		
		while($data = $db->fetch($SEOData)) {
			$SEOMeta .= $data['meta'] . "\n";
		}
		
		$this->headers = "<base href=\"" . ROOT . "\">
<meta charset=\"UTF-8\">
<meta name=\"robots\" content=\"index,follow\">
<meta name=\"googlebot\" content=\"index,follow\">
" . $SEOMeta . "
<link rel=\"shortcut icon\" href=\"" . ROOT . "system/images/favicon.ico\">
<link rel=\"stylesheet\" href=\"" . ROOT . "system/stylesheets/superpackage.desktop.css\">
<script src=\"" . ROOT . "system/javascripts/superpackage.desktop.js\"></script>
";
	}
	
//Import the template beginning
	public function top() {
		global $config;
		
		$lang = $this->lang;
		$title = $this->title;
		$headers = $this->headers;
		$logo = $this->logo;
		$templateRoot = $this->templateRoot;
		$siteName = $this->name;
		$slogan = $this->slogan;
		$homePage = $this->homePage;
		$breadCrumb = $this->breadCrumb;
		
		require_once($config->installRoot . "system/templates/desktop/" . $this->template . "/top.php");
	}
	
//Import the template ending
	public function bottom() {
		global $config;
		
		require_once($config->installRoot . "system/templates/" . $this->template . "/bottom.php");
	}
}
	
//Instantiate the template class
	$template = new TemplateAdmin();
?>