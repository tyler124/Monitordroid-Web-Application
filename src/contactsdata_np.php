<?php
if(!isset($_SESSION)) session_start();

    // receive data from HTML readsms request
	if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
		header("location:index.php");
		exit();		
	}

	$rName=$_SESSION["registration"];
	$rowId=$_SESSION["rowid"];

	include_once './db_functions.php';

	$db = new DB_Functions();
	$contactslist = $db->getContacts($rName);
	
	$mxr = explode("}", $contactslist);
	$paragraphs = $mxr[0];
	for ($i = 1; $i < count($mxr) - 1; $i += 1) {
		$paragraphs .= '},' . $mxr[$i];
	}
	

	if ($paragraphs == "") {
		echo "[]";
	}
	else {
		$paragraphs .= '}';
		echo '[' . stripslashes($paragraphs) . ']';
	}

    ?>