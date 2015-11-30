<?php 
session_start();
$x = $_POST["set"];
$_SESSION[$x] = $_POST[$x];
?>