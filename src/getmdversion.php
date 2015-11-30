<?php if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION["email"]) || !isset($_SESSION["registration"])){
    http_response_code(403); // forbidden
    die();
}
include_once 'db_functions.php';
$db = new DB_Functions();

$regId = $_SESSION["registration"];
$rowid = $_SESSION["rowid"];

$dataVersion = $db->getDataVersion($regId, $rowid);
$mdVersion = $db->getMDVersion($regId, $rowid);

if ($mdVersion != $dataVersion) {
    $db->clearDeviceData($regId);
    $db->setDataVersion($regId, $mdVersion);
    echo ($mdVersion . " Updated");
}
else {
    echo $mdVersion;
}

?>