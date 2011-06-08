<?php
/*
 * This is the application installer wizard. Each step is managed by a series of $_SESSIONs.
 * 
 * Since this portion of the application cannot connect to the database, or use the system's
 * super-core in any way, it is completely dependant on the files and classes within this module.
*/

//Include the module core
	require_once("system/server/index.php");
	
//Include other non-database dependant classes
	require_once("../system/server/core/Validate.php");
	require_once("../system/server/misc/Misc.php");
	require_once("../system/server/misc/FileManipulate.php");
	
//First-run initialization
	if (!isset($_SESSION['installer'])) {
		$_SESSION['installer'] = array();
		
	//Monitor the user's step
		$_SESSION['installer']['step'] = "welcome";
		
	//Gather the entered data for building configuration scripts later
		$_SESSION['installer']['data'] = array(
			"dbHost" => "",
			"dbPort" => "",
			"dbUsername" => "",
			"dbPassword" => "",
			"dbName" => "",
			
			"installDomain" => "",
			"installRoot" => "",
			
			"encryptedSalt" => "",
			"sessionSuffix" => ""
		);
	}
	
//Test to see if the supplied values allow access to the database
	if ($_SESSION['installer']['step'] == "database" && isset($_POST['test'])) {
		$dbHost = Validate::required($_POST['dbHost']);
		$dbPort = Validate::numeric($_POST['dbPort']);
		$dbUsername = Validate::required($_POST['dbUsername']);
		$dbPassword = Validate::required($_POST['dbPassword']);
		$dbName = Validate::required($_POST['dbName']);
		
		if (mysql_connect($dbHost . ":" . $dbPort, $dbUsername, $dbPassword) && mysql_select_db($dbName)) {
			echo "success";
		} else {
			echo "failure";
		}
		
		exit;
	}
	
//Build the wizard's content based on the step indicated by the $_SESSION
	$title;
	$content;
	
	switch($_SESSION['installer']['step']) {
	//This is the welcome step
		case "welcome" :
			$title = "Welcome";
			$content = "<div style=\"width:75%; padding-left:12.5%; text-align:center;\">
<p>Welcome to the Content Management System setup wizard. These series of steps will guide you through the process of setting up your website's management system.</p>
<br /><br />
<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">
<input type=\"submit\" name=\"welcomeSubmit\" value=\"Continue\" />
</form>
</div>";
			
			break;
			
		case "database" :			
			$title = "Setup Database";
			$content = "<p>This is the most essential step for getting your website up and running. Here, you are asked to fill in the credential for the system to access a database.
<br /><br />
You will need to log into your hosting provider's control panel, and create a MySQL database. MySQL is the most popular database technology used on the web, and therefore, it is very likely that your hosting provider will give you tools to create a MySQL database. If you have access to multiple database types, such as PostgreSQL, Oracle, Microsoft SQL, etc..., make sure you <strong>ONLY</strong> setup a MySQL database. This system will not work with any other database technology.
<br /><br />
While you are setting up the database, make sure you write down the username and password you created for the database. You will only need to enter them one time into the &quot;Database username&quot; and &quot;Database password&quot; fields below.
<br /><br />
<strong>Please be aware that you are not creating a user account at this point. This step is strictly for the system to access a database.</strong>
</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">
<table width=\"100%\">
<tbody>
<tr>
<td width=\"48%\"><p align=\"right\">Database connection URL:</p></td>
<td width=\"4%\">&nbsp;</td>
<td width=\"48%\"><input type=\"text\" name=\"dbHost\" class=\"dbHost\" value=\"localhost\" /></td>
</tr>
<tr>
<td width=\"48%\"><p align=\"right\">Database connection port:</p></td>
<td width=\"4%\">&nbsp;</td>
<td width=\"48%\"><input type=\"text\" name=\"dbPort\" class=\"dbPort\" value=\"3306\" /></td>
</tr>
<tr>
<td width=\"48%\">&nbsp;</td>
<td width=\"4%\">&nbsp;</td>
<td width=\"48%\">&nbsp;</td>
</tr>
<tr>
<td width=\"48%\"><p align=\"right\">Database username:</p></td>
<td width=\"4%\">&nbsp;</td>
<td width=\"48%\"><input type=\"text\" name=\"dbUsername\" class=\"dbUsername\" /></td>
</tr>
<tr>
<td width=\"48%\"><p align=\"right\">Database password:</p></td>
<td width=\"4%\">&nbsp;</td>
<td width=\"48%\"><input type=\"password\" name=\"dbPassword\" class=\"dbPassword\" /></td>
</tr>
<tr>
<td width=\"48%\"><p align=\"right\">Database name:</p></td>
<td width=\"4%\">&nbsp;</td>
<td width=\"48%\"><input type=\"text\" name=\"dbName\" class=\"dbName\" /></td>
</tr>
</tbody>
</table>
<br />
<br />

<input type=\"button\" name=\"dbTest\" id=\"dbTest\" value=\"Test connection\" />
<input type=\"submit\" name=\"dbSubmit\" id=\"dbContinue\" value=\"Continue\" disabled=\"disabled\" />
</form>";
			
			break;
	}
	
//Process the incomming form data
	switch($_SESSION['installer']['step']) {
	//This is the welcome form
		case "welcome" :
			if (isset($_POST['welcomeSubmit'])) {
			//Redirect to next step
				$_SESSION['installer']['step'] = "database";
				Misc::redirect($_SERVER['PHP_SELF']);
			}
			
			break;
			
	//This is the database form	
		case "database" : 
			if (isset($_POST['dbSubmit'])) {
			//These values are assigned by the user
				$dbHost = Validate::required($_POST['dbHost']);
				$dbPort = Validate::numeric($_POST['dbPort']);
				$dbUsername = Validate::required($_POST['dbUsername']);
				$dbPassword = Validate::required($_POST['dbPassword']);
				$dbName = Validate::required($_POST['dbName']);
				
			//Double-check to see if the supplied values are correct
				if (mysql_connect($dbHost . ":" . $dbPort, $dbUsername, $dbPassword) && mysql_select_db($dbName)) {
					//Everything is good
				} else {
					Misc::redirect($_SERVER['PHP_SELF']);
				}
				
			//These values are automatically generated
				$installAbsolute = str_replace("/install", "", dirname($_SERVER['PHP_SELF'])) . "/";
				$installDomain = $_SERVER['HTTP_HOST'] . $installAbsolute;
				$installRoot = strstr(dirname(__FILE__), "\\") ? str_replace("\install", "", dirname(__FILE__)) . "\\\\" : str_replace("/install", "", dirname(__FILE__)) . "/";
				$encryptedSalt = Misc::randomValue(50);
				$sessionSuffix = Misc::randomValue(10, "alphaNumeric");
				
			//Create the configuration file
				if (strstr(dirname(__FILE__), "\\")) {
					$file = $installRoot . "data\system\config.php";
				} else {
					$file = $installRoot . "data/system/config.php";
				}
				
				$contents = "<?php
//This script is created during the automated setup process, and contains the core configuration and definitions of the system, which will be used globally.

//Define the configuration class
	class Config {
		public \$dbHost = \"" . $dbHost . "\";
		public \$dbPort = \"" . $dbPort . "\";
		public \$dbUsername = \"" . dbUsername . "\";
		public \$dbPassword = \"" . $dbPassword . "\";
		public \$dbName = \"" . $dbName . "\";
		
		public \$installDomain = \"" . $installDomain . "\";
		public \$installRoot = \"" . $installRoot . "\";
		
		public \$encryptedSalt = \"" . $encryptedSalt . "\";
		public \$sessionSuffix = \"" . $sessionSuffix . "\";
	}
?>";
				
				FileManipulate::write($file, $contents);
				
			//Create the core .htaccess file
				$file = $installRoot . ".htaccess";
				$contents = "# Allow access to all PHP files
<Files ~ \"\.(php)\$\">
	order allow,deny
	allow from all
</Files>

# Deny access to all .htaccess files
<Files ~ \"\.(htaccess)\$\">
	order deny,allow
	deny from all
</Files>

# ErrorDocument handlers
ErrorDocument 403 " . $installAbsolute . "system/server/apache/index.php?error=403
ErrorDocument 404 " . $installAbsolute . "system/server/apache/index.php?error=404

<IfModule mod_rewrite.c>
	RewriteEngine On
	
# Direct to the dynamically created robots.txt file
	Options +FollowSymlinks
	RewriteRule ^robots.txt$ robots.php [QSA,L] 
	
# Direct to the dynamically created sitemap.xml file
	Options +FollowSymlinks
	RewriteRule ^sitemap.xml$ sitemap.php [QSA,L] 
	
# Create SEO friendly URLs
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
	Options -Indexes
</IfModule>";
				
				FileManipulate::write($file, $contents);
				
			//Redirect to next step
				$_SESSION['installer']['step'] = "database";
				Misc::redirect($_SERVER['PHP_SELF']);
			}
			
			break;
	}
	
//The top of the page
	$template->title = $title;
	$template->top();
	
//The content of the page
	echo $content;
	
//The bottom of the page
	$template->bottom();
?>