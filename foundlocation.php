<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 03/03/17
 * Time: 11:01
 */

//Set up database connection
include 'functions/core.php';

$conn = getDatabaseConnection();

//Check the GET method is being used
if($_SERVER['REQUEST_METHOD'] === "GET") {
    //Verify the API key has been passed, and that groupNo, huntID and number_found have been set
    if ($_GET['apiKey'] === "team12apikey" && !empty($_GET['groupNo']) && !empty($_GET['huntID']) && !empty($_GET['number_found'])) {
        //Clean up parameters passed via GET
        $huntID = mysqli_real_escape_string($conn, $_GET['huntID']);
        $groupNo = mysqli_real_escape_string($conn, $_GET['groupNo']);
        $number_found = mysqli_real_escape_string($conn, $_GET['number_found']);

        //Update the database with new parameters
        $result = $conn->query("UPDATE HuntGroup SET found='{$number_found}' WHERE number='{$groupNo}' AND hunt_id='{$huntID}'");

        //optional, check $result is TRUE
    }
}