<?php session_start(); ?>
<?php 
include_once 'db_functions.php'; 
include_once 'access.php';
if (!userIsLoggedIn()) {
  header('Location: ./src/login.php');
  exit();
} ?><!DOCTYPE html>
<html>
<!-- Monitordroid Web Application
     By Monitordroid Inc.
     Last Updated July 2015 -->
    <head>
      <title>Monitordroid Control Center</title>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="apple-touch-icon" sizes="57x57" href="./img/apple-touch-icon-57x57.png">
      <link rel="apple-touch-icon" sizes="60x60" href="./img/apple-touch-icon-60x60.png">
      <link rel="apple-touch-icon" sizes="72x72" href="./img/apple-touch-icon-72x72.png">
      <link rel="apple-touch-icon" sizes="76x76" href="./img/apple-touch-icon-76x76.png">
      <link rel="apple-touch-icon" sizes="114x114" href="./img/apple-touch-icon-114x114.png">
      <link rel="apple-touch-icon" sizes="120x120" href="./img/apple-touch-icon-120x120.png">
      <link rel="apple-touch-icon" sizes="144x144" href="./img/apple-touch-icon-144x144.png">
      <link rel="apple-touch-icon" sizes="152x152" href="./img/apple-touch-icon-152x152.png">
      <link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon-180x180.png">
      <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32">
      <link rel="icon" type="image/png" href="./img/android-chrome-192x192.png" sizes="192x192">
      <link rel="icon" type="image/png" href="./img/favicon-96x96.png" sizes="96x96">
      <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16">
      <link rel="manifest" href="./manifest.json">
      <meta name="msapplication-TileColor" content="#da532c">
      <meta name="msapplication-TileImage" content="./mstile-144x144.png">
      <meta name="theme-color" content="#ffffff">

      <meta name="description" content="Monitordroid web application, the device management software for Android devices.">
      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
      <!--Bootstrap includes-->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

      <script crossorigin="anonymous" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
      <!--End bootstrap-->
      <script type="text/javascript" src="./lib/js_crookies.js"></script>
      <script type="text/javascript" src="./md_utilities.js?v=0.199"></script>
      <link rel="stylesheet" href="./main_style.css?v=2.01">
      <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/js/bootstrap-formhelpers.js"></script>-->
      <script type="text/javascript">
      var msgList = ["getbrowserhistory-1","getapps",'readsms-1', "contacts", "calls"];
      </script>
      <script type="text/javascript" src="./md_core_index.js?v=0.02"></script>
    </head>
    <body>
      <div id="dummy-tutorial-element"></div>
	  
	 
    <?php
	//Create database connection
    $db = new DB_Functions();
	//Get the current user's session
    $users = $db->getUserByEmail($_SESSION['email']);
	//Gets the max number of devices the user is allowed to have registered to his/her account
  	$maxNumberOfDevices = $db->getMaxDevices($_SESSION['email']);
    if ($users != false)
			$no_of_users = $users->rowCount();
    else
      $no_of_users = 0;
    ?>
      <div>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
          <div class="container-fluid centered-div">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="/app/"><img alt="Brand" width="37" height="40" src="./logos/MD_2_brand.png"></a>
            </div>
            <div class="nav navbar-nav truss" style="position:fixed; display: inline-block; float:none; margin-left:-140px" class="hidden-xs hidden-md hidden-lg">
              <h1 style="display: inline" class="hidden-xs hidden-lg"><small>Control Center</small></h1>
            </div>
            <div class="nav navbar-nav truss" style="position:fixed; display: inline-block; float:none; margin-left:-10%" class="hidden-xs hidden-sm">
              <h1 style="display: inline" class="hidden-sm hidden-md hidden-xs"><small>Monitordroid</small></h1>
              <h1 style="display: inline" class="hidden-sm hidden-md hidden-xs"><small>Control Center</small></h1>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse-1" style="text-align: left">
              <div class="navbar-left nav navbar-nav" style="margin-top:7px">
                <div class="btn-group">
                  <div class="dropdown" style="display: inline-block; float: left">
                    <button class="btn btn-info dropdown-toggle" type="button" id="device-choice-dropdown" data-toggle="dropdown" aria-expanded="false" style="border-top-right-radius: 0; border-bottom-right-radius: 0">
                      <span id="device-choice-dropdown-txt"><?php
                      if (isset($_SESSION['name'])){
                          echo $_SESSION['name'];
                      }
					  //Dropdown menu for choosing device to interact with
                      else {
                          echo "Choose Device";
                          } ?></span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="device-choice-dropdown">
                    <?php
                      $rowid;
                      $registration;
                      $name;
					  //Get list of devices associated with user's account from the database
                      while ($row = $users->fetch(PDO::FETCH_ASSOC)) {
                        $rowid = $row["id"];
                        $registration = $row["gcm_regid"];
                        $name = $row["name"];
                      ?>
                      <script type="text/javascript">
                      $(document).ready(function(){
                        $("#SD_<?php echo $rowid; ?>").click(function() {
                          $.ajax({
                            url: "/src/switchdevice.php",
                            type: 'POST',
                            data: {
                              rowid: "<?php echo $rowid ?>",
                              registration: "<?php echo $registration ?>",
                              name: "<?php echo $name ?>"
                            }
                          })
                          .done(function(){
                              reloadOnThisTab();
                          });
                        });
                      });
                      </script>
                      <li role="presentation"><a role="menuitem" style="cursor:pointer" tabindex="-1" id="SD_<?php echo $rowid; ?>"><?php echo $name ?></a></li>
                      <?php }
                      if (!isset($_SESSION['registration'])) {
                          $_SESSION["registration"] = $registration;
                          $_SESSION["name"] = $name;
                          $_SESSION["rowid"] = $rowid;
                          $deviceDataVersion = $db->getDataVersion($registration, $rowid);
                           ?>
                          <script type="text/javascript">
                          $(document).ready(function(){
                            $("#device-choice-dropdown-txt").text("<?php echo $name ?>");
                            if (Cookies.get('options_autoRefreshPages') != "false") {
                              refreshAll("<?php echo $registration ?>");
                            }
                          });
                          </script>
                      <?php }
                      //
                      // Managing the clearing of old data fields for upgraded devices
                      if(!isset($_SESSION["version_deviceinfo" . $_SESSION["name"]])) { // only update device info if we have not set it in session
                        ?>
                        <script>
                        $.ajax({
                          url: "/src/send_message.php",
                          type: "GET",
                          data: {
                            message: "getdeviceinfo",
                            regId: '<?php echo $_SESSION["registration"]; ?>'
                          }
                        });
                        setTimeout(function() {
                          $.ajax({
                            url: "/src/getmdversion.php",
                            method: "POST"
                          }).done(function(result){
                            if (result == "1.02R Updated") {
                              $('#first-visit-modal').modal('show');
                              $("#start-tutorial-btn").one("click", function() {
                                $.get("/src/tutorialx.php", function(data) {
                                    $("#dummy-tutorial-element").html(data);
                                });
                              });
                            }
                            else if (result == "1.1") {
                              $("#older-version-modal").modal('show');
                            }
                          });
                        }, 500);
                        </script>
                        <?php $_SESSION["version_deviceinfo" . $_SESSION["name"]] = true;
                      } ?>
                      <script type="text/javascript">initializeGlobals(<?php echo $rowid; ?>);</script>
                    </ul>
                  </div>
                  <a style="float: right" href="./src/devicemanage.php" class="btn btn-default">Manage Devices</a>
                </div>
            </div>
            <div class="nav navbar-nav navbar-right">
              <ul class="nav navbar-nav navbar-list">
                <li><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#optionsModal" style="margin-top:7px; margin-right: 5px">
                  Settings
                </button></li>
                <li><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#chgPassModal" style="margin-top:7px">
                  Change Password
                </button></li>
                <li><a href="logout.php" >Log Out</a></li>
              </ul>
            </div>
          </div>
          <ul class="nav nav-tabs" id="prodTabs" role="tablist">
              <li role="presentation" class="active"><a href="#" data-target="#tab-curr" id="home_tab" data-toggle="tab" data-link="/src/controlx.php" role="tab" title="Home">Home</a></li>
              <li role="presentation"><a href="#contacts" data-target="#tab-curr" id="contacts_tab" data-toggle="tab" data-link="/src/readcontactsx.php" role="tab" title="Contacts">Contacts</a></li>
              <li role="presentation"><a href="#sms" data-target="#tab-curr" id="sms_tab" data-toggle="tab" data-link="/src/readsmsx.php" role="tab" title="SMS"><span class="visible-md-inline visible-lg-inline">Text </span>Messages</a></li>
              <li role="presentation"><a href="#call_log" data-target="#tab-curr" id="call_log_tab" data-toggle="tab" data-link="/src/calllogx.php" role="tab" title="Call Log">Call Log</a></li>
              <li role="presentation"><a href="#browser_history" data-target="#tab-curr" id="browser_history_tab" data-toggle="tab" data-link="/src/browserhistoryx.php" role="tab" title="Browser History">Browser History</a></li>
              <li role="presentation"><a href="#location_log" data-target="#tab-curr" id="location_log_tab" data-toggle="tab" data-link="/src/locationlogx.php" role="tab" title="Location Log">Location Log</a></li>
              <li role="presentation"><a href="#record" data-target="#tab-curr" id="record_tab" data-toggle="tab" data-link="/src/recordx.php" role="tab" title="Recording">Recording <span class="badge record-timer"></span></a></li>
              <li role="presentation"><a href="#view_files" data-target="#tab-curr" id="view_files_tab" data-toggle="tab" data-link="/src/viewfilesx.php" role="tab" title="View Files">View Files/Photos</a></li>
              <li role="presentation"><a href="#app_list" data-target="#tab-curr" id="app_list_tab" data-toggle="tab" data-link="/src/applistx.php" role="tab" title="App List">App List</a></li>
              <li role="presentation" id="lastTab">
                <a class="btn dropdown-toggle pull-right" data-toggle="dropdown" href="#" style="margin-top: 0; font-weight: 600" id="more-tab-link">
                  More <span class="caret"></span>
                </a>
                <ul class="dropdown-menu pull-right" id="collapsed">
                </ul>
              </li>
          </ul>
          </div>
        </nav>
        </div>

        <div id="content">
        <div class="container">
                <?php
	
                if ($no_of_users > 0) {
					//If the user exceeds their max allotted devices, display deletion prompt. 
                    if ($maxNumberOfDevices < $no_of_users) {
                    	?>
              <div class="limit-alert alert alert-danger fade in" role="alert">
                  <p class="lead">
                      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                      <strong>Error</strong> Looks like you have too many devices for your current account. You can delete a device from <a href="./src/devicemanage.php">Device Management</a>, or email <a href="mailto:sales@monitordroid.com">sales@monitordroid.com</a> for enterprise pricing options.
                  </p>
              </div>
              <?php }
          					else { // i.e. maxNumberOfDevices >= no_of_users
                      ?>
                    <div id="my-tab-content" class="tab-content">
                      <div id="indexTxtHint">
                        <div class="spinner"></div>
                      </div>
                      <div class="centered-div" id="err-no-pageload">
                      <div class="ajax-alert alert alert-danger fade in" role="alert">
                          <p class="lead">
                              <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                              <strong>Error</strong> Looks like <span id="js-replace">javascript is disabled/not supported by your browser.</span>
                              <br>
                              Try <span id="js-replace-2">switching browsers (we suggest <a class="alert-link" href="https://www.google.com/chrome/browser/">Google Chrome</a>), </span><a class="alert-link" href="./?rnd=<?php echo rand(0, 1000); ?>">refreshing</a>, or emailing us at <a class="alert-link" href="mailto:help@monitordroid.com">help@monitordroid.com</a>
                          </p>
                      </div>
                      </div>
                      <div id="tab-curr" class="tab-pane active">
                          
                      </div>
                    </div>
                    
                    <div class="modal fade" id="first-visit-modal" tabindex="-1" role="dialog" aria-labelledby="first-visit-modal-label" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="first-visit-modal-label">Welcome to Monitordroid 1.0R!</h4>
                          </div>
                          <div class="modal-body">
                            <p class="lead">Welcome to Monitordroid 1.0R, with a better UI, faster, smarter loading and more features than ever!</p>
                            <div class="alert alert-warning" role="alert">
                              <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                              This webapp is for use with the latest version of the Monitordroid Android app, avaliable on the <a href="https://play.google.com/store/apps/details?id=com.monitordroid.app">Google Play Store</a>.
                            </div>
                            <p>Press Start Tutorial below if you'd like a quick walkthrough of Monitordroid's features. You can restart the tutorial at any time in the Options pane.</p>
                            <p>If you've just registered with us, remember to change your password -- the one we gave you has a record in your email, and is probably hard to remember.</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="start-tutorial-btn" class="btn btn-primary" data-dismiss="modal">Start Tutorial</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal fade" id="older-version-modal" tabindex="-1" role="dialog" aria-labelledby="older-version-modal-label" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="older-version-modal-label">Have you updated your Monitordroid app? The device that is selected is using an older version of Monitordroid...</h4>
                          </div>
                          <div class="modal-body">
                            <div class="alert alert-danger" role="alert">
                              <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                              This webapp is only for use with the latest version of the Monitordroid Android app, just released on the <a href="https://play.google.com/store/apps/details?id=com.monitordroid.app">Google Play Store</a>. The device you have selected doesn't seem to have it yet.
                            </div>
                            <div class="alert alert-info" role="alert">
                              <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                              If you have already updated but with a different device, you can just click <a href="" data-dismiss="modal">Close</a> and then select the new device in the upper-lefthand corner.
                            </div>
                            <p>We suggest you update the app, as the old app is no longer supported. You may view data for older versions of Monitordroid at <a href="/app_1_1/">https://www.monitordroid.com/app_1_1/</a>.</p>
                            <p>If you have updated and believe you are seeing this by mistake, please <a href="mailto:help@monitordroid.com">contact us</a> or send click on Feedback in the lower-right corner of the screen.</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <a href="/app_1_1/" class="btn btn-danger">Old Version</a>
                            <a href="https://play.google.com/store/apps/details?id=com.monitordroid.app" class="btn btn-primary">Google Play Store</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php
                }
			    }
				  else { // i.e. no_of_users <= 0
          ?>
                      <div class="alert alert-danger" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        Looks like you haven't registered any devices yet! Don't know how? Download the <a href="https://play.google.com/store/apps/details?id=com.monitordroid.app" alt="Google Play App" class="alert-link">Android App</a> and check out our <a href="http://www.monitordroid.com/tutorial/" alt="tutorial" class="alert-link">tutorial</a>.
                      </div>
                <?php } ?>
              </div>
            </div>

        <!-- Modal Settings -->
        <div class="modal fade" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true" style="overflow-y: hidden">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="optionsModalLabel">Settings</h4>
              </div>
              <div class="modal-body">
                <form role="form" style="display:block" action="javascript:void(0);" id="options-form" method="post">
                  <div class="iform-home" style="padding-bottom: 1px; margin: 0; overflow-y: auto">
                    <div class="form-group">
                      <h4 style="margin-bottom: 5px">Autolocate</h4>
                      <div class="btn-group">
                          <button type="button" id="settings_startlocate" data-reg="<?php echo $_SESSION["registration"] ?>" class="btn btn-default"><span class="glyphicon glyphicon-globe"></span> On</button>
                          <button type="button" id="settings_stoplocation" data-reg="<?php echo $_SESSION["registration"] ?>" class="btn btn-default"><span class="glyphicon glyphicon-globe"></span> Off</button>
                      </div>
                    </div>
                    <p class="help-block">Periodically updates the current device's location at the frequency of your choosing. Changes take effect immediately. Specific to the device that is currently selected. Note: This will use a bit more battery and data.</p>
                    <div class="form-group">
                      <h4 style="margin-bottom: 5px">Autolocate Update Frequency</h4>
                      <div class="input-group">
                        <span class="input-group-addon">Every</span>
                        <input type="number" id="autolocate-update-frequency" class="form-control"/>
                        <span class="input-group-addon">minute(s)</span>
                      </div>
                    </div>
                    <p class="help-block">Duration between location updates when autolocate is turned on. If autolocate is already on, turn it off and then on again for the new frequency to take effect.</p><p class="default">Default: 10 minutes</p>
                    <!--<div class="form-group">
                      <h4 style="margin-bottom: 5px">Autoupdate All Content</h4>
                      <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-primary">
                          <input type="radio" name="options" id="auto-refresh-pages-option1"> On
                        </label>
                        <label class="btn btn-primary">
                          <input type="radio" name="options" id="auto-refresh-pages-option2"> Off 
                        </label>
                      </div>
                    </div>
                    <p class="help-block">Automatically update content every 10 minutes on each tab.</p>-->
                    <div class="form-group">
                      <h4 style="margin-bottom: 5px">Autoupdate Frequency</h4>
                      <div class="input-group">
                        <span class="input-group-addon">Every</span>
                        <input type="number" id="autorefresh-update-frequency" class="form-control"/>
                        <span class="input-group-addon">minute(s)</span>
                      </div>
                    </div>
                    <p class="help-block">Duration between automatic updates of the content of any open tab (except for location). All tabs will autoupdate when the Control Center is first loaded. Set to 0 to disable autoupdate.</p><p class="default">Default: 0 minutes (off)</p>
                    <div class="form-group" style="margin-bottom: 12px; margin-top: -5px">
                      <button id="start-tutorial-again" class="btn btn-primary">Go To Tutorial</button>
                    </div>
                    <div class="list-group">
                      <a class="list-group-item active" role="button" data-toggle="collapse" href="#options-advanced-settings" aria-expanded="false" aria-controls="options-advanced-settings">
                        <h4 class="list-group-item-heading">Advanced Settings<small style="float:right">Click to Expand</small></h4>
                      </a>
                      <div id="options-advanced-settings" class="collapse">
                        <div class="list-group-item">
                          <div class="form-group">
                            <h4 style="margin-bottom: 5px">Find and Add Contact Names for Text Messages &amp; Call Logs</h4>
                            <div class="btn-group" data-toggle="buttons">
                              <label class="btn btn-primary">
                                <input type="radio" name="options" id="experimental-resolve-contacts-option1"> On
                              </label>
                              <label class="btn btn-primary">
                                <input type="radio" name="options" id="experimental-resolve-contacts-option2"> Off 
                              </label>
                            </div>
                          </div>
                          <p class="help-block">Attempts to display a contact name next to text messages and call logs. Turning this off will make these pages load more quickly.</p><p class="default">Default: On</p>
                        </div>
                        <div class="list-group-item">
                          <div class="form-group">
                            <h4 style="margin-bottom: 5px">Always autoload more content when scroll to bottom</h4>
                            <div class="btn-group" data-toggle="buttons">
                              <label class="btn btn-primary">
                                <input type="radio" name="options" id="always-auto-load-option1"> On
                              </label>
                              <label class="btn btn-primary">
                                <input type="radio" name="options" id="always-auto-load-option2"> Off 
                              </label>
                            </div>
                          </div>
                          <p class="help-block">When you scroll to the bottom, load more messages without pressing a button.</p><p class="default">Default: Off</p>
                        </div>
                        <div class="list-group-item">
                          <div class="form-group">
                            <h4 style="margin-bottom: 5px">Place Map Markers On...</h4>
                            <div class="btn-group" data-toggle="buttons">
                              <label class="btn btn-primary">
                                <input type="radio" name="options" id="map-markers-option1"> Street Address
                              </label>
                              <label class="btn btn-primary">
                                <input type="radio" name="options" id="map-markers-option2"> Precise Location 
                              </label>
                            </div>
                          </div>
                          <p class="help-block">Placing map markers on the appoximate street address rather than precise location will replace older markers
                            location with more recent ones, making the map look cleaner at the expense of precision.</p><p class="default">Default: Precise Location</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" id="commit-options" value="Change Settings" data-dismiss="modal">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal Change Password -->
        <div class="modal fade" id="chgPassModal" tabindex="-1" role="dialog" aria-labelledby="chgPassModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="chgPassModalLabel">Change Password</h4>
              </div>
              <div class="modal-body">
                <form role="form" style="display:block" action="/src/passwordchange.php" method="post">
                  <div class="modal-body">
                        <div class="form-group" style="margin-bottom: 10px">
                            <label for="currentpwd" class="control-label">Current Password</label>
                            <input type="password" name="currentpwd" id="currentpwd" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px">
                            <label for="newpass" class="control-label">New Password</label>
                            <input type="password" name="newpass" id="newpass" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px">
                            <label for="newpasscnfm" class="control-label">Repeat New Password</label>
                            <input type="password" name="newpasscnfm" id="newpasscnfm" class="form-control">
                        </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" id="change-pass" value="Change Password">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- /Modal -->
      </body>
</html>
