<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tomson
 * Date: 19/11/2016
 * Time: 23:16
 */

// Initialize the session.
session_start();

// Unset all of the session variables.
$_SESSION = array();

// Destroy the session.
session_destroy();

// Finally, redirect to homepage.
header("Location: index.php");
?>