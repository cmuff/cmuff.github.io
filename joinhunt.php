<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 03/03/17
 * Time: 10:49
 */

//Set up database connection
include 'functions/core.php';

$conn = getDatabaseConnection();

//Check the GET method is being used
if($_SERVER['REQUEST_METHOD'] === "GET"){
    // Verify the API key, group number and hunt ID
    // have been set and have valid values.
    if(isset($_GET['apiKey']) && $_GET['apiKey'] === "team12apikey" &&
            isset($_GET['groupNo']) && is_numeric($_GET['groupNo']) && 
            isset($_GET['huntID']) && !empty($_GET['huntID'])){
        //Clean up the hunt ID and group number from the URL
        $huntID = mysqli_real_escape_string($conn, $_GET['huntID']);
        $groupNo = mysqli_real_escape_string($conn, $_GET['groupNo']);

        //Check that the huntID exists in the hunt table
        $result=$conn->query("SELECT * FROM Hunt WHERE id='{$huntID}'");
        if($result->num_rows > 0){
            //The hunt ID is valid, now verify the group is a part of this hunt
            $result=$conn->query("SELECT * FROM HuntGroup WHERE number='{$groupNo}' AND hunt_id='{$huntID}'");
            if($result->num_rows > 0){
                //Everything is good, return success
                die("success");
            }
        }
    }
    //If script has reached here, must be an error
    die("error");
}

?>