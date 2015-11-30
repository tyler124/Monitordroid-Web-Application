<?php if (!isset($_SESSION)){ session_start(); }
if (!isset($_SESSION["email"])) {
    die();
}
define("LOG_FILE", "./jserrors.log");
error_log(date('[Y-m-d H:i] '). ": [" . $_SESSION["email"] . "] ERR: " . $_POST["msg"] . " @ " . $_POST["url"] . ":" . $_POST["linenumber"] . ":" . $_POST["column"] . " (" . $_POST["errorObj"] . ")" . PHP_EOL, 3, LOG_FILE);
?>