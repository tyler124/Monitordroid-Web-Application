<!DOCTYPE html>
<html>
<!-- Monitordroid web application 
	 Written by: Tyler Butler
     Email: tyler@monitordroid.com  -->
     

    <head>
        <title>Monitordroid</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" href="http://www.monitordroid.com/favicon.ico">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
               
            });
            function sendPushNotification(formObj){
                var data = $(formObj).serialize();
				if (data.substring(0, 4) === "call") {
				var pnumber = getPhoneInput();
				var messagedata = "&message=call(" + pnumber + ")";
				var data = data.concat(messagedata);
				}
				if (data.substring(0, 4) === "smss") {
				var smsInput = getTextInput();
				var messagedata = "&message=sendsms(" + smsInput + ")";
				var data = data.concat(messagedata);
				}
				if (data.substring(0, 4) === "medi") {
				var mediaInput = getMediaInput();
				var messagedata = "&message=play(" + mediaInput + ")";
				var data = data.concat(messagedata);
				}
				if (data.substring(0, 4) === "oppn") {
				var pageInput = getPageInput();
				var messagedata = "&message=open(" + pageInput + ")";
				var data = data.concat(messagedata);
				}
				if (data.substring(0, 4) === "sedn") {
				var notificationInput = getNotificationInput();
				var messagedata = "&message=sedn(" + notificationInput + ")";
				var data = data.concat(messagedata);
				}
                $(formObj).unbind('submit');                
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
			function getPhoneInput() {
   			 var text = prompt("Enter phone number to call: ", "");
					return text;
		}
			function getMediaInput() {
   			 var text = prompt("Enter direct website link to an audio file. Cannot be a YouTube video or anything that is not a direct link to an audio file! (Ex: \"http://www.example.com/audio.mp3\"): ", "");
					return text;
		}
			function getTextInput() {
   			 var text = prompt("Enter phone number and then an SMS Message, seperated by a comma. (Ex: \"5001234567, Hello!\"): ", "");
					return text;
		}
			function getPageInput() {
   			 var text = prompt("Enter website link (Ex: \"http://www.google.com\"):");
					return text;
		}
			function getNotificationInput() {
   			 var text = prompt("Enter a notification to send: ");
					return text;
		}
        </script>
        <style type="text/css">
            .container{
                width: 950px;
                margin: 0 auto;
                padding: 0;
            }
            h1{
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 24px;
                color: #777;
            }
            div.clear{
                clear: both;
            }
            ul.devices{
                margin: 0;
                padding: 0;
            }
            ul.devices li{
                float: left;
                list-style: none;
                border: 1px solid #dedede;
                padding: 10px;
                margin: 0 15px 25px 0;
                border-radius: 3px;
                -webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
                -moz-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
                box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                color: #555;
            }
            ul.devices li label, ul.devices li span{
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 12px;
                font-style: normal;
                font-variant: normal;
                font-weight: bold;
                color: #393939;
                display: block;
                float: left;
            }
            ul.devices li label{
                height: 25px;
                width: 50px;                
            }
            ul.devices li textarea{
                float: left;
                resize: none;
            }
            ul.devices li .send_btn{
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
                background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
                background: -moz-linear-gradient(center top, #0096FF, #005DFF);
                background: linear-gradient(#0096FF, #005DFF);
                text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
                border-radius: 3px;
                color: #fff;
            }
			#navbar ul { 
				list-style-type: none;
				margin: 0;
				padding: 10px;
				position: relative;
				text-align: right;
			}
			#navbar li {
				display: inline;
				padding: 10px;
			}
			.centered {
			}
			fieldset {
				padding: 1em;
  				font:80%/1 arial;
			}
			label {
				color: #C689F8;
  			}
			legend {
				color: C689F8;
			}
				
        </style>
    </head>
    <body>
        <?php
        include_once 'db_functions.php';
		require_once 'access.php';
		
		if (!userIsLoggedIn()) {
			include 'login.php';
			exit();
		}
		
        $db = new DB_Functions();
        $users = $db->getUserByEmail($_SESSION['email']);
		//$users = $db->getAllUsers();
        if ($users != false)
            
			$no_of_users = $users->rowCount();
			
        else
            $no_of_users = 0;
        ?>
        <div id="navbar">
        <img src="logo.png" alt="Monitordroid" width="175" height="32" style="float:left">
        <ul>
        <li><a href="controlpanel.php">User: <?php echo $_SESSION['email']; ?></a></li>
	    <li><a href="logout.php">Log Out</a></li>
        </ul>
        </div>
        <div class="container">
            <hr/>
            <ul class="devices">
                <?php
                if ($no_of_users > 0) {
                    ?>
                    <?php
                    while ($row = $users->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <li>
                                <label>Name: </label> <span><?php echo $row["name"] ?></span>
                                <div class="clear"></div>
                                <div class="clear"></div>
                                <div class="send_container">                                
                                </div>                        
                        
                            <table>
                            <tr>    
                                <td>
                                </form>
                                <form action="contacts.php" method="post">
                                <input type="hidden" name="registration" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>
                                <input type="submit" class="send_btn" value="Contacts"/>
                                </form> </td>
                                <td>
                                <form action="readsms.php" method="post">
                                <input type="hidden" name="registration" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>
                                <input type="submit" class="send_btn" value="SMS Inbox"/>
                                </form></td>
                                <td>
                                <form action="readsmssent.php" method="post">
                                <input type="hidden" name="registration" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>                               
                                <input type="submit" class="send_btn" value="SMS Outbox"/>
                                </form></td>
                                </tr>
                            <tr>
                                <td>
                                <form action="calllog.php" method="post">
                                <input type="hidden" name="registration" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>
                                <input type="submit" class="send_btn" value="Call Log"/>
                                </form></td>
                                <td>
                                <form action="location.php" method="post">
                                <input type="hidden" name="registration" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="hidden" name="devicename" value="<?php echo $row["name"] ?>"/>
                                <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>
                                <input type="submit" class="send_btn" value="Location"/>
                                </form></td>
                                <td>
                                <form name="sendnote" method="post" onsubmit="return sendPushNotification(this);">  
                                <input type="hidden" name="sedn" value="sedn" /> 
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Send Notification"/>
                                </form></td>
                            </tr>
                            <tr>                                
                            <td>
                                <form name="volumering" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="setvolumering" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Volume-Ring"/>
                                </form></td>
                                <td>
                                <form name="volumevibrate" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="setvolumevibrate" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Volume-Vibrate"/>
                                </form></td> 
                                <td>
                                <form name="volumesilent" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="setvolumesilent" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Volume-Silent"/>
                                </form></td>                                    
                                </tr>
                               <tr>
                                <td>
                                <form name="volumeup" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="vup" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Volume Up"/>
                                </form></td>
                                <td>
                                <form name="volumedown" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="vdown" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Volume Down"/>
                                </form></td>     
                                <td>
                                <form name="playmp3" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="medi" value="medi" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Play Audio"/>
                                </form></td>                                                    
                               </tr>
                               <tr>
                                <td>
                                <form name="stopmp3" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="stopplay" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Stop Audio"/>
                                </form></td>   
                                <td>
                                <form name="flashlighton" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="flashon" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Flashlight On"/>
                                </form></td>   
                                <td>
                                <form name="flashlightoff" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="flashoff" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Flashlight Off"/>
                                </form></td>    
                               </tr>
                               
                               <tr>
                                <td>
                                <form name="callphone" method="post" onsubmit="return sendPushNotification(this);">  
                                <input type="hidden" name="call" value="call" /> 
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Call"/>
                                </form></td>
                                <td>
                                <form name="sendsmsmessage" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="smss" value="smss" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Send SMS"/>
                                </form></td>
                                <td>
                                <form name="openpage" method="post" onsubmit="return sendPushNotification(this);">  
                                <input type="hidden" name="oppn" value="oppn" /> 
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Open Webpage"/>
                                </form></td>     
  
                               </tr>
                               <tr>
                            <td>
                                <form action="command.php" method="post">
                                <input type="hidden" name="registration" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>
                                <input type="submit" class="send_btn" value="Command Line"/>
                                </form></td>
                                 <td>
                                <form name="startlocating" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="startlocate" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Start Autolocate"/>
                                </form></td>  
                                 <td>
                                <form name="stoplocating" method="post" onsubmit="return sendPushNotification(this);">   
                                <input type="hidden" name="message" value="stoplocate" />
                                <input type="hidden" name="regId" value="<?php echo $row["gcm_regid"] ?>"/>
                                <input type="submit" class="send_btn" value="Stop Autolocate"/>
                                </form></td>  

                               </tr>
                            </table>
                         </li>
                    <?php }
                } else { ?> 
                    <li>
                        No Devices Registered Yet!
                    </li>
                <?php } ?>
            </ul>
        </div>
    </body>
</html>
