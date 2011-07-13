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
 * @package    templates
 * @copyright  Copyright (c) 2011 and Onwards, ForwardFour Innovations
 * @license    http://forwardfour.com/license    [Proprietary/Closed Source]  
 */

/**
 * This class is used to introduce dynamic content to each of the static HTML 
 * templates. Although this class is never instantiated directly, its
 * subclasses are instantiated, and are significantly easier to create when 
 * they extend this class.
 * 
 * @category   Core
 * @package    templates
 * @since      v0.1 Dev
 */

class TemplateBase {
//These tags are required to be customized by the user on a page-by-page basis
/**
 * Set the title of the page
 * 
 * @var        string
 */
	public $title;
	
/**
 * Add a by-line to the template, if one is supported
 * 
 * @var        string
 */
	public $byLine;
	
//These tags are not required to be customized by the user, but offer control over what external scripts are loaded in the <head> of each page
/**
 * Additional HTML to place in the <head> tag of page page, such as additional
 * scripts
 * 
 * @var        string
 */
	public $headerScripts;

/**
 * Whether or not the page should contain the JavaScript superpackage
 * 
 * @var        boolean
 */
	public $JSSuperPackage = true;

//These tags will be populated by the sever for use within the template itself
/**
 * Set the language of the page for use within the HTML DOM
 * 
 * @var        string
 */
	protected $lang = " lang=\"en-US\"";
	
/**
 * The name of the site
 * 
 * @var        string
 */
	protected $siteName;
	
/**
 * The slogan or "by-line" of the site
 * 
 * @var        string
 */
	protected $slogan;
	
/**
 * The current template being used for a particular page
 * 
 * @var        string
 */
	protected $template;
	
/**
 * The URL for the logo
 * 
 * @var        string
 */
	protected $logo;
	
/**
 * Set the root URL of the application's installation, with respect to the local
 * system
 * 
 * @var        string
 */
	protected $systemRoot = ROOT;
	
/**
 * The root URL of the template's installation, with respect to the local system
 * 
 * @var        string
 */
	protected $templateRoot;
	
/**
 * Set the root URL of the application's installation, with respect to the CDN
 * 
 * @var        string
 */
	protected $CDNRoot = CDN_ROOT;
	
/**
 * The root URL of the template's installation, with respect to the CDN
 * 
 * @var        string
 */
	protected $CDNTemplateRoot;
	
/**
 * Whether or not the current page is the home page for the entire site
 * 
 * @var        boolean
 */
	protected $homePage = false;
	
/**
 * The standard set of HTML tags to place in the <head> section
 * 
 * @var        string
 */
	protected $headers;
	
/**
 * The TemplateBase constructor method, which will assign values to the
 * server-assigned variables, which were defined above
 * 
 * @return     void
 * @since      v0.1 Dev
 */
	public function __construct() {
		global $db;
		
	//Assign the textual values, such as the name and footer infomation, which a user will see
		$siteData = $db->quick("SELECT * FROM `config` WHERE `id` = '1'");
		$this->siteName = $siteData['name'];
		$this->slogan = $siteData['slogan'];
		$this->footer = $siteData['footer'];
		
	//Template information
		$templateData = $db->quick("SELECT * FROM `template-admin` WHERE `selected` = '1'");		
		$this->template = $templateData['name'];
		
	//Grab the logo URL
		$logoGrabber = glob(INSTALL_ROOT . "system/images/logo.{png,jpg,jpeg,gif,bmp}", GLOB_BRACE);
		$logoInfo = pathinfo($logoGrabber[0]);
		$this->logo = ROOT . "system/images/" . $logoInfo['basename'];
		
	//Generate URLs
		$this->templateRoot = ROOT . "system/templates/desktop/" . $this->template . "/";
		$this->CDNTemplateRoot = CDN_ROOT . "system/templates/desktop/" . $this->template . "/";
		
	//Generate SEO information
		$SEOData = $db->query("SELECT * FROM `seo`");
		$SEOMeta = "";
		
		while($data = $db->fetch($SEOData)) {
			$SEOMeta .= $data['meta'] . "\n";
		}
		
	//Generate the HTML tags to go into the <head> section
		$superPackage = $this->JSSuperPackage ? "
<script src=\"" . STATIC_ROOT . "system/javascripts/superpackage.desktop.js\"></script>" : "";
		
		$this->headers = "<base href=\"" . ROOT . "\">
<meta charset=\"UTF-8\">
<meta name=\"robots\" content=\"index,follow\">
<meta name=\"googlebot\" content=\"index,follow\">
" . $SEOMeta . "
<link rel=\"shortcut icon\" href=\"" . ROOT . "system/images/favicon.ico\">
<link rel=\"stylesheet\" href=\"" . STATIC_ROOT . "system/stylesheets/superpackage.desktop.css\">" . 
$superPackage .  
$this->headerScripts . "
";
	}
}