<?php
	// enable for debugging
	// ini_set('display_errors', 'On');
    // error_reporting(E_ALL);
	
	include('../includes/send_card.php');
	
	$database = mysqli_connect("localhost", "root", "", "git_php_repos");
	
	// ensure db exists
	$result = mysqli_query($database, "SHOW TABLES LIKE 'repositories';");
	if (mysqli_num_rows($result) == 0) {
		sendToClient("Couldn't find table 'repositories' in git_php_repos database. Please click 'refresh database' button and try again.");
		exit();
	}
	
	// get repo info from db
	$result = mysqli_query($database, "SELECT * FROM repositories;");
	$rows = mysqli_num_rows($result);
	
	// ensure we got back at least 1 entry
	if ($rows == 0) {
		sendToClient("'repositories' table found in git_php_repos database but query returned 0 results. Please click 'refresh database' button and try again.");
		exit();
	}
	
	ob_start();
	echo "<ul class=\"collapsible\">";
	while ($repo = mysqli_fetch_array($result)) {
		echo 
			"<li>
				<div class='collapsible-header'>
					" . $repo['name'] . "
				</div>
				<div class='collapsible-body'>
					<h6>Repository Details:</h6>
					<span>GitHub ID: " . $repo['repo_id'] . "</span>
					<span>Name : " . $repo['name'] . "</span>
					<span>URL: " . $repo['url'] . "</span>
					<span>Date Created: " . $repo['created'] . "</span>
					<span>Date Last Pushed: " . $repo['lastPushed'] . "</span>
					<span>Description: " . $repo['description'] . "</span>
					<span>Stars: " . $repo['stars'] . "</span>
				</div>
			</li>";
	}
	echo "</ul>";
	ob_end_flush();
?>
