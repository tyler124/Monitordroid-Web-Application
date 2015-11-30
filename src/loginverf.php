<?php session_start();
//loginreq.php
include_once 'access.php';
if (!userIsLoggedIn()) {
    if ($_POST["hash"] != "") {
      header("Location: login.php#" . $_POST["hash"]);
    }
    else {
      header("Location: login.php");
    }
}

?>