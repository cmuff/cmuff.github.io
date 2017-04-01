<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 15/11/16
 * Time: 15:01
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
        //Clean up the hunt ID from the URL
        $huntID = mysqli_real_escape_string($conn, $_GET['huntID']);
        $groupNo = mysqli_real_escape_string($conn, $_GET['groupNo']);

        //Check the start time has passed
        $result = $conn->query("SELECT start_time FROM Hunt WHERE id='{$huntID}'");
        if($result->num_rows > 0){
            while($row=$result->fetch_assoc()){
                if(time() < $row['start_time']){
                    die("error");
                }
            }
        }
        else {
            die("error"); //There is no such hunt ID
        }



        //Get the hunt name from the database
        $result = $conn->query("SELECT name FROM Hunt where id='{$huntID}'");
        
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                //Output the first two rows of a hunt file, the hunt name and its ID
                echo $row['name'] . "\n";
                echo $huntID . "\n";
            }
        }

        //Get the next location number for this group
        $result = $conn->query("SELECT next_location,found FROM HuntGroup WHERE number='{$groupNo}' AND hunt_id='{$huntID}'");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                //Output the next_location number (i.e the starting location for this group)
                //Make sure to increment it by 1, in the DB index runs from -1 to n - 2, in the app it runs from 0 to n - 1
                echo ($row['next_location'] + 1) . "\n";
                // Also output the number found to track the group's progress
                echo $row['found'] . "\n";
            }
        }

        //Get the list of locations from the database
        $result = $conn->query("SELECT * FROM Location WHERE hunt_id='{$huntID}' ORDER BY number");
        if($result->num_rows > 0){
            //Output the elements in the following order: name, clue, answer, lat, lng
            while($row = $result->fetch_assoc()){
                echo $row['name'] . "\n" . $row['clue'] . "\n" . $row['lat'] . "\n" . $row['lng'] . "\n";
            }
        }
    }
}