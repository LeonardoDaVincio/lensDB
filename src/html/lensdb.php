<!DOCTYPE html>
<html>
<head>
<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.29.2/js/jquery.tablesorter.js"></script>
</head>

<body>
	<div class="container">
		<a class="btn btn-primary" href="index.html">Create new Entry</a>
		<?php
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);

		/**
		* Class for list object
		*/
		class ListObject
		{
			private $id;
			private $name;
			private $price_in;
			private $price_out;
			private $focal_length;
			private $focal;
			private $condition;
			private $notes;
			
			function __construct($id, $name, $price_in, $price_out, $focal_length, $focal, $condition, $notes)
			{
				$this->id = $id;
				$this->name = $name;
				$this->price_in = $price_in;
				$this->price_out = $price_out;
				$this->focal_length = $focal_length;
				$this->focal = $focal;
				$this->condition = $condition;
				$this->notes = $notes;
			}

			function print_object() 
			{
				$outcome = '-';
				if ($this->price_out > 0) {
					$outcome = ($this->price_in - $this->price_out) * -1;
				}
				echo "<tr>";

				echo "<td>" . $this->name . "</td>";
				echo "<td>" . $this->focal_length . "</td>";
				echo "<td>" . $this->focal . "</td>";
				echo "<td>" . $this->price_in . "</td>";
				echo "<td>" . $this->price_out . "</td>";
				echo "<td>" . $outcome . "</td>";
				echo "<td>" . $this->condition . "</td>";
				echo "<td>" . $this->notes . "</td>";

				echo "</tr>";
			}
		}

		$dbh = new PDO("sqlite:../inventory.sqlite");
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		$insert = $dbh->prepare("INSERT INTO lenses (name, condition, notes, focal_length, focal, mount, price_in, price_out) VALUES (:name, :condition, :notes, :focal_length, :focal, :mount, :price_in, :price_out)");


		if(isset($_POST["name"], /*$_POST["condition"], $_POST["notes"], */
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

			echo("<h1>Lens added!</h1>");

		}

		$query = $dbh->prepare("SELECT * from lenses");
		$query->execute();

		$result = $query->fetchAll();

		$cash = $dbh->prepare("SELECT sum(price_in) - sum(price_out) from lenses");
		$cash->execute();

		$cash_result = $cash->fetchAll();

		$ppl = $dbh->prepare("SELECT (sum(price_in) - sum(price_out)) / count(id) from lenses");
		$ppl->execute();

		$ppl_result = $ppl->fetchAll();

		echo("<h1>Lens DB</h1>");

		echo "<h2> Overall balance: " . $cash_result[0][0] * -1 . "</h2>";
		echo "<h2> Overall price per lens: " . $ppl_result[0][0] . "</h2>";

		echo "<table class='table table-striped tablesorter'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th scope='col'>Name</th>";
		echo "<th scope='col'>Focal Length</th>";
		echo "<th scope='col'>Focal</th>";
		echo "<th scope='col'>Price In</th>";
		echo "<th scope='col'>Price Out</th>";
		echo "<th scope='col'>Outcome</th>";
		echo "<th scope='col'>Condition</th>";
		echo "<th scope='col'>Notes</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach($result as $row){
			$obj = new ListObject($row['id'], $row['name'], $row['price_in'], $row['price_out'], $row['focal_length'], $row['focal'], $row['condition'], $row['notes']);

			$obj->print_object();

		}

		echo "</tbody></table>";

	?>
</div>
</body>
</html>