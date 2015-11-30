<?php
if (isset($_POST["regId"]) && isset($_POST["message"])) {
    $regId = $_POST["regId"];
    $message = $_POST["message"];
    
    include_once './GCM.php';
    
    $gcm = new GCM();

    $registration_ids = array($regId);
    $message = array("price" => $message);

    $result = $gcm->send_notification($registration_ids, $message);

    echo $result;
}
?>