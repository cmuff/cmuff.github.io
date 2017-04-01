<?php
/**
 * User: Jack
 * Date: 17/02/17
 */
include 'core.php';
$conn = getDatabaseConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	echo"connection error please contact support";
}else{
	//checks input
	if(isset($_POST["huntName"]) && isset($_POST["endLocation"])){
		// Gets data from site
		$hunt_name=$conn->real_escape_string($_POST["huntName"]);
		$staff_username=getStaffSession();

		//generates random alphanumeric id
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$max = strlen($characters) - 1;
		$hunt_id = '';

		//generates and checks uniqueness
		do{
			$hunt_id = '';
			for ($i = 0; $i < 6; $i++) {
				$hunt_id .= $characters[mt_rand(0, $max)];
			}
			$result = $conn->query("SELECT id FROM Hunt WHERE id='".$hunt_id."';");
		}while($result->num_rows > 0);

		//Insert data into Hunt table
		$sql = "INSERT INTO Hunt (id, start_time, name, staff_username)
		VALUES ('".$hunt_id."',NULL,'".$hunt_name."','".$staff_username."')";
		$conn->query($sql);

		//add end location
		$loc_name = $conn->real_escape_string($_POST["endLocation"]["name"]);
		$clue = $conn->real_escape_string($_POST["endLocation"]["clue"]);
		$lat = $conn->real_escape_string($_POST["endLocation"]["lat"]);
		$lng = $conn->real_escape_string($_POST["endLocation"]["lng"]);
		$query="INSERT INTO Location (number, hunt_id, name, clue, lat, lng) VALUES
		(-1,'".$hunt_id."','".$loc_name."','".$clue."',".$lat.",".$lng.")";
		$conn->query($query);
		
		//checks for location data
		if (isset($_POST["location"])){
			//get posted data and add all locations
			$locationSize=sizeof($_POST["location"]);
			for ($number = 0; $number < $locationSize; $number++) {
				$loc_name = $conn->real_escape_string($_POST["location"][$number]["name"]);
				$clue = $conn->real_escape_string($_POST["location"][$number]["clue"]);
				$lat = $conn->real_escape_string($_POST["location"][$number]["lat"]);
				$lng = $conn->real_escape_string($_POST["location"][$number]["lng"]);
				$query="INSERT INTO Location (number, hunt_id, name, clue, lat, lng) VALUES
				(".$number.",'".$hunt_id."','".$loc_name."','".$clue."',".$lat.",".$lng.")";
				$conn->query($query);
			}
		}
		$conn->close();
		//execution has been ran successfully return to dashboard
		//querys can still fail though
		header("Location: ../dashboard.php");
	}
}
?>
