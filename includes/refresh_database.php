<?php
	// set these if you're behind a proxy
	define("PROXY_IP", '255.255.255.255');
	define("PROXY_PORT", 1234);
	
	include('../includes/send_card.php');
	
	// enable for debugging
	// ini_set('display_errors', 'On');
    // error_reporting(E_ALL);
	$log = fopen("debug.log", 'w');
	fwrite($log, "Request to refresh database received.\r\n");
	ob_start();		// This is done to collect warnings and such. We want more control over error reporting.
	
	// connect to MySQL server and see if git_php_repos database exists
	$database = mysqli_connect("localhost", "root", "", "git_php_repos");
	
	$db_check = mysqli_query($database, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'git_php_repos';");
	if (mysqli_num_rows($db_check) == 0) {
		fwrite($log, "Couldn't connect to MySQL database 'git_php_repos' via localhost address/hostname.\r\n");
		fclose($log);
		$err = "Couldn't connect to MySQL database 'git_php_repos' via localhost address/hostname. Make sure you have created a 'git_php_repos' database in your MySQL server instance.";
		sendToClient($err);
		exit();
	}
	
	fwrite($log, "Found git_php_repos database in MySQL server instance.\r\n");
	
	// at this point we know git_php_repos is reachable/exists
	// check to see if table 'repositories' exists.. if not, create it
	$table_check = mysqli_query($database, "SHOW TABLES LIKE 'repositories';");
	if (mysqli_num_rows($table_check) == 0) {
		$create_query = file_get_contents("make_repos_table.sql");
		$success = mysqli_query($database, $create_query);
		if (!$success) {
			fwrite($log, "Failed to create repositories table in db.\r\n");
			fclose($log);
			sendToClient("Failed to create repositories table in db.");
			exit();
		}
	}
	
	fwrite($log, "Found or created repositories table.\r\n");
	
	
	
	// below is mockup string data to test without abusing GitHub API
	// $json_string = file_get_contents("sample_output.txt");
	
	
	
	// // actually get repository data from github
	$curl = curl_init();
	fwrite($log, "curl session initialized.\r\n");
	curl_setopt($curl, CURLOPT_URL, "https://api.github.com/search/repositories?q=stars:>10000+topic:php");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	// specify v3 for github api
	
	// set proxy options if applicable
	if (defined(PROXY_IP) && defined(PROXY_PORT)) {
		curl_setopt($curl, CURLOPT_PROXY, PROXY_IP);
		curl_setopt($curl, CURLOPT_PROXYPORT, PROXY_PORT);
	}
	
	// set header values for GitHub API
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		"Accept: application/vnd.github.v3+json",
		"User-Agent: git_php_repos"
	));
	fwrite($log, "curl options set.\r\n");
	
	// execute!
	$json_string = curl_exec($curl);
	fwrite($log, "curl session finished.\r\n");
	
	// cleanup
	curl_close($curl);
	fwrite($log, "curl session closed.\r\n");
	
	
	
	if (!$json_string) {
		// uncomment below to see curl return in log
		// fwrite($log, "cURL session failed.\r\n" . var_dump($json_string));
		fwrite($log, "cURL session failed.\r\n");
		fclose($log);
		
		sendToClient("cURL session to get repository data from GitHub failed. If you're behind a proxy, set defines in lines 3-4 of 'includes/refresh_database.php'.");
		exit();
	} else {
		fwrite($log, "cURL successful.\r\n\r\n" . $json_string);
	}
		
	// at this point, we should have serialized json (string) data in our $json_string variable
	// let's write a query to insert the relevant repo data to our database
	
	$insert_query = "INSERT INTO repositories VALUES";
	$repo_data = json_decode($json_string);
	$repo_count = $repo_data->total_count;
	
	function quote($arg) {
		return "'" . $arg . "'";
	}
	
	function format_date($str) {
		$str = str_replace("T", " ", $str);
		$str = str_replace("Z", "", $str);
		return quote($str);
	}
	
	if ($repo_count > 0) {
		// safe to truncate previous repo data
		mysqli_query($database, "TRUNCATE TABLE repositories;");
		
		// concat query
		for ($i = 1; $i <= $repo_count; $i++) {
			$repo = $repo_data->items[$i - 1];
			$ordered_values = array(
				"NULL",		// reserved for primary ID
				quote($repo->id),
				quote($repo->name),
				quote($repo->url),
				format_date($repo->created_at),
				format_date($repo->pushed_at),
				quote($repo->description),
				$repo->stargazers_count
			);
			
			$suffix = ($i == $repo_count) ? ';' : ',';
			
			$insert_query = $insert_query . " (" . implode(',', $ordered_values) . ")" . $suffix;
		}
		
		fwrite($log, "Query created:\r\n\r\n" . $insert_query . "\r\n");
		
		// query ready, let's insert
		$success = mysqli_query($database, $insert_query);
		if (!$success) {
			fwrite($log, "INSERT query failed. See error:\r\n\r\n" . mysqli_error($database));
			fclose($log);
			sendToClient("Server was unable to insert GitHub API repository information to targeted MySQL database. Contact carlreedw@gmail.com.");
			exit();
		} else {
			fwrite($log, "INSERT query successful!");
			fclose($log);
			sendToClient("Database refreshed!");
			exit();
		}
		// otherwise we will send the default 200 (OK) HTTP code
	}
?>
