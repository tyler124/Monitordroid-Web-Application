    <?php

    // receive data from app's http request

    $data= $_POST["SMSSentData"];

	$rName= $_POST["regName"];

    // write data from my android app to a text file

   // file_put_contents('myTextFile.txt',$data);

 	//file_put_contents('phoneid.txt', $rName);

	

	include_once './db_functions.php';

	$db = new DB_Functions();

	$db->insertSentSMS($rName, $data);

    ?>