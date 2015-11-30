<?php 
    if (!isset($_SESSION)) { session_start(); }
    if (!isset($_SESSION['email']) || !isset($_POST['rowid']) ||
      !isset($_POST['registration']) || !isset($_POST['reqtime']) ||
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

    // locationlogdata.php

    $email = $_SESSION['email'];
    $reg = $_POST["registration"];
    $rowId = $_POST["rowid"];
    $reqTime = intval($_POST["reqtime"]);

    include_once './db_functions.php';

    $db = new DB_Functions();

    if (! $db->userDeviceVerify($rowId, $email)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
        exit();
    }

    $updTime = 1;
    $updTime = $db->getLocationUpdateTime($reg);

    //error_log("rtime " . $reqTime);
    //error_log("updTime " . $updTime);
    if ($reqTime > $updTime) {
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
        exit();
    }
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");

    //Gets formatted list of locations
    $loclogs = stripslashes($db->getLocationLogs($reg));
    //Splits up locations into individual locations in form "{32.111122, -84.322351}"
    //$locations = explode(":", $loclogs);
    $locations = explode(":{", $loclogs);
    $numberOfLocations = count($locations) - 1;//Because of the way the location log is formatted, begin at entry 1 instead of entry 0
    //Create an array, $rawLatLong, which is filled with latitude and longitude tuples in the form "32.111122,-84.322351"
    //$rawLatLong;
    $locationData;

    $nameArray = array("latitude", "longitude", "locationtime", "locationaccuracy");
    //if ($numberOfLocations > 1) {
    for ($i = 0; $i < $numberOfLocations; $i++) {
        //in theory not necessary $ourTuple = str_replace('{', '', $locations[$i + 1]); //Because of the way the location log is formatted, begin at entry 1 instead of entry 0
        $ourTuple =  str_replace('}', '', $locations[$i + 1]);
        $ourTuple = explode(',', $ourTuple);

        $currData;
        for ($p = 0; $p < count($ourTuple); $p++) {
            //error_log("assigning " . $nameArray[$p] . " to " . $ourTuple[$p]);
            $currData[$nameArray[$p]] = $ourTuple[$p];            
        }
        
        $locationData[$i] = $currData;
    // add most recent location to our locationlog
    }
    
    $locationData[$i] = $db->getLocationData($reg);
    if ($locationData[$i]["latitude"] == 0 && $locationData[$i]["longitude"] == 0 &&
        $numberOfLocations == 0) {
        echo "{}";

    } else {

        //$xlast = $locationData[$i]['latitude'] . ',' . $locationData[$i]['longitude'];

        echo json_encode($locationData);
    }

    ?>