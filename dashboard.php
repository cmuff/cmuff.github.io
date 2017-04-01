<?php
include 'functions/core.php';
include 'functions/qr.php';

$staff_username = getStaffSession();

$conn = getDatabaseConnection();

// Gets the hunt names and ids for the chosen staff username
$huntQuery = $conn->query("SELECT name,id,start_time FROM Hunt WHERE staff_username = '" . $staff_username . "'");

// Stores the html that makes up the array of hunts
$nameArray = [];
// Stores the html that makes up the array of hunts buttons
$huntArray = [];
// Stores the html that makes up the location name and answer tale rows
$ansTableRows = [];

if ($huntQuery->num_rows <= 0) {
	array_push($huntArray,"<h3>No hunts found!</h3>");
}
else {
	//create a variable to distinguish different QRCodes
	$QRNumber=0;
	
	while ($row = $huntQuery->fetch_assoc()) {
		// Temporarily stores the html rows for the hunt name
		$huntString = "";
		// Add hunt to list, checks start_time to see if the hunt has been initialised
		if(is_null($row["start_time"]) or $row["start_time"]<date("U")){
			$huntString .= "<h3>" . $row["name"] ."</h3>";
		}else{
			$huntString .= "<h3>" . $row["name"] ." (Activate)</h3>";
		}
		array_push($nameArray, $huntString);
		
		$huntString = "<td><form action='initiate_hunt.php' method='post' class='button_form'><input type='hidden' value='" .$row["id"]. "' name='hunt_id'/><input type ='submit' value='Initiate hunt'/></form></td>";
		$huntString .= "<td><form action='view_groups.php' method='post' class='button_form'><input type='hidden' value='" .$row["id"]. "' name='hunt_id'/><input type ='submit' value='View groups'/></form></td>";
		$huntString .= "<td><form action='functions/delete_hunt_action.php' method='post' class='button_form'><input type='hidden' value='" .$row["id"]. "' name='hunt_id'/><input type='submit' value='Delete hunt'></form></td>";
		
		// Add hunt to hunt array
		array_push($huntArray, $huntString);
		
		// Get location names and answers for the hunt
		$ansQuery = $conn->query("SELECT name, number, hunt_id FROM Location WHERE hunt_id = '" . $row["id"] . "'");
		
		// Temporarily stores the html table rows for a single hunt
		$ansString = "";
		if ($ansQuery->num_rows <= 0) {
			$ansString = '<tr><td colspan="2">No locations found.</td></tr>';
		}
		else {
			// Create new table row for each location
			while ($row2 = $ansQuery->fetch_assoc()) {
				$id="qrcode" . $QRNumber;
				$text=$row2["number"].$row2["hunt_id"];
				$ansString .= "<tr>";
				$ansString .= "<td>" . $row2["name"] . "</td>";
				$ansString .= 
				"<td>
				<div id='".$id."'></div>
				" .genQR($id , $text, 64, 64 ). "
				</td>";
				$ansString .= "</tr>";
				$QRNumber++;
			}
		}
		// Add html table rows to the array
		array_push($ansTableRows, $ansString);
	}
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
	<title>Staff Dashboard</title>
	<?php
	include 'htmlsnippets/head.html';
	?>
	<link rel="stylesheet" type="text/css" href="css/dashboard.css">
</head>

<body>
	<?php
    include 'htmlsnippets/nav.html';
    ?>
    <div class="container">
		<h1>Dashboard</h1>
		<?php
		echo "<p>Hello, " . $staff_username . "!</p>";
		?>

		<h2>Activate and View Treasure Hunts:</h2>
		<div id="huntView">
		<?php
			$counter = 0;
			foreach($ansTableRows as $tableRows) {
				echo "<table><tr><th colspan=4>".$nameArray[$counter]."</th></tr>";
				echo "<tr><td><table><tr><th class='thLoc'>Location Names</th><th>QRCode</th></tr>";
				echo $tableRows;
				echo "</table></td>";
				echo $huntArray[$counter];
				$counter++;
				echo "</tr></table>";
			}
		?>
		</div>
	</div>
</body>
</html>