<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 03/03/17
 * Time: 11:11
 */

//Set up database connection
include 'functions/core.php';

$conn = getDatabaseConnection();

//Check the GET method is being used
if($_SERVER['REQUEST_METHOD'] === "GET") {
    //Verify the API key has been passed, as well as huntID
    if ($_GET['apiKey'] === "team12apikey" && !empty($_GET['huntID'])) {
        //Clean up the huntID parameter
        $huntID = mysqli_real_escape_string($conn, $_GET['huntID']);

        //Fetch the results from the DB, order by number_found descending
        $results=$conn->query("SELECT number, found FROM HuntGroup WHERE hunt_id='{$huntID}' ORDER BY found DESC");

        //Output
        if($results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                echo $row['number'] . "," . $row['found'] . "\n";
            }
        }
        else {
            die("error");
        }
    }
}