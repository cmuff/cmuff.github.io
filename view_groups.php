<?php
include 'functions/core.php';
include 'functions/qr.php';

$staff_username = getStaffSession();
$conn = getDatabaseConnection();
// Stores the html that makes up the location name and answer table rows
$ansString = '<tr><td colspan="4">No data found.</td></tr>';
$huntName = "no hunt found";

if(isset($_POST["hunt_id"])) {
	//GET hunt id from 
	$hunt_id = $conn->real_escape_string($_POST["hunt_id"]);
	
	$nameQuery = $conn->query("SELECT name FROM Hunt WHERE id = '" .$hunt_id. "'");
	if($nameQuery){
		$row = $nameQuery->fetch_assoc();
		$huntName = $row["name"];
	}
	$groupQuery = $conn->query("SELECT number, found, next_location, finish_time FROM HuntGroup WHERE hunt_id = '" .$hunt_id. "'");
	if ($groupQuery){		
		if ($groupQuery->num_rows <= 0) {
			$ansString = '<tr><td colspan="4">No groups found.</td></tr>';
		}
		else {
			//reset ansString
			$ansString = "";
			//identifier for qr codes
			$QRNumber=0;
			// Create new table row for each group
			while ($row = $groupQuery->fetch_assoc()) {
				//prepare qr data
				$id="qrcode" . $QRNumber;
				$text=$row["number"].$hunt_id;
				//begin row
				$ansString .= "<tr>";
				//column 1, group QR
				$ansString .= 
				"<td>
				<div id='".$id."'></div>
				" .genQR($id , $text, 64, 64 ). "
				</td>";
				//locations found
				$ansString .= "<td>" . $row["found"] . "</td>";
				//next location
				$ansString .= "<td>" . $row["next_location"] . "</td>";
				//finish time 
				$ansString .= "<td>" . $row["finish_time"] . "</td>";
				//end row
				$ansString .= "</tr>";
				$QRNumber++;
			}
		}
	}
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
	<title>Group View</title>
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
		<h1>Group view</h1>
		<?php
		echo "<p>Hello, " . $staff_username . "!</p>";
		echo "<p>Viewing the Hunt: " . $huntName . "</p>";
		?>

		<h2>Groups:</h2>
		<div id="huntView">
		<table><tr><th>Group QRCode</th><th>Locations Found</th><th>Next Location</th><th>Finish Time</th></tr>
		<?php
			echo $ansString;
		?>
		</table>
		</div>
	</div>
</body>
</html>