<?php
	
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpassword = 'root';
	$dbdatabase = 'anime_club';
	
	$link = mysql_connect('localhost', 'root', 'root');
	mysql_select_db($dbdatabase, $link) or die('Could not select database.');
?>