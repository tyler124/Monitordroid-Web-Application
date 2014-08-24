<?php
	$rowId=$_POST["rowid"];
	
	include_once './db_functions.php';
	$db = new DB_Functions();
	$db->deleteDevice($rowId);
	?>
   <html>
   <title>Device Deleted</title>
   <body>
   <a href="javascript:history.back()">Back</a>
   
   <h2>Device successfully deleted!</h2>
   </body>
   </html>