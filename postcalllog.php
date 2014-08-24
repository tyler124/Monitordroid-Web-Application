    <?php
    	// receive data from app's http request
    	$data= $_POST["LogData"];
	$rName=$_POST["regName"];
	
	include_once './db_functions.php';
	$db = new DB_Functions();
	$db->insertCallLogs($rName, $data);
    ?>
