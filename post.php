<!DOCTYPE html>
<html>
<head></head>
<body>
	<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);

	$dbh = new PDO("sqlite:inventory.sqlite");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	$insert = $dbh->prepare("INSERT INTO lenses (name, condition, notes, focal_length, focal, mount, price_in, price_out) VALUES (:name, :condition, :notes, :focal_length, :focal, :mount, :price_in, :price_out)");


	if(isset($_POST["name"], $_POST["condition"], $_POST["notes"],
		$_POST["focal_length"], $_POST["focal"], $_POST["mount"],
		$_POST["price_in"], $_POST["price_out"])){

		$name = $_POST["name"];
	$condition = $_POST["condition"];
	$notes = $_POST["notes"];
	$focal_length = $_POST["focal_length"];
	$focal = $_POST["focal"];
	$mount = $_POST["mount"];
	$price_in = $_POST["price_in"];
	$price_out = $_POST["price_out"];
	$insert->bindParam(':name', $name);
	$insert->bindParam(':condition', $condition);
	$insert->bindParam(':notes', $notes);
	$insert->bindParam(':focal_length', $focal_length);
	$insert->bindParam(':focal', $focal);
	$insert->bindParam(':mount', $mount);
	$insert->bindParam(':price_in', $price_in);
	$insert->bindParam(':price_out', $price_out);
	$insert->execute();
}

?>

</body>