    <?php
    
    //Check if the app is signaling that this is the first SMS update
        if (isset($_POST["FirstUpdateData"])) {
    	$firstupdatedata = $_POST["FirstUpdateData"];
    }

	//Check if the app is signaling that this is not the first SMS update
    if (isset($_POST["SMSData"])) {
    	$smsdata = $_POST["SMSData"];
    }    

	$rName=$_POST["regName"];
	
	include_once './db_functions.php';
	$db = new DB_Functions();
	
	//If it's the first update, call the function which clears previous SMS data and inserts the new data into the beginning
	if (isset($firstupdatedata)) {
		$db->insertFirstSMS($rName, $firstupdatedata);
	}
	
	//If not, call the function that will concatenate the new SMS data onto the end of previous data
	else {
	$db->insertSMS($rName, $smsdata);
	}
    ?>