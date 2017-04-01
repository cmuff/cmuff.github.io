/**
 * Created by Tomson on 19/11/2016.
 */

const locForm = '<div class="{{className}}"> <h3>Edit Location</h3> <div class="labelInputPair"> <label for="locName{{locationNumber}}">Name:</label> <input id="locName{{locationNumber}}" type="text" maxlength="30" value="Location{{locationNumber}}" > </div><div class="labelInputPair"> <label for="locClue{{locationNumber}}">Clue:</label> <input id="locClue{{locationNumber}}" type="text" maxlength="150"> </div></div>';
var map; // Google maps object
var huntPath; // Google polyline object
var campus; // Google polygon object
var locations = []; // Array of (marker, loc form) pairs
var endPointLoc; // End point of the hunt stored separately from the location array.
var endPoint = false; // Indicates that the end point is currently being set.
// Google maps marker icons
var activeMarkerIcon;
var inactiveMarkerIcon;
var markerDragOldLocation;

// Runtime event listeners that aren't dependant on the google maps api
$('#finishButton').click(submitForm);
$('#addEndButton').click(function() {
    endPoint = true;
});

// initMap is called after all the google maps api scripts have loaded
function initMap() {
    // Create map in the map div which is centered on newcastle university
    // and zoomed in an appropriate amount
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 54.9796, lng: -1.6184},
        zoom: 16,
        clickableIcons: false
    });

    // Initialise marker icons
    activeMarkerIcon = {
        labelOrigin: new google.maps.Point(11, 11),
        url: 'img/active-marker.png',
        scaledSize: new google.maps.Size(22, 40)
    };
    inactiveMarkerIcon = {
        labelOrigin: new google.maps.Point(11, 11),
        url: 'img/inactive-marker.png',
        scaledSize: new google.maps.Size(22, 40)
    };

    var campusCoords = [
        {lat: 54.977901, lng: -1.618314},
        {lat: 54.976842, lng: -1.615203},
        {lat: 54.977901, lng: -1.614269},
        {lat: 54.978972, lng: -1.613218},
        {lat: 54.979662, lng: -1.612638},
        {lat: 54.981102, lng: -1.612531},
        {lat: 54.981127, lng: -1.611866},
        {lat: 54.980265, lng: -1.611887},
        {lat: 54.98125, lng: -1.609034},
        {lat: 54.981699, lng: -1.609055},
        {lat: 54.982173, lng: -1.609763},
        {lat: 54.9825, lng: -1.611845},
        {lat: 54.981964, lng: -1.612016},
        {lat: 54.981256, lng: -1.611855},
        {lat: 54.981367, lng: -1.612628},
        {lat: 54.982648, lng: -1.612993},
        {lat: 54.982746, lng: -1.613422},
        {lat: 54.982364, lng: -1.615138},
        {lat: 54.981755, lng: -1.616469},
        {lat: 54.982666, lng: -1.620224},
        {lat: 54.982038, lng: -1.620664},
        {lat: 54.982752, lng: -1.623131},
        {lat: 54.983399, lng: -1.622852},
        {lat: 54.983706, lng: -1.624075},
        {lat: 54.983122, lng: -1.625127},
        {lat: 54.983602, lng: -1.626715},
        {lat: 54.982918, lng: -1.627401},
        {lat: 54.981736, lng: -1.625524},
        {lat: 54.980862, lng: -1.624236},
        {lat: 54.98133, lng: -1.623678},
        {lat: 54.980702, lng: -1.620589},
        {lat: 54.981662, lng: -1.619988},
        {lat: 54.980985, lng: -1.616898},
        {lat: 54.979508, lng: -1.617241}
    ];

    campus = new google.maps.Polygon({
        paths: campusCoords,
        strokeColor: '#0ca76e',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#28f0a7',
        fillOpacity: 0.1
    });

    campus.setMap(map);

    // Add event listener for adding locations
    google.maps.event.addListener(campus, 'click', addLocation);

    // Initialise hunt path
    huntPath = new google.maps.Polyline();
}

function submitForm() {
    if (!emptyInputCheck()) {
        // Get end location inputs
        var endInputs = $(endPointLoc.dom).find("input");
        
        // Create JSON object we are going to post
        var data = {
            huntName : $("#huntName").val(),
            location : [],
            endLocation : {
                name: endInputs.get(0).value,
                clue: endInputs.get(1).value,
                lat : endPointLoc.marker.getPosition().lat(), 
                lng : endPointLoc.marker.getPosition().lng()
            }
        };
        locations.forEach(function(location){
            var inputs = $(location.dom).find("input");
            
            data.location.push({
                name : inputs.get(0).value,
                clue : inputs.get(1).value,
                lat : location.marker.getPosition().lat(),
                lng : location.marker.getPosition().lng()
                
            });
        });
        
        // Post the hunt
        var dataString = decodeURIComponent($.param(data));
        $.redirect('functions/create_hunt_action.php' + '?' + dataString, null, 'POST');
    }
}

function findEmptyInputs() {
    // Get array of empty inputs
    var empty = $(".locForm input, #huntName").filter(function () {
        return $(this).val() === "";
    });
    return empty;
}

function emptyInputCheck() {
    var empty = findEmptyInputs();
    
    if (empty.length > 0) {
        // Get form corresponding to input
        var locForm = empty.first().closest(".locForm").get(0);
        
	// Find index of loc form in locations array
	var i = findLocFormInLocationArray(locForm);
        
        // Show empty input
        changeActiveLocationByIndex(i);
    }
    
    return empty.length > 0;
}

function changeActiveLocationByIndex(i) {
    if (typeof i === 'undefined') {
        changeActiveLocation(endPointLoc.marker);
    }
    else {
        changeActiveLocation(locations[i].marker);
    }
}

function findLocFormInLocationArray(locForm) {
    var i;
    $.each(locations, function(index, loc) {
        if (loc.dom.get(0) === locForm) {
            i = index;
            return false;
        }
    });
    return i;
}

function addLocation(e) {
    // Run an alternative function instead if the end location is being added.
    if (endPoint) {
        endPoint = !endPoint;
        addEndLocation(e);
        return;
    }
    // Create marker using lat and lng of click
    var marker = new google.maps.Marker({
        position: e.latLng,
        map: map,
        label: "" + (locations.length + 1),
        draggable: true
    });

    // Add event listeners for marker
    google.maps.event.addListener(marker, 'dragstart', markerDragStart);
    google.maps.event.addListener(marker, 'dragend', markerDragEnd);
    google.maps.event.addListener(marker, 'click', changeActiveLocation);
    google.maps.event.addListener(marker, 'rightclick', removeLocation);

    // Get html for location form
    var html = Mustache.to_html(locForm, {className: "normal locForm", locationNumber: (locations.length + 1)});
    // Add html to page
    var $dom = $(html).appendTo('#locFormCon');

    // Add location to locations array
    locations.push({marker: marker, dom: $dom});

    // Update lines
    drawHuntPath();

    // Make marker just added active
    changeActiveLocation(marker);
}

function markerDragStart(e) {
    markerDragOldLocation = e.latLng;
}

function markerDragEnd(e) {
    if (google.maps.geometry.poly.containsLocation(e.latLng, campus)) {
        drawHuntPath();
    }
    else {
        this.setPosition(markerDragOldLocation);
    }
}

function addEndLocation(e) {
    // Create marker using lat and lng of click
    var marker = new google.maps.Marker({
        position: e.latLng,
        map: map,
        label: "E",
        draggable: true
    });

    $('#addEndButton').remove(); // Stop multiple end points being added
    $('#finishButton').addClass('active'); // Allow hunt to be submitted

    // Add event listener for marker
    google.maps.event.addListener(marker, 'click', changeActiveLocation);
    google.maps.event.addListener(marker, 'dragstart', markerDragStart);
    google.maps.event.addListener(marker, 'dragend', markerDragEnd);

    // Get html for location form
    var html = Mustache.to_html(locForm, {className: "end locForm", locationNumber: "End"});
    // Add html to page
    var $dom = $(html).appendTo('#locFormCon');

    // Set end location
    endPointLoc = {marker: marker, dom: $dom};

    changeActiveLocation(marker);
}

function removeLocation() {
    var marker = this;

    // Remove marker from map
    marker.setMap(null);

    // Find index of marker in locations array
    var i = getMarkerIndex(marker);

    // Remove location form
    locations[i].dom.remove();

    // Remove location from array
    locations.splice(i, 1);

    // Update lines
    drawHuntPath();

    updateMarkerNumbers();
}

function changeActiveLocation(marker) {
    // Marker parameter is optional
    if(typeof marker.getPosition === 'undefined') {
        // Assume function called from marker
        marker = this;
    }

    // Hide all location forms
    $('.locForm').removeClass('active');
    // Show appropriate location form
    var i = getMarkerIndex(marker);
    if (typeof i === 'undefined') {
        // Location form not found in locations array so must be end point
        endPointLoc.dom.addClass('active');
    }
    else {
        locations[i].dom.addClass('active');
    }

    flashLocationForm();

    updateMarkerIcons(marker);
}

function flashLocationForm() {
    // Flash location form red
    var locFormCon = $('#locFormCon');
    locFormCon.css('backgroundColor', '#FF7777');
    locFormCon.animate({backgroundColor: '#FFFFFF'}, 300);
}

function updateMarkerIcons(marker) {
    // Set all marker icons to inactive
    locations.forEach(function(loc) {
        loc.marker.setIcon(inactiveMarkerIcon);
    });
    if (endPointLoc) {
        endPointLoc.marker.setIcon(inactiveMarkerIcon);
    }

    // Set current marker icon to active
    marker.setIcon(activeMarkerIcon);
}

// Returns the index of the marker in the locations array
// If marker isn't found 'undefined' is returned
function getMarkerIndex(marker) {
    var i;
    $.each(locations, function(index, loc) {
        if (loc.marker == marker) {
            i = index;
            return false;
        }
    });
    return i;
}

function updateMarkerNumbers() {
    $.each(locations, function(index, loc) {
        loc.marker.setOptions({label: "" + (index + 1)});
    });
}

function drawHuntPath() {
    // Create list of co-ordinates for polyline
    var coords = [];
    if (locations.length > 0) {
        locations.forEach(function (loc) {
            coords.push(loc.marker.getPosition());
        });
        coords.push(locations[0].marker.getPosition());
    }

    // Remove old hunt path from map
    huntPath.setMap(null);

    // Create new hunt path
    huntPath = new google.maps.Polyline({
        path: coords,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    // Add new hunt path to map
    huntPath.setMap(map);
}