<?php
class DB_Functions {

	/*
	db_functions.php
	Provides functions to interact with the MySQL database
	Authors: Tyler Butler, Noah Lowenthal
	*/
	
	private $db;
	// constructor
	function __construct() {

		require_once 'config.php';
		// connecting to mysql
		

		try {

			//NOTE: If you created your gcm_users and gcm_accounts tables under a different database name,
			//change 'monitordroid' in the below statement to whatever database name you created them under!
			$this->db = new PDO('mysql:host=localhost;dbname=monitordroid', DB_USER, DB_PASSWORD);

		}

		catch (PDOException $e) {

			$output = 'Unable to connect to database server.';
			error_log($output);

			$e->getMessage();

			exit();

		}

	}
// destructor
function __destruct() {
}
	//insert device into database
	public function storeUser($name, $email, $gcm_regid) {	try {

    //Only insert device if the account exists
	if ($this->accountEmailExists($email)) {

		try {
			$sql = "INSERT INTO gcm_users(name, email, gcm_regid, created_at) VALUES('$name', '$email', '$gcm_regid', NOW())";
			$result = $this->db->query($sql);

			if ($result) {
				// get user details
				$id = $this->db->lastInsertId(); //last inserted id
				$sql = "SELECT * FROM gcm_users WHERE id = $id";
				$result = $this->db->query($sql);
				$no_of_rows = $result->fetchColumn();
				// returns confirmation message if completed
				if ($no_of_rows > 0) {
					return "existsandcompleted";
				}
				else {
					return "didnotcomplete";
				}
			}
	 		else {
				return "didnotcomplete";
	 		}
		}

		catch (PDOException $e) {
			$error = 'Error storing user: ' . $e->getMessage();
			}
		}
	else {
		//return false if input email is not an existing account
		return "didnotcomplete";
	}
	}
	catch (Exception $e) {
		$error = 'Error accessing database: ' . $e->getMessage();
	}
  }
	//Get user by email or password

	public function getUserByEmail($email) {
	try {

		$sql = "SELECT * FROM gcm_users WHERE email = '$email'";

		$result = $this->db->query($sql);

		return $result;

		}

		catch (PDOException $e) {

			$error = 'Error fetching user by email: ' . $e->getMessage();

		}

	}
	//Returns all users

	public function getAllUsers() {
	try {
		$sql = "select * FROM gcm_users";

		$result = $this->db->query($sql);

		return $result;

		}
		catch (PDOException $e) {

			$error = 'Error getting all users: ' . $e->getMessage();

		}

	}
	
	public function getAllAccounts() {
		
		try {

			$sql = "SELECT email FROM gcm_accounts";

			$resource = $this->db->query($sql);

			//$resultArray = $resource->fetch(PDO::FETCH_ASSOC);
			$resultArray = $resource->fetchAll();	}

		catch (PDOException $e) {

			$output = 'Error fetching sms outbox: ' . $e->getMessage();

		}

		return $resultArray;
	}

	//Check if user exists

	public function isUserExisted($email) {
		try {
			$sql = "SELECT email from gcm_users WHERE email = '$email'";

			$result = $this->db->query($sql);

			$no_of_rows = $result->fetchColumn();
		if ($no_of_rows > 0) {

			// user exists

			return true;

		}

        else {

			// user doesn't exist

			return false;

	   }

	}
		catch (PDOException $e) {

			$error = 'Error fetching user by email: ' . $e->getMessage();

		}

	}
	//Insert Contacts into DB

	public function insertContacts($regId, $contacttext) {
		try {

			$sql = "UPDATE gcm_users SET contacts = :ctext, contactsupdatetime = UNIX_TIMESTAMP()
					WHERE gcm_regid = '$regId'";
			
			$s = $this->db->prepare($sql);

			$s->bindParam(':ctext', $contacttext);

			$s->execute();
		}
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
	//Insert SMS data into DB (When "Load More Messages" is pressed, concatenating these messages onto previous ones)

	public function insertSMS($regId, $smsdata) {
		try {
				
			$sql = "UPDATE gcm_users SET smsdata = concat(ifnull(smsdata, ''), :smstext), smsupdatetime = UNIX_TIMESTAMP()
				    WHERE gcm_regid = '$regId'";
			
			$s = $this->db->prepare($sql);

			$s->bindParam(':smstext', $smsdata);

			$s->execute();

		}	catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
	//Insert SMS data into DB (When "Update" is pressed initially, clearing other SMS data and inserting these as the first messages to maintain proper order)

	public function insertFirstSMS($regId, $firstsmsdata) {
		try {

			$sql = "UPDATE gcm_users SET smsdata = :smstext, smsupdatetime = UNIX_TIMESTAMP()
			        WHERE gcm_regid = '$regId'";

			$s = $this->db->prepare($sql);

			$s->bindParam(':smstext', $firstsmsdata);

			$s->execute();

		}
	catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
	//Insert Call Logs into DB

	public function insertCallLogs($regId, $logData) {
		try {

			$sql = "UPDATE gcm_users SET calllogs = :logtext, calllogsupdatetime = UNIX_TIMESTAMP()
					WHERE gcm_regid = '$regId'";

			$s = $this->db->prepare($sql);

			$s->bindParam(':logtext', $logData);

			$s->execute();

		}
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
	
	//Inserts the first iteration browser history into DB.
	//Will clear the previously stored data and replace with the newest 50 links
	
	public function insertFirstBrowserHistory($regId, $browserHistory) {
		
		try {
			$sql = "UPDATE gcm_users SET browserhistory = :browserhistory, browserhistoryupdatetime = UNIX_TIMESTAMP()
					WHERE gcm_regid = '$regId'";
			
			$s = $this->db->prepare($sql);
			
			$s->bindParam(':browserhistory', $browserHistory);
			
			$s->execute();
			
		}
		
		catch (PDOException $e) {
			$output = 'Error performing update: ' . $e->getMessage();
		}
	}
	
	
		//Inserts the following iterations of browser history,
		//concatenating 50 messages at a time into the database
	
		public function insertBrowserHistory($regId, $browserHistory) {

		try {
				
			$sql = "UPDATE gcm_users SET browserhistory = concat(ifnull(browserhistory, ''), :browsertext), browserhistoryupdatetime = UNIX_TIMESTAMP()
				    WHERE gcm_regid = '$regId'";
			
			$s = $this->db->prepare($sql);

			$s->bindParam(':browsertext', $browserHistory);

			$s->execute();

		}
	catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
		//Insert installed applications into DB
	
	public function insertInstalledApps($regId, $installedApps) {
		
		try {
			$sql = "UPDATE gcm_users SET apps = :installedapps, appsupdatetime = UNIX_TIMESTAMP()
				    WHERE gcm_regid = '$regId'";
			
			$s = $this->db->prepare($sql);
			
			$s->bindParam(':installedapps', $installedApps);
			
			$s->execute();
			
		}
		
		catch (PDOException $e) {
			$output = 'Error performing update: ' . $e->getMessage();
		}
	}

	//insert location data into DB

	public function insertLocationData($regId, $latitude, $longitude, $time, $accuracy) {
		try {

			$sql1 = "UPDATE gcm_users SET latitude = :lat, longitude = :long, locationtime = :locationtime, locationaccuracy = :locationaccuracy, locationupdatetime = UNIX_TIMESTAMP()
			         WHERE gcm_regid = '$regId'";

			$s = $this->db->prepare($sql1);

			$s->bindParam(':lat', $latitude);
			$s->bindParam(':long', $longitude);
			$s->bindParam(':locationtime', $time);
			$s->bindParam(':locationaccuracy', $accuracy);
			$s->execute();
	}
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
	public function insertLocation($regId, $latitude, $longitude, $time, $accuracy) {
		try {
			
			//First get the current location data, then insert them into the logs column
			$currentLocationData = $this->getLocationData($regId);
			$currentLat = $currentLocationData['latitude'];
			$currentLong = $currentLocationData['longitude'];
			$currentLocationTime = $currentLocationData['locationtime'];
			$currentLocationAccuracy = $currentLocationData['locationaccuracy'];
			
			if ($currentLat != 0 || $currentLong != 0) {
			$formattedLocationData = ":" . "{" . $currentLat . "," . $currentLong . "," . $currentLocationTime . "," . $currentLocationAccuracy . "}";
			//Insert old location into location logs database column
			$this->insertLocationLogs($regId, $formattedLocationData);
			}
			//Insert new location into latitude and longitude database columns 
			$this->insertLocationData($regId, $latitude, $longitude, $time, $accuracy);

			}
			
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}

	}
	
	public function insertLocationLogs($regId, $formattedLatLong) {
		
		try {
			
			//First check the current size of the location logs
			$locLogs = $this->getLocationLogs($regId);
			$currentByteSize = strlen($locLogs);
			
			//Due to database limits, if the current size is over 61kb wipe the column and insert the new data
			//Gives a max of around 700 locations
			if ($currentByteSize > 61000) {
				$sql = "UPDATE gcm_users SET locationlogs = :formattedLatLong WHERE gcm_regid = '$regId'";
			}
			
			else {
			//Else concat the new logs onto the end of the current location logs data
			$sql = "UPDATE gcm_users SET locationlogs = concat(ifnull(locationlogs, ''), :formattedLatLong) WHERE gcm_regid = '$regId'";
			}
			
			$s = $this->db->prepare($sql);

			$s->bindParam(':formattedLatLong', $formattedLatLong);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}

	public function clearLocationLogs($regId) {
		
		try {
			
			$sql = "UPDATE gcm_users SET locationlogs = '' WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql);
			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}
	public function clearDeviceData($regId) {
		
		try {
			
			$sql = "UPDATE gcm_users SET contacts = '', locationlogs = '', smsdata = '', smssentdata = '', calllogs = '', browserhistory = '', apps = '', latitude = '', longitude = '', locationtime = '', locationaccuracy = '', picturedir = '' WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql);
			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}

	public function insertBatteryLevel($regId, $batteryLevel) {
		
		try {
			$sql = "UPDATE gcm_users SET batterylevel = :batterylevel WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':batterylevel', $batteryLevel);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}	
	
	public function insertPhoneNumber($regId, $phoneNumber) {
		
		try {
			$sql = "UPDATE gcm_users SET phonenumber = :phonenumber WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':phonenumber', $phoneNumber);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}			
	
	public function insertNetworkOperator($regId, $networkOperator) {
		
		try {
			$sql = "UPDATE gcm_users SET networkoperator = :networkoperator WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':networkoperator', $networkOperator);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}
	
	public function insertRadioType($regId, $radioType) {
		
		try {
			$sql = "UPDATE gcm_users SET radiotype = :radiotype WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':radiotype', $radioType);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}

	public function insertDeviceName($regId, $deviceName) {
		
		try {
			$sql = "UPDATE gcm_users SET devicename = :devicename WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':devicename', $deviceName);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}
	
	public function insertWifiSSID($regId, $wifiSSID) {
		
		try {
			$sql = "UPDATE gcm_users SET wifissid = :wifissid WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':wifissid', $wifiSSID);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}
	
	public function insertWifiIP($regId, $wifiIP) {
		
		try {
			$sql = "UPDATE gcm_users SET wifiip = :wifiip WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':wifiip', $wifiIP);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}
	
	public function insertMonitordroidVersion($regId, $monitordroidVersion) {
		
		try {
			$sql = "UPDATE gcm_users SET monitordroidversion = :monitordroidversion WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':monitordroidversion', $monitordroidVersion);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}
	
	public function insertAndroidVersion($regId, $androidVersion) {
		
		try {
			$sql = "UPDATE gcm_users SET androidversion = :androidversion WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':androidversion', $androidVersion);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}	
	
	public function insertPictureDir($regId, $pictureDir) {
		
		try {
			$sql = "UPDATE gcm_users SET picturedir = :picturedir, picturedirupdatetime = UNIX_TIMESTAMP()
					WHERE gcm_regid = '$regId'";;

			$s = $this->db->prepare($sql);

			$s->bindParam(':picturedir', $pictureDir);

			$s->execute();
		}
		
		catch (PDOException $e) {

			$output = 'Error performing update: ' . $e->getMessage();

		}
	}				
	
	//Get all contacts from DB
	public function getContacts($regId) {
		try {

			$sql = "SELECT contacts FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['contacts'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching contacts: ' . $e->getMessage();

		}

		return $result;

	}
	//Get all SMS Inbound messages from DB

	public function getSMSInbox($regId) {
		try {

			$sql = "SELECT smsdata FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['smsdata'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching sms data: ' . $e->getMessage();

		}

		return $result;

	}
	//Get all SMS Outbound messages from DB

	public function getSMSSent($regId) {
		try {

			$sql = "SELECT smssentdata FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['smssentdata'];

	}

		catch (PDOException $e) {

			$output = 'Error fetching sms outbox: ' . $e->getMessage();

		}

		return $result;

	}
	//Get CallLogs from DB

	public function getCallLogs($regId) {
		try {

			$sql = "SELECT calllogs FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['calllogs'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching logs: ' . $e->getMessage();

		}

		return $result;

		}
	
	//Get Browser History from DB
	
	public function getBrowserHistory($regId) {
		
		try {
				
			$sql = "SELECT browserhistory FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['browserhistory'];			
		}

		catch (PDOException $e) {

			$output = 'Error fetching browser history: ' . $e->getMessage();

		}

		return $result;

		}
	
	//Get latitude from DB

	public function getLat($regId) {
		try {

			$sql = "SELECT latitude FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['latitude'];

		}
		catch (PDOException $e) {

				$output = 'Error fetching location: ' . $e->getMessage();

		}

		return $result;

	}
	//Get longitude from DB

	public function getLong($regId) {
		try {

			$sql = "SELECT longitude FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['longitude'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching location: ' . $e->getMessage();

			}
		return $result;

		}

	//Get location logs from DB

	public function getLocationLogs($regId) {
		try {

			$sql = "SELECT locationlogs FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['locationlogs'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching location: ' . $e->getMessage();

			}
		return $result;

		}
	
	//Get installed applications from DB

	public function getInstalledApps($regId) {
		try {

			$sql = "SELECT apps FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['apps'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching installed apps: ' . $e->getMessage();

			}
		return $result;

	}

	//Gets the pictures directory on the device from the database
	public function getPictureDir($regId) {
		try {

			$sql = "SELECT picturedir FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['picturedir'];

		}
		catch (PDOException $e) {

			$output = 'Error fetching picture directory: ' . $e->getMessage();

			}
		return $result;

	}

	//Get phone number, network operator, radio type, device name, wifi SSID, and wifi IP from DB
	//NOTE: Returns an array of string results corresponding to the database column instead of a single string
	
	public function getDeviceInformation($regId) {
			
			
		try {

			$sql = "SELECT phonenumber, networkoperator, radiotype, devicename, wifissid, wifiip, batterylevel, monitordroidversion, androidversion FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching device information: ' . $e->getMessage();

			}
		return $resultArray;

	}
	public function getMDVersion($regId, $rowid) {
			
			
		try {

			$sql = "SELECT monitordroidversion FROM gcm_users WHERE gcm_regid = '$regId' AND id = '$rowid'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$mdver = $resultArray["monitordroidversion"];

		}
		catch (PDOException $e) {

			$output = 'Error fetching md version: ' . $e->getMessage();

		}

		return $mdver;

	}

	public function getDataVersion($regId, $rowid) {
			
		$dver;
		try {

			$sql = "SELECT data_version FROM gcm_users WHERE gcm_regid = '$regId' AND id = '$rowid'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$dver = $resultArray["data_version"];

		}
		catch (PDOException $e) {

			$output = 'Error fetching data version: ' . $e->getMessage();

		}

		return $dver;

	}
	public function setDataVersion($regId, $dataversion) {
		try {

			$sql = "UPDATE gcm_users SET data_version='$dataversion' WHERE gcm_regid = '$regId'";

			$s = $this->db->prepare($sql);
			$s->execute();

		}
		catch (PDOException $e) {

			$output = 'Error setting data version: ' . $e->getMessage();

		}
		return;
	}

	//Get the an array of the times (in seconds since UNIX epoch) that contacts, SMS, call logs, browser history, apps list, location, and the current file directory were last updated
	//NOTE: Returns an array of string results corresponding to the database column instead of a single string
	public function getUpdateTimes($regId) {
		
		try {

			$sql = "SELECT contactsupdatetime, smsupdatetime, calllogsupdatetime, browserhistoryupdatetime, appsupdatetime, 
					locationupdatetime, picturedirupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching updatetimes: ' . $e->getMessage();

			}
		return $resultArray;

	}

	public function getContactsUpdateTime($regId) {
		
		try {

			$sql = "SELECT contactsupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching contactsupdatetime: ' . $e->getMessage();

			}
		return $resultArray["contactsupdatetime"];

	}
	public function getPictureDIRUpdateTime($regId) {
		
		try {

			$sql = "SELECT picturedirupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching picturedirupdatetime: ' . $e->getMessage();

			}
		return $resultArray["picturedirupdatetime"];

	}
	public function getSMSUpdateTime($regId) {
		
		try {

			$sql = "SELECT smsupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching smsupdatetime: ' . $e->getMessage();

			}
		return $resultArray["smsupdatetime"];

	}

	public function getBrowserHistoryUpdateTime($regId) {
		
		try {

			$sql = "SELECT browserhistoryupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching browserhistoryupdatetime: ' . $e->getMessage();

			}
		return $resultArray["browserhistoryupdatetime"];

	}

	public function getCallLogsUpdateTime($regId) {
		
		try {

			$sql = "SELECT calllogsupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching calllogsupdatetime: ' . $e->getMessage();

			}
		return $resultArray["calllogsupdatetime"];

	}

	public function getAppsUpdateTime($regId) {
		
		try {

			$sql = "SELECT appsupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching appsupdatetime: ' . $e->getMessage();

			}
		return $resultArray["appsupdatetime"];

	}
	
	public function getLocationUpdateTime($regId) {
		
		try {

			$sql = "SELECT locationupdatetime FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching locationupdatetime: ' . $e->getMessage();

			}
		return $resultArray["locationupdatetime"];

	}

	
	public function getLocationData($regId) {
			
			
		try {

			$sql = "SELECT latitude, longitude, locationtime, locationaccuracy FROM gcm_users WHERE gcm_regid = '$regId'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

		}
		catch (PDOException $e) {

			$output = 'Error fetching location data: ' . $e->getMessage();

			}
		return $resultArray;

	}

	public function accountExists($email, $password) {
		try {
			$sql = 'SELECT COUNT(*) FROM gcm_accounts WHERE email = :email AND password = :password';

			$s = $this->db->prepare($sql);

			$s->bindParam(':email', $email);

			$s->bindParam(':password', $password);

			$s->execute();

		}
		catch (PDOException $e) {

				$output = 'Error fetching account: ' . $e->getMessage();

		}
		$row = $s->fetch();
		if ($row[0] > 0) {

			return TRUE;

		}

		else {

			return FALSE;

		}

	}
	public function accountEmailExists($email) {
		try {
			$sql = 'SELECT COUNT(*) FROM gcm_accounts WHERE email = :email';

			$s = $this->db->prepare($sql);

			$s->bindParam(':email', $email);

			$s->execute();

		}
		catch (PDOException $e) {

				$output = 'Error fetching account: ' . $e->getMessage();

		}

		$row = $s->fetch();
		if ($row[0] > 0) {

			return TRUE;

		}

		else {

			return FALSE;

		}

	}
	public function deleteDevice($rowId) {

		try {

			$sql = 'DELETE FROM gcm_users WHERE id = :id';

			$s = $this->db->prepare($sql);

			$s->bindParam(':id', $rowId);

			$s->execute();

		}

		catch (PDOException $e) {

		}
	}

	public function deleteDeviceVerify($rowId, $email) {
		try {
			$sql = "DELETE FROM gcm_users WHERE id = '$rowId' AND email = '$email'";
			$s = $this->db->prepare($sql);
			$s->execute();
			return TRUE;

		}

		catch (PDOException $e) {
			return FALSE;

		}
	}
	
	public function changeDeviceName($rowId, $uemail, $newName) {
	// changes device name (user name) given a rowId and email. The email is used for verification that the user has that ability.
		try {
			$sql = "UPDATE gcm_users SET name = '$newName' WHERE id = '$rowId' AND email = '$uemail'";
			$s = $this->db->prepare($sql);
			$s->execute();
			return TRUE;
		}

		catch (PDOException $e) {
			return FALSE;
		}
	}

	public function userDeviceVerify($rowId, $email) {
		try {
			$sql = "SELECT COUNT(*) FROM gcm_users WHERE id = '$rowId' AND email = '$email'";
			$s = $this->db->prepare($sql);
			$s->execute();
		}
		catch (PDOException $e) {
			$output = 'Error fetching device verify: ' . $e->getMessage();
		}

		$row = $s->fetch();

		if ($row[0] > 0) {
			return TRUE;
		}

		else {
			return FALSE;
		}
	}


	//Get the raw password of an account by it's email
	public function getPasswordByEmail($email) {

		try {

			$sql = "SELECT password FROM gcm_accounts WHERE email = '$email'";

			$query = $this->db->query($sql);

			$resultArray = $query->fetch(PDO::FETCH_ASSOC);

			$password = $resultArray['password'];

			return $password;

		}
		catch (PDOException $e) {

			$output = 'Error fetching password: ' . $e->getMessage();

		}

	}
	//Get the raw password of an account by it's row id

	public function getPasswordByID($rowId) {
		try {

			$sql = "SELECT password FROM gcm_accounts WHERE id = '$rowId'";

			$query = $this->db->query($sql);

			$resultArray = $query->fetch(PDO::FETCH_ASSOC);

			$password = $resultArray['password'];

			return $password;

		}
		catch (PDOException $e) {

			$output = 'Error fetching password: ' . $e->getMessage();

		}

	}
	public function changePasswordById($rowId, $newpass) {

		try {

			$sql = "UPDATE gcm_accounts SET password = :newpass WHERE id = '$rowId'";

			$s = $this->db->prepare($sql);

			$s->bindParam(':newpass', $newpass);

			$s->execute();

		}

		catch (PDOException $e) {

		}

	}
	public function changePassword($email, $newpass) {

		try {

			$sql = "UPDATE gcm_accounts SET password = :newpass WHERE email = '$email'";

			$s = $this->db->prepare($sql);

			$s->bindParam(':newpass', $newpass);

			$s->execute();

		}

		catch (PDOException $e) {

		}

	}

	public function changeCountry($email, $newcountry) {
		// for converting trial accounts
		// change country by email

		try {

			$sql = "UPDATE gcm_accounts SET country = :newcountry WHERE email = '$email'";

			$s = $this->db->prepare($sql);

			$s->bindParam(':newcountry', $newcountry);

			$s->execute();

		}

		catch (PDOException $e) {

		}

	}
	public function setKMLIToken($email, $kmli) {
		// mutator for the keep me logged in token
		try {

			$sql = "UPDATE gcm_accounts SET kmli_tok = '$kmli' WHERE email = '$email'";

			$s = $this->db->prepare($sql);

			$s->execute();

		}

		catch (PDOException $e) {
			error_log("couldn't set kmli token");

		}
	}
	public function getKMLIToken($email) {
		// accessor for the keep me logged in token
		try {
			$sql = "SELECT kmli_tok FROM gcm_accounts WHERE email = '$email'";

			$query = $this->db->query($sql);

			$resultArray = $query->fetch(PDO::FETCH_ASSOC);

			return $resultArray["kmli_tok"];

		}

		catch (PDOException $e) {

		}
	}

	public function setToken($email, $newtoken) {
		// change token by email & update time

		try {

			$newtokentime = date("Y-m-d H:i:s");

			$sql = "UPDATE gcm_accounts SET reset_token = :newtoken, token_gen_at = :newtokentime WHERE email = '$email'";

			$s = $this->db->prepare($sql);

			$s->bindParam(':newtoken', $newtoken);

			$s->bindParam(':newtokentime', $newtokentime);

			$s->execute();

		}

		catch (PDOException $e) {

		}

	}

	public function getTokenInfo($email) {
		// change token by email & update time

		try {
			$sql = "SELECT reset_token, token_gen_at FROM gcm_accounts WHERE email = '$email'";

			$query = $this->db->query($sql);

			$resultArray = $query->fetch(PDO::FETCH_ASSOC);

			return $resultArray;

		}

		catch (PDOException $e) {

		}

	}

	public function expireToken($email) {
		// make token unusable by expiring it

		try {
			$sql = "SELECT token_gen_at FROM gcm_accounts WHERE email = '$email'";

			$query = $this->db->query($sql);

			$resultArray = $query->fetch(PDO::FETCH_ASSOC);

			$tokenGenTimeStr = $resultArray["token_gen_at"];
			$tokenGenTimeObj = new DateTime($tokenGenTimeStr);

			$tokenGenTimeObj->modify('-2 days');

			$newtokentime = $tokenGenTimeObj->format("Y-m-d H:i:s");

			error_log($newtokentime);

			$sql2 = "UPDATE gcm_accounts SET token_gen_at = :newtokentime WHERE email = '$email'";

			$s = $this->db->prepare($sql2);

			$s->bindParam(':newtokentime', $newtokentime);

			$s->execute();

		}

		catch (PDOException $e) {
			error_log("error type" . $e);
		}

	}

	public function addAccount($accountName, $newpass, $contactemail, $name, $country) {

		try {

			$sql = "INSERT INTO gcm_accounts(email, password, maxdevices, contactemail, name, country, created_at) VALUES(:accountName, :newpass, 10, :contactemail, :name, :country, NOW())";

			$s = $this->db->prepare($sql);

			$s->bindParam(':accountName', $accountName);

			$s->bindParam(':newpass', $newpass);

			$s->bindParam(':contactemail', $contactemail);

			$s->bindParam(':name', $name);

			$s->bindParam(':country', $country);

			$s->execute();
			
			//Make a file repository for user's pictures and recordings
			$uploads_dir = "/home3/monitot5/files/";
			mkdir($uploads_dir.$accountName);

		}

		catch (PDOException $e) {
		}

	}

	
	//Helper function for delete accounts
	//Deletes the user's file which stores pictures and recordings
	public function deleteAccountFiles($dirPath) {
		
    	if (! is_dir($dirPath)) {
     	   throw new InvalidArgumentException("$dirPath must be a directory");
   	 	}
    	if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
       	 	$dirPath .= '/';
    	}
    	$files = glob($dirPath . '*', GLOB_MARK);
    	foreach ($files as $file) {
        	if (is_dir($file)) {
            	self::deleteDir($file);
        	} 
        	else {
            	unlink($file);
        	}
    	}
    	rmdir($dirPath);
	}
	
	//Deletes all devices associated with an account
	public function deleteAllDevices($email) {
		
		try {
			$sql = "DELETE FROM gcm_users WHERE email = :email";

			$s = $this->db->prepare($sql);

			$s->bindParam(':email', $email);

			$s->execute();
			}
			
			catch (PDOException $e) {
				$output = 'Error deleting devices: ' . $e->getMessage();
			}
	}
	
	public function getMaxDevices($accountEmail) {

		try {

			$sql = "SELECT maxdevices FROM gcm_accounts WHERE email = '$accountEmail'";

			$resource = $this->db->query($sql);

			$resultArray = $resource->fetch(PDO::FETCH_ASSOC);

			$result = $resultArray['maxdevices'];
			return $result;
		}

		catch (PDOException $e) {
		}

	}
}
?>
