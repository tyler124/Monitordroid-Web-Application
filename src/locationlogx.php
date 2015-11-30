<?php
  if (!isset($_SESSION)) { session_start(); }

  if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
      header("location:index.php");
      exit();
  }
  $rName = $_SESSION["registration"];
  $dName = $_SESSION["name"];
  $rowId = $_SESSION["rowid"];
?>

<style type="text/css">
  html, body {
    height: 100%;
  }
  div#content {
    height: 95%;
    min-height: 350px;
  }
  div.container, div#my-tab-content, div#tab-curr {
    height: 100%;
  }
  #txtHint {
    display: none;
  }
  .error-msg {
    display: none;
    font-size: 12pt;
    text-align: center;
    font-weight: bold;
    color: red;
  }
  #map_canvas {
    width: 100%;
    height: 100%;
    
    margin: 0;
    padding: 0;
    /*display: none;*/
  }
  #tab-curr {
    height: 100%;
    width: 100%;
  }
  #test {
    padding-top: 5px;
  }
  .show-sel:hover {
    cursor: pointer;
  }
  .warning-msg {
    display: none;
  }
  #txtHint {
    display: block;
  }
  #no-data-warning {
    display: none;
  }
  p.unit-meters::after {
    content: " meter(s)";
    color: rgba(0,0,0,.6);
  }
</style>
<script type="text/javascript" src="./lib/Geohash.js"></script>
<script type="text/javascript" id="doc-init-script">
/*jshint maxerr: 10000 */
var locationstoshow;
var initialize_runbefore;
var saved_location_string;

$(document).ready(function () {
    if (( window.gmaps_loaded===null ) || !( window.gmaps_loaded )) {
        window.gmaps_loaded = true;
        loadScript('https://maps.googleapis.com/maps/api/js?v=3&libraries=places&sensor=false&callback=delayInit');
    }
    else {
        eval("window.delayInit();");
        initialize.runbefore = true;
    }

    initialize_runbefore = false;
    document.title = "Monitordroid - Location Log";
    locationstoshow = 10;
    $('.show-sel').click(function(){
        locationstoshow = Number($(this)[0].id.substr(5));
        console.log("locs to show", locationstoshow);
        $('#show-now').text($(this).text());
        $('#show-plural').text((locationstoshow > 1) ? "s" : "");
        initialize(saved_location_string);
    });
    var stageTwo = function() {
      window.location.href = "./#location_log";
      location.reload();
    };
    var updateProc = function() {
        $("#update_btn").prop("disabled", true);
        //var resolveLocationMarkers = (Cookies.get("options_resolveLocationMarkers")==="true") ? "1" : "0";
        sendGCMRefreshMessage("location", "<?php echo $rName?>", stageTwo);
    };

    $("#update_btn").click(updateProc);
    
    $("#clear_btn").click(function() {
        $.ajax({
            url: "clear_locationlog.php",
            type: 'POST',
            data: {
              regId: "<?php echo $rName ?>"
            },
            success: function() {
                setTimeout(function(){
                    reloadOnThisTab();
                }, 200);
            }
        });
    });
    /*if (Cookies('options_autoRefreshPages') != "false") {
        var updateIntervalID = setInterval(updateProc, 1000 * 60);
        tabsUnloadData["locationlogx.php"] = function(){
            console.log("unloading locationlogx");
            clearInterval(updateIntervalID);
        };
    }*/
    var sendAjaxMessage = function(data) {
      $.ajax({
          url: "send_message.php",
          type: 'GET',
          data: {
              regId: "<?php echo $rName; ?>",
              message: data
          }
      });
    };

    $("#startlocate").click(function(){
      sendAjaxMessage("location-" + (Cookies.get("options_autolocateUpdFreq") ? Cookies.get("options_autolocateUpdFreq") : 10)); //data-reg
    });
    $("#stoplocation").click(function(){
      sendAjaxMessage("stoplocation");
    });
}); 
var save_geocode = {};
var save_placename = {};
var placename = "";
var geocoder;
var placeservice;
var x;
var markersArray = {};
var phonePath;


var initialize = function(json) {
    saved_location_string = json;

    var addresses;
    try {
        addresses = jQuery.parseJSON( json );
    } catch(e) {
        addresses = json;
    }

    if ($.isEmptyObject(addresses)) {
        $("#no-data-warning").css("display", "block");
        $("#update_btn").prop("disabled", false);
        $('#txtHint').css("display", "none");
        return;
    }

    $('#map_canvas').css("display", "block");
    $("#update_btn").prop("disabled", false);
    $('#txtHint').css("display", "none");

    console.log("initializing");
    
    var arunbefore = initialize_runbefore;
    initialize_runbefore = true;
    var xmap; //
    geocoder = new google.maps.Geocoder();
    
    var clearMarkers = function() {
        for(var key in markersArray)
        {
            if (markersArray.hasOwnProperty(key)) {
                markersArray[key].setMap(null);
                delete markersArray[key];
            }
        }
    };

    if (!arunbefore) {
        var elevator;
        /*var myOptions = {
            zoom: 12,
            center: new google.maps.LatLng(php echo $xlast; ),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };*/
        //console.log("anaximander", llngCenter);

        xmap = new google.maps.Map(document.getElementById('map_canvas'), 
            { zoom: 12,
            center: new google.maps.LatLng(addresses[addresses.length - 1].latitude, addresses[addresses.length - 1].longitude),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        initialize.map = xmap; // save the current map as static (function) variable
    } else {
        xmap = initialize.map;
        clearMarkers();
        phonePath.setMap(null);
    }
    // xmap will be set from here on



    //var addresses = results;
    x = addresses.length - 1;
    var pinfowindow = null; // previous info window

    var addExtraInfoContent = function(infoContent, address) {
        console.log(address);
        infoContent += '<p>Time: ' + address.locationtime + "</p>";
        infoContent += '<p class="unit-meters">Accuracy: ' + address.locationaccuracy + "</p>";
        return infoContent;
    };

    var latLngFromStr = function(gps_str) {
        if (ISGEOCODEDEBUG) {
            console.log(gps_str);
        }
        var faddress = gps_str.split(',');
        var lat = Number(faddress[0]);
        var lng = Number(faddress[1]);
        return new google.maps.LatLng(lat, lng);
    };

    var infowindows = {};
    var createInfoWindow = function(infoContent, marker, xx) { // assumes xmap is set
        var infowindow = new google.maps.InfoWindow({
              content: infoContent,
              maxWidth: 290 // seems to help with making the height sensible too
        });
        infowindows[xx] = infowindow;
        google.maps.event.addListener(marker, 'click', function() {
            if (pinfowindow) { // if another infowindow is open, close it
                pinfowindow.close(); // only want one open at a time
            }
            infowindow.setContent(infowindow.getContent()); // force a resize of infowindow
            infowindow.open(xmap, marker);
            pinfowindow = infowindow; // save this info window for the next listener
        });
    };

    var init_marker = function(xlatlng, xx) {
        var dispx = Math.abs(addresses.length - xx - 1);
        //console.log("displaying num " + dispx);
        var xmarker = new google.maps.Marker({
            position: xlatlng,
            map: xmap,
            title: "<?php echo $dName ?> " + xx,
            icon: (dispx >= 100) ? ('https://chart.googleapis.com/chart?chst=d_map_spin&chld=1|0|eeeeee|12|b|'+ dispx) : ('./img/number_' + dispx + '.png'), // TODO only works up to 100
            zIndex: xx // prioritize geolocated markers
        });
        markersArray[''+xx+''] = xmarker; // only seems to work if key is string
        var infoContent = "<p><em>Address Unknown</em></p>";
        infoContent += "<a href=\"http://www.google.com/maps/place/" + xlatlng.lat() + "," + xlatlng.lng() + "\">View on Google Maps</a>";
        console.log("foox");
        console.log(xx);
        console.log(addresses);
        infoContent = addExtraInfoContent(infoContent, addresses[xx]);
        createInfoWindow(infoContent, xmarker, xx);
    };

    var update_marker = function(results, xplacename, xlatlng, ixx) {
        console.log(results);
        if (results[0] != null) {
            results = results[0];
        }
        var infoContent = "<p><em>" + results.formatted_address + "</em></p>";
        if (xplacename != null && xplacename != "") { //&& xplacename != results[0].) {
            infoContent = xplacename + " at " + infoContent;
        }
        infoContent += "<p><a href=\"http://www.google.com/maps/place/" + xlatlng.lat() + "," + xlatlng.lng() + "\">View on Google Maps</a></p>";
        console.log("fooxe");
        console.log(ixx);
        console.log(addresses);
        infoContent = addExtraInfoContent(infoContent, addresses[ixx]);
        
        // this will get us the geocoded marker -- markers on the same address will
        // be the same address
        if (Cookies.get('options_resolveLocationMarkers') == "true") {
            linePath.splice(0, 0, new google.maps.LatLng(results.geolat, results.geolng));
            var p = results.geometry.location;
            try {
                console.log("Fsm ");
                console.log(google.maps.LatLng(results.geolat, results.geolng));
                xlatlng = new google.maps.LatLng(results.geolat, results.geolng);
                console.log("location resolve to new");
                console.log(xlatlng);
            }
            catch (e) {
                try {
                    console.log("err");
                    //xlatlng = new google.maps.LatLng(p.k, p.B);
                    console.log(p);
                    xlatlng = new google.maps.LatLng(p.A, p.F);
                }
                catch (e2) {
                    // yeah not working
                    console.log("could not resolve location markers error ");
                    console.log(e2);
                    infowindows[ixx].setContent(infoContent);
                    return;
                }
            }
            finally {
                console.log("cleaning up after resolved update_marker");
                //console.log(xlatlng);
                markersArray[''+ixx+''].setPosition(xlatlng);// only seems to work if key is string
                infowindows[ixx].setContent(infoContent);
                return;
            }
        }

        //var dispx = Math.abs(addresses.length - xx - 1); // 0 is last address -- inverted numbering
        //console.log("displaying num " + dispx);

        

        /*var xmarker = new google.maps.Marker({
            position: xlatlng,
            map: xmap,
            title: "<?php echo $dName ?> " + dispx,
            icon: './img/number_' + dispx + '.png', 
            zIndex: xx // prioritize geolocated markers -- MAYBE NOT??
            // if we have more than 10 we need to still display low-leveled ones
        });*/
        infowindows[ixx].setContent(infoContent);
        //createInfoWindow(infoContent, xmarker);
        //markersArray.push(xmarker);
    };
    var ISGEOCODEDEBUG = <?php if(isset($_COOKIE["geocode_debug"])) { echo "true"; } else { echo "false"; } ?>;

    var geocode_and_update_marker = function (alatlngx, aplacename, iterationx) {
        console.log("gum iteration " + iterationx);
        with({iteration: iterationx, alatlng: alatlngx, nplacename: aplacename}) {
            geocoder.geocode({'latLng': alatlng}, function(results, status) {
                if (ISGEOCODEDEBUG) {
                    console.log("iteration " + iteration +  " geocode status " + status);
                }
                if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                    /*setTimeout(function(){
                        console.log("a300 " + iteration);
                        geocode_and_update_marker(alatlng, iteration);
                    }, 2000);*/
                    (function(alatlng, iteration){
                        setTimeout(function(){
                            geocode_and_update_marker(alatlng, aplacename, iteration);
                        }, 2000);
                    })(alatlng, iteration);
                    //setTimeout(geocode_and_update_marker.bind(alatlng, iteration), 2000);
                    return;
                }
                var ghash;
                var store_obj;
                if (status == google.maps.GeocoderStatus.OK) {
                    if (localStorage) {
                        ghash = encodeGeoHash(alatlng.lat(), alatlng.lng());
                        console.log(results[0]);
                        results[0]["geolat"] = results[0].geometry.location.lat();
                        results[0]["geolng"] = results[0].geometry.location.lng();
                        store_obj = { placename: nplacename, results: results[0] };
                        localStorage.setItem('address_'+ghash, JSON.stringify(store_obj));
                    }
                    save_geocode[iteration] = results[0]; // save geocode
                    save_placename[iteration] = placename;
                    update_marker(results, nplacename, alatlng, iteration);
                }
                else {
                    save_geocode[iteration] = null;
                    save_placename[iteration] = null;
                    if (localStorage) {
                        ghash = encodeGeoHash(alatlng.lat(), alatlng.lng());
                        store_obj =  { placename: null, results: null };
                        localStorage.setItem('address_'+ghash, JSON.stringify(store_obj));
                    }
                    return;
                    //save_geocode[iteration] = null;
                    //save_placename[iteration] = null;
                    //init_marker(null, null, alatlng, iteration);
                }
            });
        }
    };

    placeservice = new google.maps.places.PlacesService(xmap);

    var linePath = [];
    for (x = addresses.length - 1; x >= 0; x--) {
        with({ iteration: x, alatlng: latLngFromStr(addresses[x]['latitude']+','+addresses[x]['longitude'])}) { // with is necessary for the async callbacks that google uses
            placename = "";

            if (iteration < (addresses.length - locationstoshow)) { // only show the last locationstoshow markers
                continue;
            }
            if (ISGEOCODEDEBUG) {
                console.log("displaying " + iteration + " runbefore");
            }
            if (Cookies.get('options_resolveLocationMarkers') !== "true") {
                linePath.splice(0, 0, alatlng);
            }
            
            
            if (arunbefore && (save_geocode[iteration] != null)) {
                //console.log("arunbefore?");

                init_marker(alatlng, iteration);
                var geo = save_geocode[iteration];
                var place = save_placename[iteration];
                if (geo && Cookies.get('options_resolveLocationMarkers') === "true") {
                    linePath.splice(0, 0, new google.maps.LatLng(geo.geolat, geo.geolng));
                }
                console.log("normal cycle results = ");
                console.log(geo);
                update_marker(geo, place, alatlng, iteration); // use cached geocodeJSON data
            }
            else {
                init_marker(alatlng, iteration);

                var ghash = encodeGeoHash(alatlng.lat(), alatlng.lng());

                if(localStorage && localStorage.getItem('address_'+ghash)) {
                    var xplace = JSON.parse(localStorage.getItem('address_'+ghash));

                    if (Cookies.get('options_resolveLocationMarkers') == "true") {
                        linePath.splice(0, 0, new google.maps.LatLng(xplace.results.geolat, xplace.results.geolng));
                    }
                    if (xplace) {
                        update_marker(xplace.results, xplace.placename, alatlng, iteration);
                    }
                } else {
                    var request = {
                      location: alatlng,
                      radius: '15', //addresses[iteration]['locationaccuracy'] / 2,
                      types: ['establishment']
                    };
                    var placeNameNearbySearch = function(results, status) {
                        console.log("annex", status);
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            if (status == google.maps.places.PlacesServiceStatus.OK) {
                                placename = results[results.length - 1].name;
                            }
                            else {
                                placename = "";
                            }
                            geocode_and_update_marker(alatlng, placename, iteration);
                        }
                        else {
                            geocode_and_update_marker(alatlng, null, iteration);
                        }
                    };

                    placeservice.nearbySearch(request, placeNameNearbySearch);
                }
            }
        }
    }
    var lineSymbol = {
        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
        scale: 3
    };
    var xicon = [{
      icon: lineSymbol,
      offset: '100%'
    }];
    phonePath = new google.maps.Polyline({
        path: linePath,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        icons: xicon,
        map: xmap
    });
};
window.delayInit = function(){
    console.log("delayinit");
    handleCallback("location", "locationlogdata.php", initialize, "<?php echo $rName ?>", "<?php echo $rowId ?>");
};

</script>
<div id="test"></div>
<div class="form-inline" id="load-buttons" style="margin-bottom: 6px">
    <div class="btn-group">
      <button class="btn btn-success" id="update_btn" disabled>Update</button>
      <button class="btn btn-default" id="clear_btn">Clear Logs</button> <!--style="margin-bottom: 6px" -->

      <div class="dropdown" style="display: inline-block; margin-left: -1px">
        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="border-bottom-left-radius: 0; border-top-left-radius: 0">
          <span id="show-now">Last 10</span> Location<span id="show-plural">s</span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
          <li><a class="show-sel" id="show-1">Last</a></li>
          <li><a class="show-sel" id="show-5">Last 5</a></li>
          <li><a class="show-sel" id="show-10">Last 10</a></li>
          <li><a class="show-sel" id="show-20">Last 20</a></li>
          <li><a class="show-sel" id="show-500">All</a></li>
        </ul>
      </div>
    </div>
    <div class="btn-group">
        <button type="button" id="startlocate" class="btn btn-default" ><span class="glyphicon glyphicon-globe"></span> Autolocate On</button>
        <button type="button" id="stoplocation" class="btn btn-default"><span class="glyphicon glyphicon-globe"></span> Off</button>
    </div>
    <em class="hidden-xs hidden-sm" style="margin-left: 5px; font-size: 12pt">More recent locations have lower numbers.</em>
</div>
<div class="alert alert-danger" role="alert" id="device-error">
  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>
  <strong>Oh No!</strong> <span id="device-error-msg"></span>
</div>
<div class="alert alert-danger" role="alert" id="no-data-warning">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Warning:</span>
  <strong>Oops</strong> looks like we couldn't find any data for this category. Please click <em>Update</em> above.
</div>
<div id="txtHint">
  <div class="loading-msg">Loading location...</div>
  <br/>
  <div class="spinner"></div>
</div>

<div id="map_canvas"></div>


