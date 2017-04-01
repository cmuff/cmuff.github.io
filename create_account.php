<?php
include 'functions/core.php';

// Only logged in staff can create accounts
getStaffSession();

$conn = getDatabaseConnection();

$message = ""; // Used to display message to user
$userValue = ""; // Used to set username input value
$passValue = ""; // Used to set password input value

// Check that username and password information has been provided
if (isset($_POST['user']) && isset($_POST['pass']) && $_POST['user'] != "" && $_POST['pass'] != "") {
    // Get username entered
    $username = mysqli_real_escape_string($conn, $_POST['user']);

    // Check if username is already taken
    $conflicts = $conn->query("SELECT * FROM staff_table WHERE username='{$username}'");
    if ($conflicts->num_rows > 0) {
        $message = "Username already taken!";
        // Fill the values the user gave back into the form.
        $userValue = 'value="' . $_POST['user'] . '"';
        $passValue = 'value="' . $_POST['pass'] . '"';
    }
    else {
        // Get password entered
        $password = mysqli_real_escape_string($conn, $_POST['pass']);
        // Insert new staff account
        $conn->query("INSERT INTO staff_table (username, pass) VALUES ('{$username}','{$password}')");
        $message = "Account created successfully!";
    }
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>Staff Account Creation</title>
    <?php
    include 'htmlsnippets/head.html';
    ?>
</head>

<body>
	<?php
    include 'htmlsnippets/nav.html';
    ?>
    <div class="container">
        <h1>Staff Account Creation</h1>
        <p id="message">
            <?php echo $message; ?>
        </p>
		<form id="createForm" action="" method="post">
			<label for="user">Username</label>
			<input id="user" type="text" name="user" <?php echo $userValue ?>>

			<label for="pass">Password</label>
			<input id="pass" type="password" name="pass" <?php echo $passValue ?>>

            <label for="confirmPass">Confirm Password</label>
            <input id="confirmPass" type="password">

			<input type="submit" value="create">
		</form>
	</div>
    <script src="js/create_account.js"></script>
</body>
</html>