<?php
/*
 * This class will used to introduce dynamic content to each of the static HTML 
 * administration templates:
 *  - __construct: Assign the dynamic variables for the tempate
 *  - top: Import and build the template's beginning
 *  - bottom: Import and build the template's ending
*/

class Setup {
//Defined from script
	private $name;
	private $headers;

//Defined on as-needed basis
	public $title;

//Assign the dynamic variables for the tempate
	public function __construct() {
	//The name of this module
		$this->name = "Setup Wizard";
		
	//Meta information
		$this->headers = "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
<meta http-equiv=\"content-language\" content=\"en\" />
<meta name=\"resource-type\" content=\"document\" />

<meta name=\"robots\" content=\"noindex, nofollow\" />
<meta name=\"googlebot\" content=\"noindex, nofollow\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"../system/stylesheets/universal.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"../system/stylesheets/jquery-ui.min.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"../system/stylesheets/uploadify.css\" />
<script type=\"text/javascript\" src=\"../system/javascripts/swfobject.js\"></script>
<script type=\"text/javascript\" src=\"../system/javascripts/jquery.min.js\"></script>
<script type=\"text/javascript\" src=\"../system/javascripts/jquery-ui.min.js\"></script>
<script type=\"text/javascript\" src=\"../system/javascripts/global.jquery.js\"></script>
<script type=\"text/javascript\" src=\"../system/javascripts/analog.jquery.js\"></script>
<script type=\"text/javascript\" src=\"../system/javascripts/funtip.jquery.js\"></script>
<script type=\"text/javascript\" src=\"../system/javascripts/uploadify.jquery.min.js\"></script>
<script type=\"text/javascript\" src=\"system/javascripts/install.jquery.js\"></script>
";
	}

//Import and build the template's beginning
	public function top() {
		global $scriptRoot;
			
		$name = $this->name;
		$title = $this->title;
		$headers = $this->headers;
			
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