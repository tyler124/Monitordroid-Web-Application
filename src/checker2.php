<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Monitordroid</title>
</head>

<body>
<?php 

if (isset($_POST['accountname'])) {
	$accountname = $_POST['accountname'];
		include_once('./db_functions.php');
		$db = new DB_Functions();
		if ($db->accountEmailExists($accountname)) {
			echo "true";
		}
		else echo "false";
}
?>
</body>
</html>