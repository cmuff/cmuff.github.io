<?php
include 'functions/core.php';
$staff_username = getStaffSession();
$conn = getDatabaseConnection();
$hunt_id = "";
if(isset($_POST["hunt_id"])) $hunt_id = $_POST["hunt_id"];
// Gets the hunt names and ids for the chosen staff username
$huntQuery = $conn->query("SELECT name,id FROM Hunt WHERE staff_username = '" . $staff_username . "'");

//variable to hold form html
$initiateForm = "";

//checks if user has any hunts
if ($huntQuery->num_rows <= 0) {
	$initiateForm = "<h2>No hunts found!</h2>";
}
//if the user has hunts create a form to initialise the hunt and groups
else {
	//beginning of form
	$initiateForm .=
	"<form id='form' action='functions/initiate_hunt_action.php' method='post'>
	<select name='hunt'>";
	//input the hunts read from the database as options with a value of hunt_id
	while ($row = $huntQuery->fetch_assoc()) {
		// Add hunt to options
		if($hunt_id == $row["id"]){
			$initiateForm .= "<option value='" . $row["id"] . "' selected='selected'>" . $row["name"] . "</option>";
		}else{
			$initiateForm .= "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
		}
	}
	//bottom half of form including start time and groups
	$initiateForm .=
	"</select>
	</br>
	<label for='time'>Start Time: </label>
	<input id='time' type='datetime-local' name='time' min='".date("Y-m-d")."T".date("H:i")."'>
	</br>
	<label for='groups'>Number of Groups: </label>
	<input id='groups' type='number' name='groups' min=1>
	</br>
	<input id='submit' type='submit' name='submit'>
	</form>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Initiate Hunt</title>
	<?php include 'htmlsnippets/head.html'; ?>
</head>

<body>
	<?php include 'htmlsnippets/nav.html'; ?>
    <div class="container">
		<h1>Initiate Hunt</h1>
		<?php echo $initiateForm; ?>
	</div>
</body>
</html>