<!DOCTYPE html>

<html>

  <head>
  <title>Location</title>
  
    <style type="text/css"> 
 	  html, body {height: 100%} 
  
 	  html { overflow: hidden;
  	  	   } 
      body { margin: 0px 0px 0px 0px;
   		     padding: 0px 0px 0px 0px;
           }
	</style> 

     <?php

    // receive data from HTML readsms request

	$rName=$_POST["registration"];
    	$rowId=$_POST["rowid"];
	$dName=$_POST["devicename"];


	include_once './db_functions.php';

	$db = new DB_Functions();

	$lat = $db->getLat($rName);

	$long = $db->getLong($rName);                        

    ?>

  <style>

      #map_canvas {

        width: 100%;

        height: 89%;

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

    </style>
    
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script>
    
    if (window.attachEvent) { 
		window.attachEvent("onresize", function( ) {this.map.onResize( )} );
        window.attachEvent("onload", function( ) {this.map.onResize( )} ); 
	 } 
	 else if (window.addEventListener) { 
	 		window.addEventListener("resize", function( ) {this.map.onResize( )} , false); 
			window.addEventListener("load", function( ) {this.map.onResize( )} , false); 
			}
	 </script>

    <script src="https://maps.googleapis.com/maps/api/js"></script>

    <script>

      function initialize() {

		geocoder = new google.maps.Geocoder();

        var map_canvas = document.getElementById('map_canvas');

		var latlng = new google.maps.LatLng(<?php echo $lat ?>,<?php echo $long ?>);

        var map_options = {

          center: latlng,

          zoom: 12,

          mapTypeId: google.maps.MapTypeId.ROADMAP

        }

        var map = new google.maps.Map(map_canvas, map_options)

		geocoder.geocode({'latLng': latlng}, function(results, status) {   //doesn't work

			if (status == google.maps.GeocoderStatus.OK) {

				if (results[1]) {

					infowindow.setContent(results[1].formatted_address);

				}

			}

		});

		

		var marker = new google.maps.Marker({

			position: latlng,

			title:"<?php echo $dName ?>"

		});

		

		google.maps.event.addListener(marker, 'click', function() {

    		infowindow.open(map,marker);       //doesn't work

  		});

		

		marker.setMap(map);

      }

      google.maps.event.addDomListener(window, 'load', initialize);

    </script>
    
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

  </head>

  <body>
    <a href="javascript:history.back()">Home</a>
    <p>Instructions: Click "Update Location" and then refresh the page for the current location of the device.</p>
    <form id="<?php echo $rowId ?>" name="" method="post" onsubmit="return sendPushNotification('<?php echo $rowId ?>')">                             
    <input type="hidden" name="message" value="location" />
    <input type="hidden" name="regId" value="<?php echo $rName ?>"/>
    <input type="submit" class="send_btn" value="Update Location" onclick=""/>
    </form>
    <div id="map_canvas"></div>

  </body>

</html>
