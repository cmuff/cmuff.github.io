<?php
/*
 * This file is for functions that are used
 * repeatedly throughout the project.
 */

// Connects to the database and returns a mysqli object
function getDatabaseConnection() {
    $servername = "localhost";
    $username = "t2022t12";
    $password = "Gram|CubFire";
    $dbname = "t2022t12";
    //connects to database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if($conn->connect_error){
        die($conn->connect_error);
    }
    return $conn;
}

/*
 * Check if staff member is logged in
 * if they are, return their username
 * otherwise redirect to the login page
 */
function getStaffSession() {
    // Get previously started session
    session_start();
    $staff_username = '';
    if(isset($_SESSION['user'])) {
        // Get username from session
        $staff_username = $_SESSION['user'];
    }
    else {
        // User not logged in so redirect to login page
        header("Location: login.php");
    }
    return $staff_username;
}
?>