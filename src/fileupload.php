<?php

//Directory you want to store pictures/recordings
	$currentDir = getcwd();
	$uploads_dir = $currentDir . '/files/';
if (isset($_FILES["data"]["name"])) {
	//Name of the file (picture.jpg, sound.m4a, etc.)
	$uploadname = $_FILES["data"]["name"];

	//Extention of the file (ex. ".jpg")
	$fileExt = pathinfo($uploadname, PATHINFO_EXTENSION);


	//If the file extention is not ".m4a" (sound), assume it's a user uploaded file.
	//Always save the file as "uploadedfile.(filextension)" in order to keep only 1 uploaded file at a time on the server to save space
	//Check if there are filetypes of other extensions in the directory, if so, delete them
	if (strcmp($uploadname, "sound.m4a") != 0) {
		$uploadname = "uploadedfile" . "." . $fileExt;
		$filesInDir = scandir($uploads_dir);
		$numFiles = count($filesInDir);
		//Checks for any other uploaded files in the directory. If they exist, delete them to save space
		for ($i = 0; $i < $numFiles; $i++) {
		$fileParts = explode(".", $filesInDir[$i]);
		if (strcmp($fileParts[0], "uploadedfile") == 0) {
			unlink($uploads_dir . $filesInDir[$i]);
		}
		}
	}

	move_uploaded_file($_FILES['data']['tmp_name'], $uploads_dir . $uploadname);
	$response = $uploads_dir . $uploadname . " " . $username;

} else {
	$response = "File read unsuccessfully";
}

echo $response;
?>