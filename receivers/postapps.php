<?php

    $installedApps = $_POST["installedApps"];
	$rName=$_POST["regName"];

	include_once './db_functions.php';

	$db = new DB_Functions();

	$db->insertInstalledApps($rName, $installedApps);