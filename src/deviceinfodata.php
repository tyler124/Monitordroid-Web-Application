<?php

    // receive data from AJAX getDeviceInfo request
	$rName=$_POST["registration"];
	$rowId=$_POST["rowid"];

	include_once './db_functions.php';

	$db = new DB_Functions();
	$deviceInfoArray = $db->getDeviceInformation($rName);
	 ?><?php echo $deviceInfoArray['monitordroidversion']; ?>
	 
	<dl class="dl-horizontal">
		<dt>Phone Number</dt>
		<dd><?php echo $deviceInfoArray['phonenumber']; ?></dd>
		<dt>Network Operator</dt>
		<dd><?php echo $deviceInfoArray['networkoperator']; ?></dd>
		<dt>Radio Type</dt>
		<dd><?php echo $deviceInfoArray['radiotype']; ?></dd>
		<dt>Device Name</dt>
		<dd><?php echo $deviceInfoArray['devicename']; ?></dd>
		<dt>Wifi SSID</dt>
		<dd><?php echo stripslashes($deviceInfoArray['wifissid']); /* Had trouble with the quotes being escaped in HTML  */ ?></dd>
		<dt>Wifi IP</dt>
		<dd><?php echo $deviceInfoArray['wifiip']; ?></dd>
		<dt>Battery Level</dt>
		<dd><?php echo $deviceInfoArray['batterylevel']; ?></dd>
		<dt>Android Version</dt>
		<dd><?php echo $deviceInfoArray['androidversion']; ?></dd>
	</dl>
<?php    ?>