<?php
/*
 * By using this application, you are bound the license agreement set forth on
 * this page: http://docs.forwardfour.com/index.php/License
*/

//Include the system's core
	require_once("../system/server/index.php");
	import("templates.TemplateAdmin");
	
/*
Processors
---------------------------------------
*/

//Listen for requests from jQuery to build a table
	if (isset($_POST['action']) && $_POST['action'] == "tableFetch") {
	//Grab the pages for the current level, do not include any sub-pages
		$pages = $db->query("SELECT * FROM `pages` WHERE `parentID` = '0' ORDER BY `position` ASC");
		$counter = 1;
		
	//Build the table header
		$return = "<header>
<h1>Pages</h1>
</header>

<table class=\"dataTable\">
<thead>
<tr>
<th>&nbsp;</th>
<th>Title</th>
<th>Content Snapshot</th>
<th>Management</th>
</tr>
</thead>

<tbody>
";
		
	//Build each of the rows
		while ($result = $db->fetch($pages)) {
		//Generate zebra striping
			$rowClass = $counter & 1 ? "odd" : "even";
			$visibilityClass = $result['visibility'] == "1" ? "eyeShow" : "eyeHide";
			
		//Build the row
			$return .= "<tr class=\"" . $rowClass . "\" id=\"" . $result['id'] . "\" name=\"" . $result['position'] . "\">
<td class=\"center width50\"><a class=\"dragger\"></a><a class=\"visibilityTrigger " . $visibilityClass . "\"></a></td>
<td class=\"center width150\">" . $result['title'] . "</td>
<td>
<div class=\"clipContainer\">" . strip_tags($result['content']) . "</div>
<div class=\"hide contentContainer\">" . $result['content'] . "</div>
<div class=\"hide URLContainer\">" . $result['url'] . "</div>
</td>
<td class=\"center width75\"><a class=\"edit\"></a><a class=\"delete\"></a></td>
</tr>
";
			
		//Increment the counter for the next row
			$counter ++;
		}
		
	//Finish building the table
		$return .= "</tbody>
</table>";
		
		echo $return;
		
	//Don't continue building the rest of the page
		exit;
	}
	
//Reorder the data
	if (isset($_POST['action']) && $_POST['action'] == "reorder") {
	//Validate the incoming data
		$id = Validate::numeric($_POST['id']);
		$currentPosition = $db->escape(Validate::numeric($_POST['currentPosition'])); //Manually escape this value since it must be manually injected into the SQL below
		$newPosition = $db->escape(Validate::numeric($_POST['newPosition'])); //Manually escape this value since it must be manually injected into the SQL below
		
		try {
		//If the item is numerically higher than it was before, then the item was dragged down
			if ($newPosition > $currentPosition) {
				$db->update("UPDATE `pages` SET `position` = position - 1 WHERE `position` >= '" . $currentPosition . "' AND `position` <= '" . $newPosition . "'");
				$db->update("UPDATE `pages` SET", array (
					"position" => Validate::numeric($_POST['newPosition']) //No need to re-escape, as the method will handle it
				), "WHERE", array(
					"id" => $id
				));
		//If the item is numerically lower than it was before, then the item was dragged up
			} elseif ($newPosition < $currentPosition) {
				$db->update("UPDATE `pages` SET `position` = position + 1 WHERE `position` < '" . $currentPosition . "' AND `position` >= '" . $newPosition . "'");
				$db->update("UPDATE `pages` SET", array (
					"position" => Validate::numeric($_POST['newPosition']) //No need to re-escape, as the method will handle it
				), "WHERE", array(
					"id" => $id
				));
		//Catch any times a user tries to update the order with the same values
			} elseif ($newPosition == $currentPosition) {
				throw new Exception("<p>The items you have attempted to reorder were kept in the same order as before. Please <a href=\"javascript:window.location.href=window.location.href;\">click here</a> to try again.</p>");
		//Catch anything else strange that could happen
			} else {
				throw new Exception("<p>The system could not process your request. Please <a href=\"javascript:window.location.href=window.location.href;\">click here</a> to try again.</p>");
			}
			
			echo "success";
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
	//Don't continue building the rest of the page
		exit;
	}
	
//Set the page visibility
	if (isset($_POST['action']) && $_POST['action'] == "visibility") {		
	//Update the database
		$db->update("UPDATE `pages` SET", array (
			"visibility" => Validate::numeric($_POST['visibility'], 0, 1)
		), "WHERE", array(
			"id" => Validate::numeric($_POST['id'])
		));
		
		echo "success";
		
	//Don't continue building the rest of the page
		exit;
	}
	
//Add a new page
	if (isset($_POST['action']) && $_POST['action'] == "add") {
	//Grab the most recent position
		$position = $db->quick("SELECT * FROM `pages` WHERE `parentID` = '0' ORDER BY `position` DESC LIMIT 1");
		
	//Insert the data into the database
		$db->insert("INSERT INTO `pages`", array (
			"parentID" => "0",
			"position" => $position['position'] + 1,
			"visibility" => "1",
			"url" =>  Validate::required($_POST['URL']),
			"title" => Validate::required($_POST['title']),
			"content" => Validate::required($_POST['body'])
		));
		
	//This is the only instance where "success" is not echoed, since jQuery will need the ID when inserting the new row
		echo $db->insertID();
		
	//Don't continue building the rest of the page
		exit;
	}
	
//Edit a page
	if (isset($_POST['action']) && $_POST['action'] == "edit") {		
	//Update page's data in the database
		$db->update("UPDATE `pages` SET", array (
			"url" => Validate::required($_POST['URL']),
			"title" => Validate::required($_POST['title']),
			"content" => Validate::required($_POST['body'])
		), "WHERE", array(
			"id" => Validate::numeric($_POST['id'])
		));
		
		echo "success";
		
	//Don't continue building the rest of the page
		exit;
	}

//Delete a page
	if (isset($_POST['action']) && $_POST['action'] == "delete") {
	//Delete page's data in the database
		$db->delete("DELETE FROM `pages` WHERE", array(
			"id" => Validate::numeric($_POST['id'])
		));
		
		echo "success";
		
	//Don't continue building the rest of the page
		exit;
	}
	
/*
Visible content
---------------------------------------
*/

//Build the top of the page
	$templateAdmin = new TemplateAdmin();
	$templateAdmin->title = "Manage Public Website";
	$templateAdmin->byLine = "This is to manage the public website.";
	$templateAdmin->top();
	
//The page's description
	echo "<section class=\"description\">
<header>
<h1>Description</h1>
</header>
	
<p>This section of the site is intended to manage the public website. The content which is created from here will be visible to the public. From here, you are enabled to create, update, reorder, and delete pages. If you are using Flash&reg; as your website's main content, please be aware that this does not enable you manipulate the content of your Flash&reg; website. The content entered here will display behind the Flash&reg; application. Even though this is the case, it is still <strong>highly</strong> recommended that you enter content in here.
<br /><br />
Although you will be able to see the Flash&reg; content you have uploaded, search engines, such as Google, do not have the ability to see it. They will only be able to see the content you have built from here, which, to the typical viewer, will be hidden behind your Flash&reg; content. Building and maintaining up-to-date content here will boost your search-engine rankings and garner more frequent visitors. Furthermore, users who do not have Flash&reg; Player installed, or are viewing this site from a mobile device will see the content you have created here.
</p>
</section>";
	
//Build the toolbar
	echo "<nav>
<header>
<h1>Tools</h1>
</header>

<ul class=\"toolbar\">
<li><a class=\"new\">Create New Page</a></li>
</ul>
</nav>\n";
	
//If pages exist, then build a table containing the pages in ascending order for the current page level
	if ($db->exist("SELECT * FROM `pages`")) {
	//This table will be generated by jQuery at runtime, just provide a spot to drop it
		echo "<section class=\"content spacer\"><h1 class=\"loader\">Loading data...</h1></section>";
	} else {
		echo "<section class=\"spacer\">
<p>There aren't currently any pages on this system. You may <a class=\"create\" href=\"javascript:;\">add one now</a>.</p>
</section>";
	}
	
/*
Content containers for Data Package
---------------------------------------
*/
	
//Add content container
	echo "<div class=\"addContent editContent hide\">
<form>
<input type=\"hidden\" name=\"action\" class=\"typeInput\" value=\"add\" />
<section class=\"formSection\">
<header>
<h1>Content</h1>
</header>
<p>Title:</p>
<blockquote>
<p><input name=\"title\" type=\"text\" class=\"titleInput\" /></p>
</blockquote>
<p>Content:</p>
<blockquote>
<p><textarea name=\"body\" class=\"large bodyInput\"></textarea></p>
</blockquote>
</section>

<section class=\"formSection\">
<header>
<h1 class=\"expand\">Optional settings</h1>
</header>
<div class=\"hide\">
<p>Page URL:</p>
<blockquote>
<p>
<span class=\"parentURL\"></span>
<input name=\"URL\" type=\"select\" class=\"URLInput\" />
<span class=\"alertInfo\"></span>
</p>
</blockquote>
</div>
</section>
</form>";
	
//Build the bottom of the page
	$templateAdmin->bottom();
?>