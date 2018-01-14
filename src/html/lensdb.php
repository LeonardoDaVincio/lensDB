<!DOCTYPE html>
<html>
<head>
<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
	
	
</head>

<body>
	<nav class="navbar navbar-dark bg-dark">
		<span class="navbar-brand mb-0 h1">Lens DB</span>
			<a class="btn btn-outline-light" href="index.html">Create new Entry</a>
  <!-- Navbar content -->
</nav>
	<div class="container">

		
		<?php
		require "db.php";

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
				echo "<tr id='" . $this->id . "'>";

				echo "<td contenteditable='true'>" . $this->name . "</td>";
				echo "<td contenteditable='true'>" . $this->focal_length . "</td>";
				echo "<td contenteditable='true'>" . $this->focal . "</td>";
				echo "<td contenteditable='true'>" . $this->price_in . "</td>";
				echo "<td contenteditable='true'>" . $this->price_out . "</td>";
				echo "<td>" . $outcome . "</td>";
				echo "<td contenteditable='true'>" . $this->condition . "</td>";
				echo "<td contenteditable='true'>" . $this->notes . "</td>";
				echo "<td><button onclick=edit('" . $this->id . "') class='btn btn-outline-dark btn-sm'>Save</button></td>";

				echo "</tr>";
			}
		}

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
			$success = $insert->execute();
			if ($success){
				echo("<h1>Lens added!</h1>");
			} else {
				echo("<h1>Error Adding Lens</h1>");
			}

		}
		
		$query = $dbh->prepare("SELECT * from lenses");
		$query->execute();

		$result = $query->fetchAll();

		$cost = $dbh->prepare("SELECT sum(price_in) from lenses");
		$cost->execute();

		$cost_result = $cost->fetchAll();

		$revenue = $dbh->prepare("SELECT sum(price_out) from lenses");
		$revenue->execute();

		$revenue_result = $revenue->fetchAll();

		$profit_result = ($cost_result[0][0] - $revenue_result[0][0]) * (-1);

		$ppl = $dbh->prepare("SELECT (sum(price_in) - sum(price_out)) / count(id) from lenses");
		$ppl->execute();

		$ppl_result = $ppl->fetchAll();

		$lens_count_sold = $dbh->prepare("SELECT count(id) from lenses WHERE price_out != 0");
		$lens_count_sold->execute();

		$lens_count_sold_result = $lens_count_sold->fetchAll();

		$lens_count_stock = $dbh->prepare("SELECT count(id) from lenses WHERE price_out = 0");
		$lens_count_stock->execute();

		$lens_count_stock_result = $lens_count_stock->fetchAll();

		$lens_all = intval($lens_count_stock_result[0][0]) + intval($lens_count_sold_result[0][0]);

		echo "<h1>Overview Money</h1>";

		echo "<table class='table table-striped table-sm'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th scope='col'>Cost</th>";
		echo "<th scope='col'>Revenue</th>";
		echo "<th scope='col'>Profit</th>";
		echo "<th scope='col'>PPL</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		echo "<tr>";
		echo "<td>" . $cost_result[0][0] . "</td>";
		echo "<td>" . $revenue_result[0][0] . "</td>";
		echo "<td>" . $profit_result . "</td>";
		echo "<td>" . $ppl_result[0][0] . "</td>";
		echo "</tr>";
		echo "</tbody>";
		echo "</table>";

		echo "<h1>Overview Lenses</h1>";

		echo "<table class='table table-striped table-sm'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th scope='col'>Lens count</th>";
		echo "<th scope='col'>Lenses sold</th>";
		echo "<th scope='col'>Lenses in stock</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		echo "<tr>";
		echo "<td>" . $lens_all . "</td>";
		echo "<td>" . $lens_count_sold_result[0][0] . "</td>";
		echo "<td>" . $lens_count_stock_result[0][0] . "</td>";
		echo "</tr>";
		echo "</tbody>";
		echo "</table>";

		echo "<h1>Lenses</h1>";

		echo "<table class='table table-striped'>";
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
		echo "<th scope='col'>Edit</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach($result as $row){
			$obj = new ListObject($row['id'], $row['name'], $row['price_in'], $row['price_out'], $row['focal_length'], $row['focal'], $row['condition'], $row['notes']);

			$obj->print_object();

		}

		echo "</tbody></table>";

	?>

	<a class="btn btn-dark" href="index.html">Create new Entry</a>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
<script type="text/javascript">

			var request;

			function edit(event_id) {
				if (request) {
        			request.abort();
    			}

    			var e_id = event_id
    			var e_name = $("#" + event_id).children().eq(0).text();
    			var e_focal_length = $("#" + event_id).children().eq(1).text();
    			var e_focal = $("#" + event_id).children().eq(2).text();
    			var e_price_in = $("#" + event_id).children().eq(3).text();
    			var e_price_out = $("#" + event_id).children().eq(4).text();
    			var e_condition = $("#" + event_id).children().eq(6).text();
    			var e_notes = $("#" + event_id).children().eq(7).text();

    			request = $.ajax({
        			url: "edit.php",
        			type: "post",
        			data: {
        				id : e_id,
        				name: e_name,
        				focal_length: e_focal_length,
        				focal: e_focal,
        				price_in: e_price_in,
        				price_out: e_price_out,
        				condition: e_condition,
        				notes: e_notes
        			}
    			});

    			request.done(function (response, textStatus, jqXHR){
        			// Log a message to the console
        			alert('Lens edited successfully!');
    			});
			}

		</script>
</body>
</html>