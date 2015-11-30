    <?php
	
    // receive data from app's http request

    $lat= $_POST["Latitude"];
	$long = $_POST["Longitude"];
	$time= $_POST["Time"];
	$accuracy= $_POST["Accuracy"];
	$rName=$_POST["regName"];

	include_once './db_functions.php';

	$db = new DB_Functions();
	$db->insertLocation($rName, $lat, $long, $time, $accuracy);
	
	
    ?>