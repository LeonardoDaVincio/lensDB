<!DOCTYPE html>
<html>
<head>
<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<script   src="https://code.jquery.com/jquery-3.2.1.min.js"   integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="   crossorigin="anonymous"></script>
	
</head>

<body>
	<div class="container">
		<a class="btn btn-primary" href="index.html">Create new Entry</a>
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
</div>

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
        			alert('Lens edited successfully!  ' + e_id +" "+ e_name+" "+e_focal_length+" "+e_focal+" "+e_price_in+" "+e_price_out+" "+e_condition+" "+e_notes);
    			});
			}

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
        			url: "admin/post.php",
        			type: "post",
        			data: serializedData
    			});

    			request.done(function (response, textStatus, jqXHR){
        			// Log a message to the console
        			if(confirm("Lens added!\nGo to list?")){
        				document.location.href = "admin/objektivdbv2.php";
        			};
        			console.log('worked');
    			});

    			request.fail(function (jqXHR, textStatus, errorThrown){
        			// Log the error to the console
        			console.log('failed');
        			alert(
            			"The following error occurred: "+
            			textStatus, errorThrown
        			);
    			});

    			request.always(function () {
        			// Reenable the inputs
        			console.log('Nothing');
        			$inputs.prop("disabled", false);
    			});

				/**
				$.ajax({
					name: 'post.php',
					type: 'POST',
					data: {
						name: $('#name_input').val(),
						focal_length: $('#focal_length_input').val(),
						focal: $('#focal_input').val(),
						mount: $('#mount_input').val(),
						condition: $('#condition_input').val(),
						notes: $('#notes_input').val(),
						price_in: $('#price_in_input').val(),
						price_out: $('#price_out_input').val(),

					},
					success: function(msg) {
						alert('Lens added!');
					}               
				});
				**/
			});

		</script>
</body>
</html>