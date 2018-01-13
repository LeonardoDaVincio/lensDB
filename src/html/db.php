<?php
	$dbh = new PDO("sqlite:../inventory.sqlite");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
?>