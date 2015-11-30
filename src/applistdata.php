<?php
	if (!isset($_SESSION)) { session_start(); }
    if (!isset($_SESSION['email']) || !isset($_POST['rowid']) ||
      !isset($_POST['registration']) || !isset($_POST['reqtime']) ||
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

	function nl2p($string)
	{
	    $paragraphs = '';
	    $string .= "\n";

	    $mxr = explode("\n\n", $string);
	    for ($i = 0; $i < count($mxr); $i += 1) {
	    	$mystr = preg_replace("/(.*)?\n(.*)(?!.*\n)$/ms", "<a class=\"list-group-item\" href=\"https://play.google.com/store/apps/details?id=$2\">$1\n</a>", $mxr[$i]);
            $paragraphs .= $mystr;//'<p class="app-item">' . $mystr . '</p>';
	    }

	    return $paragraphs;
	}


    $email = $_SESSION['email'];
    $reg = $_POST["registration"];
    $rowId = $_POST["rowid"];
    $reqTime = intval($_POST["reqtime"]);

    include_once './db_functions.php';

    $db = new DB_Functions();

    if (! $db->userDeviceVerify($rowId, $email)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
        exit();
    }

    $updTime = 1;
    $updTime = $db->getAppsUpdateTime($reg);

    error_log("rtime " . $reqTime);
    error_log("updTime " . $updTime);
    if ($reqTime > $updTime) {
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
        exit();
    }
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");

	$appList = $db->getInstalledApps($reg);
	if ($appList == "") {
		echo "";
		exit();
	}
	$xdata = nl2p($appList);
	echo $xdata;
    ?>