<?php
/**
 * Database config variables
 */
define("DB_HOST", "localhost");
//root is the default for xampp and wamp, but put whatever your MySQL username is here
define("DB_USER", "root");
define("DB_PASSWORD", "YOUR_DATABASE_PASSWORD_HERE");
//NOTE: If you do not create your gcm_users and gcm_accounts tables under database "monitordroid", change it here and in db_functions
define("DB_DATABASE", "monitordroid");

/*
 * Google API Key
 */
define("GOOGLE_API_KEY", "YOUR_GOOGLE_API_KEY_HERE");
?>