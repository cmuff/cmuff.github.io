<?php
include 'core.php';
//
$conn = getDatabaseConnection();

// Gets data from site
$hunt_id=$_POST["hunt"];

// convert form time into a DateTime variable for easy timestamp
$start_time=new DateTime($_POST["time"]);

//number of groups
$groups=$_POST["groups"];

//start groups on different locations
$start_loc=0;
$huntQuery=$conn->query("SELECT number FROM Location WHERE hunt_id = '".$hunt_id."'");
var_dump($huntQuery);
$number_of_rows = $huntQuery->num_rows;
var_dump($number_of_rows);
if($number_of_rows == 1){
	$start_loc=-1;
}

//delete all groups registered to that hunt
$query="DELETE FROM HuntGroup WHERE hunt_id = '".$hunt_id."'";
$conn->query($query);

//generates a $groups amount of groups
for ($number = 0; $number < $groups; $number++) {
	$query="INSERT INTO HuntGroup (number, found, next_location, finish_time, hunt_id) VALUES
		(".$number.", 0, ".$start_loc.", NULL,'".$hunt_id."')";
	$conn->query($query);
	if($start_loc!=-1){
		$start_loc++;
		if($start_loc==$number_of_rows-1){
			$start_loc=0;
		}
	}
}

//update hunt table in database with new timestamp
$query="UPDATE Hunt SET start_time = ".$start_time->getTimestamp()." WHERE id = '".$hunt_id."'";
$conn->query($query);

//redirects user back to dashboard 
header("Location: ../dashboard.php");
?>