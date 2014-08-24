    <?php
	/**
	*postlocation.php
	*Written by: Tyler Butler
	*Email: butler.tyler.355@gmail.com
	**/
	
    // receive data from app's http request
    $data= $_POST["Latitude"];
	$data1= $_POST["Longitude"];
	$rName=$_POST["regName"];
	
	include_once './db_functions.php';
	$db = new DB_Functions();
	$db->insertLocation($rName, $data, $data1);
    ?>