<?php

	// Unset session variables when a user is logged out
	if($_GET['logout'] == 1) {
		unset($_SESSION['user_type']);
		unset($_SESSION['user_id']);
		unset($_SESSION['name']);
		unset($_SESSION['user_valid']);
	}

	if (!$_SESSION['name'] || !$_SESSION['user_valid'])
		$isloggedin = FALSE;
	else
		$isloggedin = TRUE;

	// This function checks that our user is a valid logged in user, otherwise redirects to index page
	function authuser() {
		global $isloggedin;
		if(!$isloggedin)
			header("Location: index.php?err=1");
	}

	// This function checks that our user is valid and not a "standard" user, otherwise redirects to index page
	function authadmin() {
		authuser();
		if($_SESSION['user_type'] == 'S')
			header("Location: index.php?err=2");
	}

	function site_navigation() {
		echo "\n\t" . '<div class="mainNav">
			<img src="styleimages/csumbLogo.jpg" id="csumblogo" alt="CSUMB otter logo" style="width:100px;height:100px;">
			<h1 id="sitetitle"><a href="index.php">CSUMB Anime Club</a></h1>
			<div class="navlinks">';

		// check permission and generate admin menu
		global $isloggedin;
		if($_SESSION['user_type'] == 'A' || $_SESSION['user_type'] == 'P' && $isloggedin) {
			admin_menu();
		}
		echo "\n\t\t" . '</div>
		</div>';
		// generate collection navigation menu
		collection_nav();
	}

	// Generate the menu to navigate through a list of image collections, use "next" and "previous" buttons to display a limited number of collections at one time.
	function collection_nav() {
		$query = mysql_query("SELECT name, id FROM collection WHERE apr = 1 ORDER BY id");
		echo "\t" . '<div class="collectionNav">';
		echo "\n\t\t" . '<span class="nav" id ="prevCollection">< previous</span>';

		// display a link for each image collection
		while ($row = mysql_fetch_assoc($query)) {
			echo "\n\t\t" . '<a href="collection.php?nav=' . $row['id'] . '" class="nav colNav hidden" id="collection' . $row['id'] . '">' . $row['name'] . '</a>';
		}

		echo "\n\t\t" . '<span class="nav" id="nextCollection">next ></span>';
		echo "\n\t" . '</div>';

	}

	// Code to generate the admin menu
	function admin_menu() {
		echo "\n\t" . '<a href="users.php" class="nav">Manage Users</a>'
				. "\n\t" . '<a href="aprcollection.php" class="nav">Review Collections</a>';

		// add a link for logged in users to add a new collection
		global $isloggedin;
		if($isloggedin) {
			echo "\n\t" . '<a href="editcollection.php?new=1" class="nav">Add Collection</a>';
		}
	}

	function site_head() {
		echo '<!DOCTYPE html>
		<html lang="en">
		<head>
		  <meta charset="utf-8" />
		  <title>CSUMB Anime Club</title>
		  <link href="css/index.css" rel="stylesheet" type="text/css">
		</head>
		<body onload="changeCollectionNav()">';
		site_navigation();
		echo "\n\t" . '<div class="mainContent">';
	}

	function site_footer() {
		echo "\n\t" . '</div>
		<script type="text/javascript" src="scripts.js"></script>
		</body>
		</html>';
	}

	// Checks that the variable contains a numeric string and passes back the interger value if true, otherwise returns false
	function validate_get($get) {
		if (is_numeric($get)) {
    			return $get + 0;
  		}
		else {
			return false;
		}
	}

?>
