<?php 
// updatepic.php

session_start();
$regId = $_SESSION["registration"];
$rowId = $_SESSION["rowid"];
$uid = $_GET["uid"];
header("Content-Type: text/html")
?>
<html>
<head>
	<title>View Picture <?php echo $uid; ?></title>

<style type="text/css">
.loading-msg {
	margin-bottom: -50px;
	text-align: center;
	font-size: 12pt;
}
.warning-msg {
	text-align: center;
	font-size: 9pt;
	margin-top: 50px;
	font-style: italic;
}
.spinner {
  width: 30px;
  height: 30px;
  background-color: #333;
  text-align: center;

  margin: 100px auto;
  -webkit-animation: rotateplane 1.2s infinite ease-in-out;
  animation: rotateplane 1.2s infinite ease-in-out;
}

#txtHint img {
    max-width: 100%;
    height: auto;
    width: auto\9; /*ie8 */
}

@-webkit-keyframes rotateplane {
  0% { -webkit-transform: perspective(120px) }
  50% { -webkit-transform: perspective(120px) rotateY(180deg) }
  100% { -webkit-transform: perspective(120px) rotateY(180deg)  rotateX(180deg) }
}

@keyframes rotateplane {
  0% { 
    transform: perspective(120px) rotateX(0deg) rotateY(0deg);
    -webkit-transform: perspective(120px) rotateX(0deg) rotateY(0deg) 
  } 50% { 
    transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);
    -webkit-transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg) 
  } 100% { 
    transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
    -webkit-transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
  }
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
function calcTime(offset) {
    // create Date object for current location
    var d = new Date();

    // convert to msec
    // subtract local time zone offset
    // get UTC time in msec
    var utc = d.getTime() - (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*offset));

    // return time as a string
    return nd.getTime() / 1000;
}

	var suid = "<?php echo $uid; ?>";
	var stageTwo = function() {
		setTimeout(function() {
			var denverTime = calctime(-7);
			console.log("dtime = " + denverTime);
			$.ajax({
			  url: "checkpicload.php",
			  type: "POST", 
			  data: { 'start_time': denverTime },
			  success:function(result){
			  	if (result == "true") {
			  		$("#txtHint").html('<image class="usr-img" src="./getpic.php" alt="Device Image">');
			  	}
			  	else {
			  		stageTwo();
			  	}
			  }
			});
		}, 10000);
	};
	$.ajax({
	  url: "send_message.php",
	  type: "GET", //Or even get
	  data: {
	  	message: "uploadpic(" + suid + ")",
	  	regId: "<?php echo $regId?>"
	  },
	  success:function(result){
	  	stageTwo();
	  }
	});

</script>
</head>
<body>
<div id="txtHint">
	<div class="loading-msg">Please wait as we load your picture...</div>
	<br/>
	<div class="spinner"></div>
	<div class="warning-msg">Loading your picture will take 30 seconds.</div>
</div>
</body>
</html>