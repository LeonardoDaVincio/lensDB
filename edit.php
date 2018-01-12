<!DOCTYPE html>
<html>
<head></head>
<body>
	<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);

	$dbh = new PDO("sqlite:inventory.sqlite");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	$insert = $dbh->prepare("UPDATE lenses
		SET name = :name,
			condition = :condition,
			notes = :notes,
			focal_length = :focal_length,
			focal = :focal,
			price_in = :price_in,
			price_out = :price_out
		WHERE id = :id");


	if(isset($_POST["id"], $_POST["name"], $_POST["condition"], $_POST["notes"],
		$_POST["focal_length"], $_POST["focal"],
		$_POST["price_in"], $_POST["price_out"])){

		$name = $_POST["name"];
	$id = $_POST["id"];
	$condition = $_POST["condition"];
	$notes = $_POST["notes"];
	$focal_length = $_POST["focal_length"];
	$focal = $_POST["focal"];
	
	$price_in = $_POST["price_in"];
	$price_out = $_POST["price_out"];
	$insert->bindParam(':id', $id);
	$insert->bindParam(':name', $name);
	$insert->bindParam(':condition', $condition);
	$insert->bindParam(':notes', $notes);
	$insert->bindParam(':focal_length', $focal_length);
	$insert->bindParam(':focal', $focal);
	
	$insert->bindParam(':price_in', $price_in);
	$insert->bindParam(':price_out', $price_out);
	$insert->execute();
}

?>

</body>