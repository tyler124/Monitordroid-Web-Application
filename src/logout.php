<?php
	session_start();
    session_unset(); // just unset all session variables
    if (isset($_COOKIE['rememberme'])) {
        unset($_COOKIE['rememberme']);
        setcookie('rememberme', null, -1, "/", "www.monitordroid.com", true);
    }

	header("location:goodbye.php");
    exit();