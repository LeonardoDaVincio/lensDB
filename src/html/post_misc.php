<?php
	require "db.php";
	$insert = $dbh->prepare("INSERT INTO misc (item, price) VALUES (:item, :price)");


	if(isset($_POST["item"], $_POST["price"])){

	$item = $_POST["item"];
	$price = $_POST["price"];
	$insert->bindParam(':item', $item);
	$insert->bindParam(':price', $price);
	$success = $insert->execute();
	if ($success){
		http_response_code(201);
	} else {
		http_response_code(400);
	}
	//TODO check if successful, then return status code
}
?>