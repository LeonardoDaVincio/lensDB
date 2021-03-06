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
		<a class="btn btn-outline-light text-light" data-toggle='modal' data-target='.bd-calc-modal'>Revenue Calc</a>
		<a class="btn btn-outline-light text-light" data-toggle='modal' data-target='.bd-lens_modal'>Create new Entry</a>
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

	$lens_count_sold = $dbh->prepare("SELECT count(id) from lenses WHERE price_out != 0");
	$lens_count_sold->execute();

	$lens_count_sold_result = $lens_count_sold->fetchAll();

	$lens_count_stock = $dbh->prepare("SELECT count(id) from lenses WHERE price_out = 0");
	$lens_count_stock->execute();

	$lens_count_stock_result = $lens_count_stock->fetchAll();

	$misc = $dbh->prepare("SELECT sum(price) from misc");
	$misc->execute();

	$misc_result = $misc->fetchAll()[0][0];

	$profit_result -= $misc_result;

	$lens_all = intval($lens_count_stock_result[0][0]) + intval($lens_count_sold_result[0][0]);

	$ppl_result = round($profit_result / intval($lens_count_stock_result[0][0]),2) * (-1);

	echo "<h1>Overview Money</h1>";

	echo "<table class='table table-striped table-sm'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th scope='col'>Cost</th>";
	echo "<th scope='col'>Revenue</th>";
	echo "<th scope='col'>Profit</th>";
	echo "<th scope='col'>PPL</th>";
	echo "<th scope='col'>Misc Cost/Revenue <a role='button' class='btn btn-link btn-sm' data-toggle='modal' data-target='.bd-example-modal-sm'><small>Add</small></a></th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	echo "<tr>";
	echo "<td>" . $cost_result[0][0] . "</td>";
	echo "<td>" . $revenue_result[0][0] . "</td>";
	echo "<td>" . $profit_result . "</td>";
	echo "<td>" . $ppl_result . "</td>";
	echo "<td>" . $misc_result . "</td>";
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

	<a class="btn btn-dark text-light" data-toggle='modal' data-target='.bd-lens_modal'>Create new Entry</a>
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
        			location.reload();
        		});
	}

</script>

<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">New entry</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="misc_form">
					<div class="form-group">
						<label class="form-control-label" for="price_input">Price:</label>
						<input class="form-control" id="price_input" type="text" name="price" placeholder="Price">
					</div>
					<div class="form-group">
						<label class="form-control-label" for="item_input">Item:</label>
						<input class="form-control" id="item_input" type="text" name="item" placeholder="Item">
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary form-control">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-lens_modal" tabindex="-1" role="dialog" aria-labelledby="myBigModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="myBigModalLabel">New entry</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="lens_form">
					
					<h2>Data <small>technical</small></h2>
					<div class="form-group">
						<label class="form-control-label" for="name_input">Name:</label>
						<input class="form-control" id="name_input" type="text" name="name" placeholder="Enter name">
					</div>
					<div class="form-group">
						<label class="form-control-label" for="focal_length_input">Focal Length:</label>
						<input class="form-control" id="focal_length_input" type="text" name="focal_length" placeholder="Focal length">
					</div>

					<div class="form-group">
						<label class="form-control-label" for="focal_input">Focal:</label>
						<input class="form-control" id="focal_input" type="text" name="focal" placeholder="Focal">
					</div>
					<div class="form-group">
						<label class="form-control-label" for="mount_input">Mount:</label>
						<input class="form-control" id="mount_input" type="text" name="mount" placeholder="Mount">
					</div>

					<h2>Notes <small>informistical</small></h2>
					<div class="form-group">
						<label class="form-control-label" for="condition_input">Condition:</label>
						<input class="form-control" id="condition_input" type="text" name="condition" placeholder="Condition">
					</div>
					<div class="form-group">
						<label class="form-control-label" for="notes_input">Notes:</label>
						<input class="form-control" id="notes_input" type="text" name="notes" placeholder="Notes">
					</div>

					<h2>Money <small>capitalistical</small></h2>
					<div class="form-group">
						<label class="form-control-label" for="price_in_input">Price in:</label>
						<input class="form-control" id="price_in_input" type="text" name="price_in" placeholder="Price in">
					</div>
					<div class="form-group">
						<label class="form-control-label" for="price_out_input">Price out:</label>
						<input class="form-control" id="price_out_input" type="text" name="price_out" placeholder="Price out">
					</div>

					
					<div class="form-group">
						<button type="submit" class="btn btn-primary form-control">Submit</button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-calc-modal" tabindex="-1" role="dialog" aria-labelledby="myCalcModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Calc Revenue</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
					<form>
						<div class="form-group">
							<label class="form-control-label" for="inputPrice">Price:</label>
							<input id="inputPrice" type="text" class="form-control" aria-label="Text input with checkbox" value="0">
						</div>
						<div class="form-group">
							<label class="form-control-label" for="inputShip">Shipping:</label>
							<input id="inputShip" type="text" class="form-control" aria-label="Text input with checkbox" value="0">
						</div>
						<h2>Fees:</h2>
						<div class="form-group">
							<label class="form-control-label" for="checkPayPal">PayPal</label>
							<input id="checkPayPal" type="checkbox" aria-label="PayPal">
						</div>
						<div class="form-group">
							<label class="form-control-label" for="checkEbay">eBay</label>
							<input id="checkEbay" type="checkbox" aria-label="eBay">
						</div>

						
						<h1 id="calcResult">0.00</h1>
					</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$('#lens_form').submit(function(event) {
		event.preventDefault();

		if (request) {
			request.abort();
		}

		var $form = $(this);

		var $inputs = $form.find("input, textarea");

		var serializedData = $form.serialize();

		$inputs.prop("disabled", true);

		console.log('start request');
		request = $.ajax({
			url: "post.php",
			type: "post",
			data: serializedData
		});

		request.done(function (response, textStatus, jqXHR){
        			// Log a message to the console
        			alert('Lens added!');
        			location.reload();
        			console.log('worked');
        		});

		request.fail(function (jqXHR, textStatus, errorThrown){
        			// Log the error to the console
        			console.log('failed');
        			alert(
        				"The following error occurred: "+
        				jqXHR.status + " " + errorThrown
        				);
        		});

		request.always(function () {
        			// Reenable the inputs
        			console.log('Nothing');
        			$inputs.prop("disabled", false);
        		});
	});

	$('#misc_form').submit(function(event) {
		event.preventDefault();

		if (request) {
			request.abort();
		}

		var $form = $(this);

		var $inputs = $form.find("input, textarea");

		var serializedData = $form.serialize();

		$inputs.prop("disabled", true);

		console.log('start request');
		request = $.ajax({
			url: "post_misc.php",
			type: "post",
			data: serializedData
		});

		request.done(function (response, textStatus, jqXHR){
        			// Log a message to the console
        			alert('Entry added!');
        			location.reload();
        			console.log('worked');
        		});

		request.fail(function (jqXHR, textStatus, errorThrown){
        			// Log the error to the console
        			console.log('failed');
        			alert(
        				"The following error occurred: "+
        				jqXHR.status + " " + errorThrown
        				);
        		});

		request.always(function () {
        			// Reenable the inputs
        			console.log('Nothing');
        			$inputs.prop("disabled", false);
        		});
	});

	//Calc logic

	$('#checkPayPal').change(function() {
          updatePrice();
    });

    $('#checkEbay').change(function() {
         updatePrice(); 
    });

    $('#inputPrice').change(function() {
          updatePrice();
    });

    $('#inputShip').change(function() {
          updatePrice();
    });

    function updatePrice(){

    	var checkPP = $('#checkPayPal').is(':checked');
    	var checkE = $('#checkEbay').is(':checked');

    	var sell = parseFloat($('#inputPrice').val());
    	var ship = parseFloat($('#inputShip').val());

    	if (sell === "undefined") sell = 0.0;
    	if (ship === "undefined") ship = 0.0;

    	var result = sell

    	if(checkPP) {
    		result -= (sell + ship) * 0.019;
    		result -= 0.35;
    	} 
    	if(checkE) {
    		result -= sell * 0.1
    	} 

    	

    	$("#calcResult").html(result);
    }

</script>
</body>
</html>