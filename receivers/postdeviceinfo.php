<?php

	$batteryLevel = $_POST["batteryLevel"];
    $phoneNumber = $_POST["phoneNumber"];
	$networkOperator = $_POST["networkOperator"];
	$radioType = $_POST["radioType"];
	$deviceName = $_POST["deviceName"];
	$wifiSSID = $_POST["wifiSSID"];
	$wifiIP = $_POST["wifiIP"];
	$monitordroidVersion = $_POST["monitordroidVersion"];
	$androidVersion = $_POST["androidVersion"];
	$rName=$_POST["regName"];



	include_once './db_functions.php';

	$db = new DB_Functions();

	$db->insertBatteryLevel($rName, $batteryLevel);
	$db->insertPhoneNumber($rName, $phoneNumber);
	$db->insertNetworkOperator($rName, $networkOperator);
	$db->insertRadioType($rName, $radioType);
	$db->insertDeviceName($rName, $deviceName);
	$db->insertWifiSSID($rName, $wifiSSID);
	$db->insertWifiIP($rName, $wifiIP);
	$db->insertMonitordroidVersion($rName, $monitordroidVersion);
	$db->insertAndroidVersion($rName, $androidVersion);
	
	