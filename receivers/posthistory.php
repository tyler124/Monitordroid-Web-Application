<?php

   //Check if the app is signaling that this is the first browser history update
    if (isset($_POST["FirstUpdateData"])) {
    	$firstupdatedata = $_POST["FirstUpdateData"];
    }

	//Check if the app is signaling that this is not the first browser history update
    if (isset($_POST["browserHistory"])) {
    	 $browserHistory= $_POST["browserHistory"];
    }    
   
	$rName=$_POST["regName"];



	include_once './db_functions.php';

	$db = new DB_Functions();
    
	if (isset($firstupdatedata)) {
		$db->insertFirstBrowserHistory($rName, $firstupdatedata);
	}
	
	else {
		$db->insertBrowserHistory($rName, $browserHistory);
	}
	
    ?>