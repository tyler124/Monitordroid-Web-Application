<?php

/*
access.php
Includes encryption and hash functions for account authentication
Authors: Noah Lowenthal, Tyler Butler 
*/

include_once "db_functions.php";

$db = new DB_Functions();

if (!isset($_SESSION)) { session_start(); }

function userIsLoggedIn()
{
  global $db;
  
  if (isset($_POST['action']) and $_POST['action'] == 'login')
  {
    if (!isset($_POST['email']) or $_POST['email'] == '' or
      !isset($_POST['password']) or $_POST['password'] == '')
    {
      $_SESSION['loginError'] = 'Please fill in both fields';
      return FALSE;
    }

    $upassword = $_POST['password'];
    $uemail = $_POST['email'];
    $isvalid = FALSE;
    $hashedPassword = "";
    list ($isvalid, $hashedPassword) = databaseVerifyAccount($uemail, $upassword);
    if ($isvalid)
    {
      logUserIn($uemail, $hashedPassword);
      
      if (isset($_POST["keepmeloggedin"]) && $_POST["keepmeloggedin"] == "true") {
         keepMeLoggedIn($uemail);
      }

      if (isset($_POST["hash"]) && $_POST["hash"] != "") {
        header("Location: ./#" . $_POST["hash"]);
      }
      else {
        header("Location: ./");
      }
    }
    else
    {
      //session_start();
      unset($_SESSION['loggedIn']);
      unset($_SESSION['email']);
      unset($_SESSION['password']);
      $_SESSION['loginError'] = 'The specified email address or password was incorrect.';
      return FALSE;
    }
  }
  else if (isset($_SESSION['loggedIn']))
  {
    return $db->accountExists($_SESSION['email'], $_SESSION['password']);
  }
  else if (!isset($_POST['action'])) {
    return verifyKMLI();
  }
}

function logUserIn($user, $hashedPassword) {
  $_SESSION['loggedIn'] = TRUE;
  $_SESSION['email'] = $user;
  $_SESSION['password'] = $hashedPassword;
}

function generateRandomBase62String($length)
{
  if (!defined('MCRYPT_DEV_URANDOM')) die('The MCRYPT_DEV_URANDOM source is required (PHP 5.3).');
  $result = '';
  $remainingLength = $length;
  do
  {
    // We take advantage of the fast base64 encoding
    $binaryLength = (int)($remainingLength * 3 / 4 + 1);
    $binaryString = mcrypt_create_iv($binaryLength, MCRYPT_DEV_URANDOM);
    $base64String = base64_encode($binaryString);

    // Remove invalid characters
    $base62String = str_replace(array('+', '/', '='), '', $base64String);
    $result .= $base62String;

    // If too many characters have been removed, we repeat the procedure
    $remainingLength = $length - strlen($result);
  } while ($remainingLength > 0);
  return substr($result, 0, $length);
}

function keepMeLoggedIn($user) {
  global $db;
  $kmli = generateRandomBase62String(128);
  $db->setKMLIToken($user, $kmli);

  $cookie = $user . ':' . $kmli;
  $mac = hash_hmac('sha256', $cookie, SECRET_KEY);
  $cookie .= ':' . $mac;
  setcookie('rememberme', $cookie, time()+(1814400), "/", "www.monitordroid.com", true); // cookie should be good for three weeks
  return;
}

/**
 * A timing safe equals comparison
 *
 * To prevent leaking length information, it is important
 * that user input is always used as the second parameter.
 *
 * @param string $safe The internal (safe) value to be checked
 * @param string $user The user submitted (unsafe) value
 *
 * @return boolean True if the two strings are identical.
 */
function timingSafeCompare($safe, $user) {
    // Prevent issues if string length is 0
    $safe .= chr(0);
    $user .= chr(0);

    $safeLen = strlen($safe);
    $userLen = strlen($user);

    // Set the result to the difference between the lengths
    $result = $safeLen - $userLen;

    // Note that we ALWAYS iterate over the user-supplied length
    // This is to prevent leaking length information
    for ($i = 0; $i < $userLen; $i++) {
        // Using % here is a trick to prevent notices
        // It's safe, since if the lengths are different
        // $result is already non-0
        $result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
    }

    // They are only identical strings if $result is exactly 0...
    return $result === 0;
}

function verifyKMLI() {
  global $db;
  $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
  if ($cookie) {
    list ($user, $token, $mac) = explode(':', $cookie);
    $usertoken = $db->getKMLIToken($user);
    if (timingSafeCompare($usertoken, $token)) {
      $hashedPassword = $db->getPasswordByEmail($user);
      logUserIn($user, $hashedPassword);
      if (isset($_POST["hash"]) && $_POST["hash"] != "") {
        header("Location: ./#" . $_POST["hash"]);
      }
      else {
        header("Location: ./");
      }
      return TRUE;
    }
  }
  return FALSE;
}

function databaseVerifyAccount($email, $upassword)
{
  global $db;

  if (strlen($upassword) > 72) { die("Password must be 72 characters or less"); }

  if (!($db->accountEmailExists($email))) {
    return FALSE;
  }

  require_once("./lib/PasswordHash.php");
  $hasher = new PasswordHash(8, false);

  // Just in case the hash isn't found
  $hashedPassword = "*";

  $hashedPassword = $db->getPasswordByEmail($email);

  // Check that the password is correct, returns a boolean
  $check = $hasher->CheckPassword($upassword, $hashedPassword);

  return array($check, $hashedPassword);

}
