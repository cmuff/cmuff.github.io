<?php
/**
 * User: Jack
 * Date: 23/03/17
 */
include 'core.php';
$conn = getDatabaseConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	echo"connection error please contact support";
}else{
	//checks input
	if(isset($_POST["hunt_id"])){
		// Gets data from site
		$hunt_id=$conn->real_escape_string($_POST["hunt_id"]);
		echo $hunt_id;
		$staff_username=getStaffSession();

		$query="DELETE FROM Hunt WHERE id='".$hunt_id."' AND staff_username='".$staff_username."';";
		if ($conn->query($query) === TRUE) {
			echo "record deleted successfully";
			echo $query;
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		header("Location: ../dashboard.php");
	}
}
?>
