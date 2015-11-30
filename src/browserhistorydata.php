<?php
if(!isset($_SESSION)) { session_start(); }

	function formatBrowserHist($string)
	{
	    $paragraphs = '';
	    $string .= "\n";

	    $mxr = explode("\n\n", $string);
	    for ($i = 0; $i < count($mxr); $i += 1) {
	    	//$mystr = preg_replace("/\n(.*)(?!.*\n)$/m", "\n<a class=\"fxwer\" href=\"https://play.google.com/store/apps/details?id=$1\">$1</a>", $mxr[$i]);
	    	$mystr = preg_replace("/(.*)?(: )(http|ftp)(.*)/", "<a href=\"$3$4\" class=\"list-group-item\"><h4 class=\"list-group-item-heading\">$1</h4><p class=\"list-group-item-text\">$3$4</p></a>", $mxr[$i]);
            $paragraphs .= $mystr;//'<p class="app-item">' . $mystr . '</p>';
	    }
	    return $paragraphs;
	}

    if (!isset($_SESSION['email']) || !isset($_POST['rowid']) ||
      !isset($_POST['registration']) || !isset($_POST['reqtime']) ||
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

    $email = $_SESSION['email'];
    $reg = $_POST["registration"];
    $rowId = $_POST["rowid"];
    $reqTime = intval($_POST["reqtime"]);

    include_once './db_functions.php';

    $db = new DB_Functions();

    if (! $db->userDeviceVerify($rowId, $email)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
        exit();
    }

    $updTime = 1;
    $updTime = $db->getBrowserHistoryUpdateTime( $reg );

    if ($reqTime > $updTime) {
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
        exit();
    }
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");

	$browserhist = $db->getBrowserHistory($reg);

    if ($browserhist == "") {
        echo "";
        exit();
    }

    ?>
<div class="list-group">
  <?php echo formatBrowserHist($browserhist);?>
</div>