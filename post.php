    <?php
    	// receive data from app's http request
    	//The POST data is what the device sends back when you give it the "Update Contacts" command.
    	$data=$_POST["contactdata"];
	$rName=$_POST["regName"];
	
	//Posts Contacts list to database
	include_once './db_functions.php';
	$db = new DB_Functions();
	$db->insertContacts($rName, $data);
    ?>
