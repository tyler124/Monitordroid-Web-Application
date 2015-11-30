<?php session_start();
if (!isset($_SESSION["amountToLoad"])){
	$_SESSION["amountToLoad"] = 60;
}
if (isset($_GET["regId"]) && isset($_GET["message"])) {
	if (isset($_GET["loadMore"])) {
    	$_SESSION["amountToLoad"] += 40;
    }
    $regId = $_GET["regId"];
    $message = $_GET["message"] + "-" + $_SESSION["amountToLoad"];

    include_once './GCM.php';
    
    $gcm = new GCM();

    $registration_ids = array($regId);
    $message = array("price" => $message);

    $result = $gcm->send_notification($registration_ids, $message);

    echo $result;
}
?>
