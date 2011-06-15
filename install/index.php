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
	require_once("../system/server/files/FileManipulate.php");
	require_once("../system/server/files/FileMisc.php");
	require_once("../system/server/files/Mime.php");
	require_once("../system/server/files/Upload.php");
	
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
		
	//Monitor validation data
		$_SESSION['installer']['validation'] = array();
	}
	
//Test to see if the supplied values allow access to the database
	if ($_SESSION['installer']['step'] == "database" && isset($_POST['test'])) {
		$dbHost = Validate::required($_POST['dbHost']);
		$dbPort = Validate::numeric($_POST['dbPort']);
		$dbUsername = Validate::required($_POST['dbUsername']);
		$dbPassword = $_POST['dbPassword'];
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
	
	//This is the database setup step
		case "database" :			
			$title = "Setup Database";
			$content = "<script type=\"text/javascript\">
  $(document).ready(function() {
    $('.progress').animate({
      width : '25%'
    }, 2000);
  });
</script>

<p>Progress: <span class=\"progressContainer\"><span class=\"progress\" style=\"width: 0%;\">&nbsp;</span></span></p>
<br />
<p>This is the most essential step for getting your website up and running. Here, you are asked to fill in the credential for the system to access a database.
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
<td width=\"300\"><p align=\"right\">Database connection URL<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbHost\" class=\"dbHost required\" value=\"localhost\" id='{\"standard\" : \"Please provide a host URL\", \"required\" : \"A host URL is required\"}' /></td>
</tr>
<tr>
<td width=\"300\"><p align=\"right\">Database connection port<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbPort\" class=\"dbPort required numeric\" value=\"3306\" id='{\"standard\" : \"Please provide a host port\", \"required\" : \"A host port is required\", \"error\" : \"A numeric value is required\"}' /></td>
</tr>
<tr>
<td width=\"300\">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td width=\"300\"><p align=\"right\">Database username<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbUsername\" class=\"dbUsername required\" id='{\"standard\" : \"Please provide a username\", \"required\" : \"A username is required\"}' /></td>
</tr>
<tr>
<td width=\"300\"><p align=\"right\">Database password:</p></td>
<td><input type=\"password\" name=\"dbPassword\" class=\"dbPassword\" id='{\"standard\" : \"Passwords may be optional\"}' /></td>
</tr>
<tr>
<td width=\"300\"><p align=\"right\">Database name<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbName\" class=\"dbName required\" id='{\"standard\" : \"Provide a database name\", \"required\" : \"A database name is required\"}' /></td>
</tr>
</tbody>
</table>
<br />
<br />

<input type=\"button\" name=\"dbTest\" id=\"dbTest\" value=\"Test connection\" />
<input type=\"submit\" name=\"dbSubmit\" id=\"dbContinue\" value=\"Continue\" disabled=\"disabled\" />
</form>";
			
			break;
			
	//This is the site information setup step
		case "siteInfo" : 
			$title = "Site Information Setup";
			$content = "<script type=\"text/javascript\">
  $(document).ready(function() {
    $('.progress').animate({
      width : '50%'
    }, 2000);
  });
</script>
<p>Progress: <span class=\"progressContainer\"><span class=\"progress\" style=\"width: 0%;\">&nbsp;</span></span></p>
<br />
<p>Now, the most critical and difficult part of the setup has been completed. For this step, begin setting up your site by providing details, such as the site's name, slogan, logo, and other customization options.</p>
<p>&nbsp;</p>
<p><span class=\"require\">*</span> indicates required field</p>

<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\" enctype=\"multipart/form-data\">
<table width=\"100%\">
<tbody>
<tr>
<td width=\"300\"><p align=\"right\">Site name<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"name\" class=\"name required\" id='{\"standard\" : \"This will show in the title\"}' /></td>
</tr>
<tr>
<td width=\"300\"><p align=\"right\">Slogan:</p></td>
<td><input type=\"text\" name=\"slogan\" class=\"slogan\" id='{\"standard\" : \"This will show in the header\"}' /></td>
</tr>
<tr>
<td width=\"300\" class=\"uploadifyLabel\"><p align=\"right\">Logo<span class=\"require\">*</span>:</p></td>
<td>
<script type=\"text/javascript\">
  $(document).ready(function() {
    $('#logo').uploadify();
  });
</script>
<input type=\"file\" name=\"logo\" id=\"logo\" />
</td>
</tr>
<tr>
<td width=\"300\"><p align=\"right\">Footer text<span class=\"require\">*</span>:</p></td>
<td><textarea name=\"footer\" class=\"footer required\" id='{\"standard\" : \"This will show in the footer\"}'></textarea></td>
</tr>
</tbody>
</table>
<br />
<br />

<input type=\"submit\" name=\"infoSubmit\" id=\"infoContinue\" value=\"Continue\" />
</form>";
			
			break;
	}
	
//Since Flash does not obtain the session ID from the browser, then compensate below
	if (isset($_POST['sessionID'])) {
		$session = "siteInfo";
//Browsers will revert to this
	} else {
		$session = $_SESSION['installer']['step'];
	}
	
//Process the incomming form data
	switch($session) {
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
				$installRoot = strstr(dirname(__FILE__), "\\") ? str_replace("\install", "", dirname(__FILE__)) . "\\" : str_replace("/install", "", dirname(__FILE__)) . "/";
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
		public \$dbUsername = \"" . $dbUsername . "\";
		public \$dbPassword = \"" . $dbPassword . "\";
		public \$dbName = \"" . $dbName . "\";
		
		public \$installDomain = \"" . $installDomain . "\";
		public \$installRoot = \"" . addslashes($installRoot) . "\";
		
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
				
			//Create the pseudo "robots.txt" file
				$file = $installRoot . "robots.php";
				$contents = "<?php
//Include the system core and classes
	require_once(\"system/server/index.php\");
	
//Output as a text file
	header(\"Content-type:text/plain\");
?>
User-agent: *
Disallow: 
Sitemap: <?php echo ROOT; ?>sitemap.xml";
				
				FileManipulate::write($file, $contents);
				
			//Create the pseudo "sitemap.xml" file
				$file = $installRoot . "sitemap.php";
				$contents = "<?php
//Include the system core and classes
	require_once(\"system/server/index.php\");
	
//Output as an XML file
	header(\"Content-type:text/xml\");
	
//The question marks are confusing the PHP server, so echo it manually
	echo \"<?xml version=\\\"1.0\\\" encoding=\\\"utf-8\\\"?>\\n\";
?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
<?php
//Grab all of the dynamically created pages
	\$pages = \$db->query(\"SELECT * FROM `pages`\", \"raw\");
	
	while(\$page = \$db->fetch(\$pages)) {
		\" <url>
  <loc>\" . ROOT . \"page.php</loc>
  <priority>1.000</priority>
 </url>\\n\";
	}
?>
</urlset>";
				
				FileManipulate::write($file, $contents);
				
			//Include the system super-core, now that the configuration file has been created
				require_once("../system/server/index.php");
				
			//Install the database tables
				if (strstr(dirname(__FILE__), "\\")) {
					$file = $installRoot . "install\database.sql";
				} else {
					$file = $installRoot . "install/database.sql";
				}
				
				foreach(FileManipulate::read($file, ";") as $query) {
					if (!empty($query)) {
						$db->query($query);
					}
				}
				
			//Redirect to next step
				$_SESSION['installer']['step'] = "siteInfo";
				Misc::redirect($_SERVER['PHP_SELF']);
			}
			
			break;
		
	//This is the site information form
		case "siteInfo" : 
		//Listen for a logo upload
			if (isset($_FILES['logo']['size']) && $_FILES['logo']['size'] > 0) {
				$uploader = new Upload();
				$uploader->fileField = "logo";
				$uploader->directory = "../system/images";
				$uploader->required = true;
				$uploader->addHashSuffix = false;
				$uploader->renameTo = "logo";
				$uploader->allowedExt = array("jpg", "bmp", "png", "gif", "jpeg");
				$result = $uploader->process();
				
			//A logo and browser icon are required, so track each of them in an array, since they can't be uploaded all at once
				$_SESSION['installer']['validation'][] = "logo";
				
				echo json_encode($result);
				exit;
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