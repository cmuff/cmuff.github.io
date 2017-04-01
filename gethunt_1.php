<?php
// TEST

/**
 * Created by PhpStorm.
 * User: sam
 * Date: 15/11/16
 * Time: 15:01
 */

//Set up database connection
include 'functions/core.php';

$conn = getDatabaseConnection();

function checkTimeAndOutputHuntName($huntID) {
    global $conn;
    
    //Check the start time has passed
    $result = $conn->query("SELECT name,start_time FROM Hunt WHERE id='{$huntID}'");
    if($result->num_rows <= 0){
        die("error");
    }
    $row = $result->fetch_assoc();
    if(time() < $row['start_time']){
        die("error");
    }

    // Output hunt name
    echo $row['name'] . "\n";
}

function getStartLocAndOutputNumberFound($huntID, $groupNo) {
    global $conn;
    
    //Get the next location number and number found for this group
    $result = $conn->query("SELECT next_location,found FROM HuntGroup WHERE number='{$groupNo}' AND hunt_id='{$huntID}'");
    if($result->num_rows <= 0){
        die("error");
    }
    $row = $result->fetch_assoc();
    // Output the number found to track the group's progress
    echo $row['found'] . "\n";
    
    return $row['next_location'];
}

function outputNormalLocations($huntID, $start_loc) {
    global $conn;
    
    // Output locations in the order the group will do them in
    $normLocCount = $conn->query("SELECT COUNT(*) as count FROM Location WHERE hunt_id='{$huntID}' AND number != -1;")->fetch_assoc()['count'];
    $result = $conn->query("SELECT * FROM Location WHERE hunt_id='{$huntID}' AND number != -1 ORDER BY (number+($normLocCount-$start_loc))%$normLocCount");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo $row['name'] . "\n" .
                    $row['clue'] . "\n" .
                    $row['lat'] . "\n" .
                    $row['lng'] . "\n";
        }
    }  
}

function outputEndLocation($huntID) {
    global $conn;
    
    $result = $conn->query("SELECT * FROM Location WHERE hunt_id='{$huntID}' AND number = -1 LIMIT 1");
    if($result->num_rows <= 0){
       die("error");
    }
    $row = $result->fetch_assoc();
    echo $row['name'] . "\n" .
            $row['clue'] . "\n" .
            $row['lat'] . "\n" .
            $row['lng'] . "\n";
}

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

        checkTimeAndOutputHuntName($huntID);

        $start_loc = getStartLocAndOutputNumberFound($huntID, $groupNo);
        
        outputNormalLocations($huntID, $start_loc);
        
        outputEndLocation($huntID);
        
    }
}