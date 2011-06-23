<?php
/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
 * 
 * This is the application installer wizard. Each step is managed by a $_SESSION.
 * 
 * Since some parts of this application cannot connect to the database, or use the system's
 * super-core in any way, it is mostly dependant on the files and classes within this module,
 * and several non-database dependant classes within the system's core.
 * 
 * The first part of the script includes initialization and database connection tests. The 
 * initialization creates the session which will track the user's current step.
 * 
 * Further down is a large switch statement which displays content and form input controls 
 * based on the user's current step.
 * 
 * Beneath that is the processor which will handle and process the data to setup the site
 * based on the given input. Perhaps the most important step is processor step two, which 
 * creates the configuration file and core .htaccess file.
 * 
 * At the bottom of this page is where the generated content is echoed to the browser.
*/

//Include the module core
	require_once("system/server/index.php");
	
//First-run initialization
	if (!isset($_SESSION['installer'])) {
	//Monitor the user's step
		$_SESSION['installer'] = "welcome";
	}
	
//Test to see if the supplied values allow access to the database
	if ($_SESSION['installer'] == "database" && isset($_POST['test'])) {
		$dbHost = Validate::required($_POST['dbHost']);
		$dbPort = Validate::numeric($_POST['dbPort']);
		$dbUsername = Validate::required($_POST['dbUsername']);
		$dbPassword = $_POST['dbPassword'];
		$dbName = Validate::required($_POST['dbName']);
		
		$testConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);
		echo $testConnection->connect_error ? "failure" : "success";
		
		exit;
	}
	
//Build the wizard's content based on the step indicated by the $_SESSION
	switch($_SESSION['installer']) {
	//This is the welcome step
		case "welcome" :
			$title = "Welcome";
			$content = "<div class=\"welcome\">
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
			$content = "<section class=\"progress\">
<script>
  $(document).ready(function() {
    $('.progressBar').animate({
      width : '25%'
    }, 2000);
  });
</script>

<header>
<h3>Progress</h3>
</header>

<p><progress value=\"25\" max=\"100\" class=\"progressContainer\"><span class=\"progressBar\">&nbsp;</span></progress></p>
</section>
<br />

<section class=\"directions\">
<header>
<h3>Directions</h3>
</header>

<p>This is the most essential step for getting your website up and running. Here, you are asked to fill in the credential for the system to access a database.
<br /><br />
You will need to log into your hosting provider's control panel, and create a MySQL database. MySQL is the most popular database technology used on the web, and therefore, it is very likely that your hosting provider will give you tools to create a MySQL database. If you have access to multiple database types, such as PostgreSQL, Oracle, Microsoft SQL, etc..., make sure you <strong>ONLY</strong> setup a MySQL database. This system will not work with any other database technology.
<br /><br />
While you are setting up the database, make sure you write down the username and password you created for the database. You will only need to enter them one time into the &quot;Database username&quot; and &quot;Database password&quot; fields below.
<br /><br />
<strong>Please be aware that you are not creating a user account at this point. This step is strictly for the system to access a database.</strong>
</p>
</section>
<p>&nbsp;</p>
<p>&nbsp;</p>

<section class=\"form\">
<header>
<h3>Database Connection</h3>
</header>

<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">
<table>
<tbody>
<tr>
<td class=\"details\"><p class=\"right\">Database connection URL<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbHost\" class=\"dbHost\" required=\"required\" value=\"localhost\" title='{\"standard\" : \"Please provide a host URL\", \"required\" : \"A host URL is required\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Database connection port<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbPort\" class=\"dbPort numeric\" required=\"required\" value=\"3306\" title='{\"standard\" : \"Please provide a host port\", \"required\" : \"A host port is required\", \"error\" : \"A numeric value is required\"}' /></td>
</tr>
<tr>
<td class=\"details\">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Database username<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbUsername\" class=\"dbUsername\" required=\"required\" title='{\"standard\" : \"Please provide a username\", \"required\" : \"A username is required\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Database password:</p></td>
<td><input type=\"password\" name=\"dbPassword\" class=\"dbPassword\" title='{\"standard\" : \"Passwords may be optional\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Database name<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"dbName\" class=\"dbName\" required=\"required\" title='{\"standard\" : \"Provide a database name\", \"required\" : \"A database name is required\"}' /></td>
</tr>
</tbody>
</table>
<br />
<br />

<input type=\"button\" name=\"dbTest\" id=\"dbTest\" value=\"Test connection\" />
<input type=\"submit\" name=\"dbSubmit\" id=\"dbContinue\" value=\"Continue\" disabled=\"disabled\" />
</form>
</section>";
			
			break;
			
	//This is the site information setup step
		case "siteInfo" : 
			$title = "Site Information Setup";
			$content = "<section class=\"progress\">
<script>
  $(document).ready(function() {
    $('.progressBar').animate({
      width : '50%'
    }, 2000);
  });
</script>

<header>
<h3>Progress</h3>
</header>

<p><progress value=\"50\" max=\"100\" class=\"progressContainer\"><span class=\"progressBar\">&nbsp;</span></progress></p>
</section>
<br />

<section class=\"directions\">
<header>
<h3>Directions</h3>
</header>

<p>Now, the most critical and difficult part of the setup has been completed. For this step, begin setting up your site by providing details, such as the site's name, slogan, logo, and other customization options.</p>
<p>&nbsp;</p>
<p><span class=\"require\">*</span> indicates required field</p>
<br />
</section>

<article class=\"form\">
<header>
<h3>Web Layout Setup</h3>
</header>

<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\" enctype=\"multipart/form-data\">
<section class=\"layout\">
<header class=\"tableDivider\">
<h3>Page Setup</h3>
</header>

<table>
<tbody>
<tr>
<td class=\"details\"><p class=\"right\">Site name<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"name\" required=\"required\" title='{\"standard\" : \"This will show in the title\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Slogan:</p></td>
<td><input type=\"text\" name=\"slogan\" title='{\"standard\" : \"This will show in the header\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Footer text<span class=\"require\">*</span>:</p></td>
<td><textarea name=\"footer\" required=\"required\" title='{\"standard\" : \"This will show in the footer\"}'></textarea></td>
</tr>
</tbody>
</table>
</section>
<br />

<section class=\"seo\">
<header class=\"tableDivider\">
<h3>Search Engine Optimization</h3>
</header>

<table>
<tbody>
<tr>
<td class=\"details\"><p class=\"right\">Site author or publishing organization<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"author\" required=\"required\" /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Copyright statement<span class=\"require\">*</span>:</p></td>
<td><textarea name=\"copyright\" required=\"required\" title='{\"standard\" : \"Protect your content!\"}'></textarea></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Search engine keywords<span class=\"require\">*</span>:</p></td>
<td><textarea name=\"keywords\" required=\"required\" title='{\"standard\" : \"Seperate with comma and space\"}'></textarea></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Site description<span class=\"require\">*</span>:</p></td>
<td><textarea name=\"description\" required=\"required\" title='{\"standard\" : \"What is your site about?\"}'></textarea></td>
</tr>
</tbody>
</table>
</section>
<br />

<section class=\"final\">
<header class=\"tableDivider\">
<h3>Final Touches</h3>
</header>

<script>
  $(document).ready(function() {
    $('#logo').uploadify({
      'fileExt' : '*.jpg;*.bmp;*.png;*.gif;*.jpeg',
      'fileDesc' : 'Image Files (jpg, bmp, png, gif, jpeg)'
    });
    
    $('#icon').uploadify({
      'fileExt' : '*.ico',
      'fileDesc' : 'Icons (ico)'
    });
    
     $('#flash').uploadify({
      'fileExt' : '*.zip',
      'fileDesc' : 'Zipped Folder with Flash Content (zip)',
      'required' : false,
      'showLinkOnComplete' : false
    });
  });
</script>

<table>
<tbody>
<tr>
<td class=\"details uploadifyLabel\"><p class=\"right\">Logo<span class=\"require\">*</span>:</p></td>
<td><input type=\"file\" name=\"logo\" id=\"logo\" /></td>
</tr>
<tr>
<td class=\"details uploadifyLabel\"><p class=\"right\">Browser favicon<span class=\"require\">*</span>:</p></td>
<td><input type=\"file\" name=\"icon\" id=\"icon\" /></td>
</tr>
<tr>
<td class=\"details uploadifyLabel\"><p class=\"right\">Flash &reg; content overlay ZIP file:</p></td>
<td><input type=\"file\" name=\"flash\" id=\"flash\" /></td>
</tr>
</tbody>
</table>
</section>
<br />
<br />

<input type=\"submit\" name=\"infoSubmit\" value=\"Continue\" />
</form>
</article>";
			
			break;
			
	//This is the site information setup step
		case "userInfo" : 
			$title = "Your Account Setup";
			$content = "<section class=\"progress\">
<script>
  $(document).ready(function() {
    $('.progressBar').animate({
      width : '75%'
    }, 2000);
  });
</script>

<header>
<h3>Progress</h3>
</header>

<p><progress value=\"75\" max=\"100\" class=\"progressContainer\"><span class=\"progressBar\">&nbsp;</span></progress></p>
</section>
<br />

<section class=\"directions\">
<header>
<h3>Directions</h3>
</header>

<p>Now that the site has been setup, it is time to create your user account. Fill out the required fields below to complete the last step.</p>
</section>
<p>&nbsp;</p>
<p>&nbsp;</p>

<section class=\"directions\">
<header>
<h3>User Account Setup</h3>
</header>

<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">
<table>
<tbody>
<tr>
<td class=\"details\"><p class=\"right\">Your first name<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"first\" required=\"required\" /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Your last name<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"last\" required=\"required\" /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Your username<span class=\"require\">*</span>:</p></td>
<td><input type=\"text\" name=\"username\" required=\"required\" title='{\"standard\" : \"Pick one you can rememeber\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Your password<span class=\"require\">*</span>:</p></td>
<td><input type=\"password\" name=\"password\" class=\"min[8]\" required=\"required\" title='{\"standard\" : \"Be tricky!\", \"error\" : \"Use at least 8 characters\"}' /></td>
</tr>
<tr>
<td class=\"details\">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Primary email address<span class=\"require\">*</span>:</p></td>
<td><input type=\"email\" name=\"emailAddress1\" required=\"required\" title='{\"standard\" : \"Use one you check often\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Seconday email address:</p></td>
<td><input type=\"email\" name=\"emailAddress2\" title='{\"standard\" : \"Just as a backup\"}' /></td>
</tr>
<tr>
<td class=\"details\"><p class=\"right\">Tertiary email address:</p></td>
<td><input type=\"email\" name=\"emailAddress3\" title='{\"standard\" : \"Just as a backup\"}' /></td>
</tr>
</tbody>
</table>
<br />
<br />

<input type=\"submit\" name=\"userSubmit\" value=\"Continue\" />
</form>
</section>";
			break;
			
	//This is the final step
		case "finish" :
			$title = "Finished!";
			$content = "<p class=\"center\">Your site has been setup and is ready to go! Wasn't that easy? Click &quot;Finish&quot; to access your site.</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">
<div class=\"center\">
<input type=\"submit\" name=\"finishSubmit\" value=\"Finish!\" />
</div>
</form>";
			
			break;
	}
	
//Since Flash does not obtain the session ID from the browser, then compensate below for file upload handling
	if (isset($_POST['sessionID'])) {
		$session = "siteInfo";
//Browsers will revert to this
	} else {
		$session = $_SESSION['installer'];
	}
	
//Process the incomming form data
	switch($session) {
	//This is the welcome form
		case "welcome" :
			if (isset($_POST['welcomeSubmit'])) {
			//Redirect to next step
				$_SESSION['installer'] = "database";
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
				$dbPassword = $_POST['dbPassword'];
				$dbName = Validate::required($_POST['dbName']);
				
			//Double-check to see if the supplied values are correct
				$testConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);
				
				if (!$testConnection->connect_error) {
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
/*
 * ******************************************************************
 * DO NOT MODIFY THIS FILE UNLESS YOU ARE SURE OF WHAT YOU ARE DOING.
 * CHANGING THIS FILE COULD RESULT IN MAKING YOUR SITE UNOPERABLE.
 * ******************************************************************
 *
 * This script is created during the automated setup process, and contains the core
 * configuration and definitions of the system, which will be used globally.
 *
 * The first section of instance variables contain the connection to the database.
 *
 * The second set contain the domain and this application is installed at, as well
 * as its absolute path on the server.
 *
 * The final set of instance variables contain random hashes which are used to avoid
 * session conflicts and improve security.
*/ 

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
				$_SESSION['installer'] = "siteInfo";
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
				
				echo json_encode($result);
				exit;
			}
			
		//Listen for a favicon upload
			if (isset($_FILES['icon']['size']) && $_FILES['icon']['size'] > 0) {
				$uploader = new Upload();
				$uploader->fileField = "icon";
				$uploader->directory = "../system/images";
				$uploader->required = true;
				$uploader->addHashSuffix = false;
				$uploader->renameTo = "favicon";
				$uploader->allowedExt = array("ico");
				$result = $uploader->process();
				
				echo json_encode($result);
				exit;
			}
			
		//Listen for a flash content ZIP package upload
			if (isset($_FILES['flash']['size']) && $_FILES['flash']['size'] > 0) {
				//TO DO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				exit;
			}
			
		//Listen for main form submission
			if (isset($_POST['infoSubmit'])) {
			//Now that we have database access, we'll need it
				require_once("../system/server/index.php");
				
				$db->insert("INSERT INTO `config`", array(
					"id" => "1",
					"name" => Validate::required($_POST['name']),
					"slogan" => $_POST['slogan'],
					"footer" => Validate::required($_POST['footer']),
					"author" => Validate::required($_POST['author']),
					"copyright" => Validate::required($_POST['copyright']),
					"keywords" => Validate::required($_POST['keywords']),
					"description" => Validate::required($_POST['description']),
					"template" => "admin-skin"	
				));
				
			//Redirect to next step
				$_SESSION['installer'] = "userInfo";
				Misc::redirect($_SERVER['PHP_SELF']);
			}
			
			break;
			
	//This is the user information form
		case "userInfo" : 
			if (isset($_POST['userSubmit'])) {
			//Now that we have database access, we'll need it
				require_once("../system/server/index.php");
				
				$db->insert("INSERT INTO `users`", array(
					"id" => "1",
					"role" => "Administrator",
					"firstname" => Validate::required($_POST['first']),
					"lastname" => Validate::required($_POST['last']),
					"username" => Validate::required($_POST['username']),
					"password" => md5(Validate::required($_POST['password'], false, 8) . $config->encryptedSalt),
					"emailaddress1" => Validate::isEmail($_POST['emailAddress1']),
					"emailaddress2" => Validate::isEmail($_POST['emailAddress2'], true),
					"emailaddress3" => Validate::isEmail($_POST['emailAddress3'], true)
				));
				
			//Set the user's login session
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['role'] = "Administrator";
				
			//Redirect to next step
				$_SESSION['installer'] = "finish";
				Misc::redirect($_SERVER['PHP_SELF']);
			}
			
			break;
			
	//This is the final step
		case "finish" : 
			//TO DO: DELETE APPLICATION ON COMPLETE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			//Misc::redirect("../portal/index.php");
			break;
			
	//Just in case the session wasn't set and a form was submitted
		default :
		//Redirect to the same page
			$_SESSION['installer'] = "welcome";
			Misc::redirect($_SERVER['PHP_SELF']);
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