<?php
include 'functions/core.php';

// If true print out incorrect password message
$loginfail = false;
// Check if login information has been provided
if (isset($_POST['user']) && isset($_POST['pass']) && $_POST['user'] != "" && $_POST['pass'] != "") {
	$conn = getDatabaseConnection();

	// Get username and escape characters
	$username = mysqli_real_escape_string($conn, $_POST['user']);
	// Get password corresponding to username
	$passQuery = $conn->query("SELECT password FROM Staff WHERE username='" . $username . "'");
	if ($passQuery->num_rows > 0) {
		$pass = $passQuery->fetch_assoc();
		// Check if password matches
		if ($_POST['pass'] == $pass['password']) {
			// Being login session
			session_start();
			$_SESSION['user'] = $username;
            // Redirect to dashboard
            header("Location: dashboard.php");
		}
	}
	$loginfail = true;
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>Staff Login</title>
    <?php
    include 'htmlsnippets/head.html';
    ?>
</head>

<body>
	<?php
    include 'htmlsnippets/nav.html';
    ?>
    <div class="container">
	    
		<?php
			if ($loginfail) {
				echo "<p>Username or password is incorrect.</p>";
			}
		?>
        <h1>Staff Login</h1>
		<form action="" method="post">
			<label for="user">Username</label>
			<input id="user" type="text" name="user">
			<label for="pass">Password</label>
			<input id="pass" type="password" name="pass">
			<input type="submit" value="Login">
		</form>
	</div>
</body>
</html>