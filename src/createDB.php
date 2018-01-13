<?php 
	$dbh = new PDO("sqlite:inventory.sqlite");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$query = $dbh->prepare("CREATE TABLE IF NOT EXISTS lenses (
	id INTEGER PRIMARY KEY,
	name TEXT NOT NULL,
	condition TEXT,
	notes TEXT,
	focal_length TEXT,
	focal TEXT,
	mount TEXT,
	price_in DECIMAL(10,2),
	price_out DECIMAL (10,2))");
	$query->execute();
?>