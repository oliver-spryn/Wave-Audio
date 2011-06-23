<?php
/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * This class will used to introduce dynamic content to each of the static HTML 
 * installation templates:
 *  - __construct: Assign the dynamic variables for the template
 *  - top: Import and build the template's beginning
 *  - bottom: Import and build the template's ending
*/

class Setup {
//Defined from script
	private $name;
	private $headers;
	private $lang;

//Defined on as-needed basis
	public $title;

//Assign the dynamic variables for the tempate
	public function __construct() {
	//The name of this module
		$this->name = "Setup Wizard";
		
	//Meta information, styles, and JavaScripts
		$this->headers = "<meta charset=\"UTF-8\">
<meta name=\"robots\" content=\"noindex, nofollow\">
<meta name=\"googlebot\" content=\"noindex, nofollow\">

<link rel=\"stylesheet\" href=\"../system/stylesheets/superpackage.desktop.css\" />
<link rel=\"stylesheet\" href=\"system/stylesheets/style.min.css\" />
<script src=\"../system/javascripts/superpackage.desktop.js\"></script>
<script src=\"system/javascripts/install.jquery.min.js\"></script>
<script src=\"../system/javascripts/swfobject.js\"></script>
<script src=\"../system/javascripts/uploadify.jquery.js\"></script>
";
		
	//The document language
		$this->lang = " lang=\"en-US\"";
	}

//Import and build the template's beginning
	public function top() {
		global $scriptRoot;
			
		$name = $this->name;
		$title = $this->title;
		$headers = $this->headers;
		$lang = $this->lang;
			
		require_once($scriptRoot . "system/templates/top.php");
	}

//Import and build the template's ending
	public function bottom() {
		global $scriptRoot;
			
		require_once($scriptRoot . "system/templates/bottom.php");
	}
}
	
//Instantiate the template class
	$template = new Setup();
?>