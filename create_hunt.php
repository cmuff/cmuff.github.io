<?php
include 'functions/core.php';

$staff_username = getStaffSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Hunt</title>
    <?php
    include 'htmlsnippets/head.html';
    ?>
    <link type="text/css" rel="stylesheet" href="css/create_hunt_test.css">
    <script src="http://code.jquery.com/color/jquery.color-2.1.2.min.js" integrity="sha256-H28SdxWrZ387Ldn0qogCzFiUDDxfPiNIyJX7BECQkDE=" crossorigin="anonymous"></script>
    <script src="js/jquery.redirect.js"></script>
</head>
<body>
    <?php
    include 'htmlsnippets/nav.html';
    ?>
    <div id="mapContainer">
        <div id="map"></div>
    </div>
    <div id="leftBar">
        <div id="help">
            ?
            <div id="helpPopup">
                <p>Click on map to add location.</p>
                <p>Click and drag marker to move location.</p>
                <p>Right click marker to remove location.</p>
            </div>
        </div>
    </div>
    <form id="form">
        <div id="rightBar">
            <div id="huntForm">
                <h2>Create Hunt</h2>
                <label for="huntName">Hunt Name: </label>
                <input id="huntName" type="text" name="huntName" maxlength="30">
            </div>
            <div id="locFormCon">
            </div>
            <div id="butCon">
                <div id="addEndButton">
                    Add End Point
                </div>
                <div id="finishButton">
                    Finish
                </div>
            </div>
        </div>
    </form>
    <script src="js/create_hunt.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPe0-EZh_FOPAWC342vyVFiwJWsTejMG0&callback=initMap" async defer></script>
</body>
</html>