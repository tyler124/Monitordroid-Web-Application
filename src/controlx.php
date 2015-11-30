<?php session_start() ?>
<link rel="stylesheet" href="./lib/intl-tel-input/css/intlTelInput.css">
<script type="text/javascript" src="./lib/intl-tel-input/js/intlTelInput.min.js"></script>
<script type="text/javascript" src="./lib/bootstrap-3-typeahead/bootstrap3-typeahead.min.js"></script>

<style type="text/css">
.iti-flag {background-image: url("./lib/intl-tel-input/img/flags.png");}
.address-book-entry:hover {cursor: pointer;}
.intl-tel-input input[type="text"].phone-num.form-control{
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 0;
  border-top-right-radius: 0;
}
.address-book-btn {
  border-bottom-left-radius: 0;
  border-top-left-radius: 0;
  margin-top: -4px;
}
.popover {
  width: 272px;
}
/*.btn-row {
  margin-top: 5px;
  margin-bottom: 5px;
}*/

.tab-content .panel .btn-group>.btn:first-child {
  margin-left: 3px;
}
.tab-content .panel .btn {
  margin-top: 5px;
  margin-bottom: 5px;
  margin-left: 3px;
  margin-right: 3px;
}
.tab-content .panel .btn-group .btn {
  margin-left: 0;
  margin-right: 0;
}
.tab-content .panel .btn-group:not(.btn-group-vertical) > .btn:last-child {
  margin-left: 0;
  margin-right: 3px;
}
.tab-content .panel .btn-group:not(.btn-group-vertical) > .btn:first-child {
  margin-right: 0;
  margin-left: 3px;
}
.tab-content .panel .btn-group-vertical>.btn:first-child:not(:last-child) {
  margin-bottom: 0;
  margin-left: 3px;
  margin-right: 3px;
}
.tab-content .panel .btn-group-vertical>.btn:last-child:not(:first-child) {
  margin-top: 0;
  margin-left: 3px;
  margin-right: 3px;
}
.tab-content .panel .btn-group-vertical>.btn+.btn:last-child:not(:first-child) {
  margin-top: -1px;
}
.tab-content .panel .btn-group .btn+.btn:not(:first-child) {
  margin-left: -1px;
}
.tab-content .panel .btn-group-vertical {
  margin-right: 3px;
}
@media (max-width: 768px) {
  .btn-group-vertical>.btn:first-child:not(:last-child) {
    margin-bottom: 0 !important;
  }
}

#inner-content > div.row {
  min-height: 160px;
}
.row .panel-container {
  /*width: 48%;*/
  display: inline-block;
}
.panel.panel-default {
  width: initial;
}
@media (max-width: 752px) {
  body {
    padding-top: 132px;
  }
}
</style>
<script type="text/javascript">
var contactsModeReady = function() {
    initializeContactsMode($("#callModal"), $("#phone_form"));
    initializeContactsMode($("#sendSmsModal"), $("#sms_form"));
};
var disableCollapse;
var enableCollapse;
$(document).ready(function() {
    loadScript("phone_contactsmode.js?_=e", contactsModeReady);
    tabsUnloadData["controlx.php"] = function(){
      console.log("unloading controlx");
      $("#device-info-btn").popover("hide");
    };
    disableCollapse = function(ele) {
      var disableEle = $(ele).children(".panel-body");
      disableEle.addClass("in");
      $(ele).removeClass("collapse");
      //$(ele).children('.pin').addClass("disabled-collapse");
    };
    enableCollapse = function(ele) {
      var enableEle = $(ele).children(".panel-body");
      enableEle.removeClass("in");
      $(ele).addClass("collapse");
      //$(ele).children('.pin').removeClass("disabled-collapse");
    };

    $('.collapse-panel').click(function() {
      var pinEle = $(this).parent().parent()[0];
      var currPin = Cookies.get("pin_" + pinEle.id);

      if (currPin == "true") {
          Cookies.set("pin_" + pinEle.id, "false", {expires: 360});
          //enableCollapse(pinEle);
      }
      else {
          Cookies.set("pin_" + pinEle.id, "true", {expires: 360});
          //disableCollapse(pinEle);
      }
    });

    if (!(Cookies.get("pin_ringer-panel"))) {
      Cookies.set("pin_ringer-panel", "true", {expires: 360});
    }
    if (!(Cookies.get("pin_actions-panel"))) {
      Cookies.set("pin_actions-panel", "true", {expires: 360});
    }
    if (!(Cookies.get("pin_tools-panel"))) {
      Cookies.set("pin_tools-panel", "true", {expires: 360});
    }
    if (!(Cookies.get("pin_audio-panel"))) {
      Cookies.set("pin_audio-panel", "true", {expires: 360});
    }

    var spin = $('.collapse-panel').toArray();
    for (var i = 0; i < spin.length; i++) {
      var pinEle = $(spin[i]).parent().parent()[0];
      
      var currPin = Cookies.get("pin_" + pinEle.id);
      if (currPin == "true") {
        disableCollapse(pinEle);
      }
      else {
        enableCollapse(pinEle);
      }
    }

    document.title = "Monitordroid - Home";
    $("#confirmDelete-go").click(function(){
        document.location.href = "./deletedevice.php";
    });
    <?php 
    if(!isset($_SESSION["deviceinfo" . $_SESSION["name"]]) && !isset($_SESSION["version_deviceinfo" . $_SESSION["name"]])) { // only update device info if we have not set it in session
        ?>
        $.ajax({
          url: "send_message.php",
          type: "GET",
          data: {
            message: "getdeviceinfo",
            regId: "<?php echo $_SESSION["registration"]; ?>"
          }
        });
        setTimeout(function() {
        <?php } 
        # have to get data regardless
        ?>
    $.ajax({
        url: "deviceinfodata.php",
        type: "POST",
        data: { 
            registration: '<?php echo $_SESSION["registration"]; ?>', 
            rowid: '<?php echo $_SESSION["rowid"]; ?>'
        },
        success: function(data){
            var lines = data.split('\n');
            // remove one line, starting at the first position
            var mdversion = $.trim(lines[0]);
            lines.splice(0,1);
            // join the array back into a single string
            var result = lines.join('\n');
            /*if (mdversion != "2.0") {
                Cookies.remove('md_visited');
                $('#first-visit-modal').modal('hide');
                $("#older-version-modal").modal('show');
            }*/
            $("#device-info-btn").attr("data-toggle", "popover");
            $("#device-info-btn").popover({
                container: 'body',
                html : true, 
                content: result,
                title: "Device Info",
                placement: "bottom"
            });
            $('body').on('click', function (e) {
                if ($(e.target).data('toggle') !== 'popover' && $(e.target).parents('[data-toggle="popover"]').length === 0 && $(e.target).parents('.popover.in').length === 0) { 
                    $('[data-toggle="popover"]').popover('hide');
                }
            });
        }
    });
    <?php 
    if(!isset($_SESSION["deviceinfo" . $_SESSION["name"]]) && !isset($_SESSION["version_deviceinfo" . $_SESSION["name"]])) { ?>
        }, 500);
        <?php $_SESSION["deviceinfo" . $_SESSION["name"]] = true; 
    }?>

    var sendAjaxMessage = function(data) {
        $.ajax({
            url: "send_message.php",
            type: 'GET',
            data: {
                regId: "<?php echo $_SESSION["registration"] ?>",
                message: data
            }
        });
    };
    $(".gcm-sendmsg-button").click(function() {
        sendAjaxMessage(this.id);
    });
    $("#startlocate").click(function() {
        sendAjaxMessage("location-" + (Cookies.get("options_autolocateUpdFreq") ? Cookies.get("options_autolocateUpdFreq") : 10));
    });
});
</script>
<div class="modal fade bs-example-modal-sm" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" >
    <div class="modal-content">
        <div class="modal-body">
        <p>Are you sure you want to disconnect your device from Monitordroid? If you do, you will need to reactivate Monitordroid on your Android device.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" id="confirmDelete-go" class="btn btn-primary">Yes</button>
        </div>
    </div>
  </div>
</div>
<div id="inner-content" style="margin-top: -35px">
  <div id="device-info-hook" style="display:inline"></div>
  <button type="button" class="btn btn-primary" id="device-info-btn">
    <span style="line-height: 21px">Name: <?php echo $_SESSION["name"] ?></span>
    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
  </button>
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmDeleteModal">
    Delete Device
  </button>
  <div class="clear"></div>
  <div class="clear"></div>
  <div class="send_container">
  </div>
<div class="row" style="margin-top: 10px; vertical-align: top;">
  <div class="panel-container col-sm-6">
    <div class="panel panel-default" style="display: inline-block; vertical-align: top;" id="ringer-panel">
      <div class="panel-heading">
        <a data-toggle="collapse" class="collapse-panel" data-parent="#ringer-panel" href="#collapseRingerBody" aria-expanded="true" aria-controls="collapseRingerBody">
            <h3 id="ringer-title" class="panel-title">Ringer</h3>
        </a>
      </div>
      <div class="panel-body panel-collapse collapse" role="tabpanel" aria-labelledby="ringer-panel-title" id="collapseRingerBody" >
            <div class="btn-group">
                <button type="button" id="setvolumering" class="gcm-sendmsg-button btn btn-default">Ring</button>
                <button type="button" id="setvolumevibrate" class="gcm-sendmsg-button btn btn-default">Vibrate</button>
                <button type="button" id="setvolumesilent" class="gcm-sendmsg-button btn btn-default">Silent</button>
            </div>
       </div>
    </div>
  </div>
  <div class="panel-container col-sm-6">
    <div class="panel panel-default" style="display: inline-block; vertical-align: top;" id="audio-panel">
      <div class="panel-heading">
        <a data-toggle="collapse" class="collapse-panel" data-parent="#audio-panel" href="#collapseAudioBody" aria-expanded="false" aria-controls="collapseAudioBody">
            <h3 id="audio-panel-title" class="panel-title">Audio</h3>
        </a>
      </div>
      <div class="panel-body panel-collapse collapse" role="tabpanel" aria-labelledby="audio-panel-title" id="collapseAudioBody">
        <div class="btn-group-vertical">
            <button type="button" id="mvup" title="Media Volume Up" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-headphones"></span> <span class="glyphicon glyphicon-volume-up"></span></button>
            <button type="button" id="mvdown" title="Media Volume Down" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-headphones"></span> <span class="glyphicon glyphicon-volume-down"></span></button>
        </div>
        <div class="btn-group-vertical">
                <button type="button" id="vup" title="Ringer Volume Up" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-earphone"></span> <span class="glyphicon glyphicon-volume-up"></span></button>
                <button type="button" id="vdown" title="Ringer Volume Down" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-earphone"></span> <span class="glyphicon glyphicon-volume-down"></span></button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#audioModal">
              Play Audio
            </button>
            <button type="button" id="stopplay" class="gcm-sendmsg-button btn btn-default">Stop Audio</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="panel-container col-sm-6">
    <div class="panel panel-default" style="display: inline-block; vertical-align: top;" id="tools-panel">
      <div class="panel-heading">
        <a data-toggle="collapse" class="collapse-panel" data-parent="#tools-panel" href="#collapseToolsBody" aria-expanded="false" aria-controls="collapseToolsBody">
            <h3 class="panel-title" id="tools-panel-title">Tools</h3>
        </a>
      </div>
      <div class="panel-body panel-collapse collapse" role="tabpanel" aria-labelledby="tools-panel-title" id="collapseToolsBody">
        <div class="row">
          <div class="btn-row">
            <div class="btn-group">
              <button type="button" id="cameraon" class="gcm-sendmsg-button btn btn-default" ><span class="glyphicon glyphicon-camera"></span> Camera Enable</button>
              <button type="button" id="cameraoff" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-camera"></span> Disable </button>
            </div>
            <div class="btn-group">
              <button type="button" id="flashon" class="gcm-sendmsg-button btn btn-default" ><span class="glyphicon glyphicon-flash"></span> Flashlight On</button>
              <button type="button" id="flashoff" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-flash"></span> Off </button>
            </div>
          </div>
          <div class="btn-row">
            <div class="btn-group">
                <button type="button" id="startlocate" class="btn btn-default"><span class="glyphicon glyphicon-globe"></span> Autolocate On</button>
                <button type="button" id="stoplocation" class="gcm-sendmsg-button btn btn-default"><span class="glyphicon glyphicon-globe"></span> Off</button>
            </div>
            <form action="command.php" method="post">
                <input type="hidden" name="registration" value="<?php echo $_SESSION["registration"] ?>"/>
                <input type="hidden" name="rowid" value="<?php echo $_SESSION["rowid"] ?>"/>
                <button type="submit" class="btn btn-primary">Command Line</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-container col-sm-6">
    <div class="panel panel-default" style="display: inline-block; vertical-align: top;" id="actions-panel">
      <div class="panel-heading">
        <a data-toggle="collapse" class="collapse-panel" data-parent="#actions-panel" href="#collapseActionsBody" aria-expanded="true" aria-controls="collapseActionsBody">
            <h3 id="actions-panel-title" class="panel-title">Actions</h3>
        </a>
      </div>
      <div class="panel-body panel-collapse collapse" role="tabpanel" aria-labelledby="actions-panel-title" id="collapseActionsBody">
          <div class="btn-row">
            <button type="button" id="lock" class="gcm-sendmsg-button btn btn-primary"><span class="glyphicon glyphicon-lock"></span> Lock Device</button>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#resetModal">
              Change/Add Password
            </button>
            <!-- Modal -->
            <div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="resetModalLabel">Change/Add Password</h4>
                  </div>
                  <form role="form" id="reset_form" action="javascript:void(0);" style="display:block" >
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reset-text" class="control-label">New Password</label>
                            <input type="text" id="reset-text" class="form-control" placeholder="password">
                        </div>
                        <div class="alert alert-warning fade in" style="display: none; margin-bottom: 5px; margin-top: 8px" id="password-invalid-warning" role="alert">
                            <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                            <strong>Warning</strong> Do not use ", \ or - in a new password.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="reset-go" class="btn btn-primary">Change/Add Password</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <script type="text/javascript">
            $("#reset_form").submit( function(event){
                event.preventDefault();
                if (($("#reset-text").val().indexOf('\\') !== -1) ||
                    ($("#reset-text").val().indexOf('"') !== -1) ||
                    ($("#reset-text").val().indexOf('-') !== -1)) {
                    $("#password-invalid-warning").css("display", "block");

                    //alert('Please do not use ", \ or - in the new password.');
                    event.preventDefault();
                    //$("#resetModal").modal('hide');
                    $("#reset-text").val("");
                }
                else {
                    $("#password-invalid-warning").css("display", "none");
                    var messagedata = "resetpassword-" + $("#reset-text").val();
                    $.ajax({
                        url: "send_message_post.php",
                        type: 'POST',
                        data: {
                            regId: '<?php echo $_SESSION["registration"] ?>',
                            message: messagedata
                        },
                        success: function(){ $("#reset-text").val(""); }
                    });
                    $("#resetModal").modal('hide');
                }
            });
            </script>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sednModal">
              Send Notification
            </button>
          </div>
          <!-- Modal -->
          <div class="modal fade" id="sednModal" tabindex="-1" role="dialog" aria-labelledby="sednModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="sednModalLabel">Send Notification</h4>
                </div>
                <form role="form" id="sedn_form" action="javascript:void(0);" style="display:block" >
                    <div class="modal-body">
                          <div class="form-group">
                              <label for="sedn-text" class="control-label">Message</label>
                              <textarea class="form-control input-xlarge" placeholder="Enter notification here..." rows="3" id="sedn-text"></textarea>
                          </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" id="sedn-go" class="btn btn-primary">Send Notification</button>
                    </div>
                </form>
              </div>
            </div>
          </div>
          <script type="text/javascript">
          $("#sedn_form").submit( function(event){
              event.preventDefault();
              var messagedata = "sedn(" + $("#sedn-text").val() + ")";
              $.ajax({
                  url: "send_message.php",
                  type: 'GET',
                  data: {
                      regId: '<?php echo $_SESSION["registration"] ?>',
                      message: messagedata
                  },
                  success: function(){ $("#sedn-text").val(""); }
              });
              $("#sednModal").modal('hide');
          });
          </script>
          <!-- Button trigger modal -->
          <!-- Modal -->
          <div class="modal fade" id="audioModal" tabindex="-1" role="dialog" aria-labelledby="audioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="audioModalLabel">Play Audio</h4>
                </div>
                <form role="form" id="audio_form" action="javascript:void(0);">
                    <div class="modal-body">
                          <label for="audio-url" class="control-label">Audio URL</label>
                          <div class="input-group" style="padding-right: 0; padding-left: 0; margin-left">
                              <span class="input-group-addon" style="display: table-cell; float: none">http://</span>
                              <input type="text" id="audio-url" class="form-control" placeholder="www.example.com/audio.mp3">
                          </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" id="audio-go" class="btn btn-primary">Play Audio</button>
                    </div>
                </form>
                <script type="text/javascript">
                $("#audio_form").submit( function(event){
                    event.preventDefault();
                    var messagedata = "play(" + $("#audio-url").val() + ")";
                    $.ajax({
                        url: "send_message.php",
                        type: 'GET',
                        data: {
                            regId: '<?php echo $_SESSION["registration"] ?>',
                            message: messagedata
                        },
                        success: function(){ $("#audio-url").val(""); }
                    });
                    $("#audioModal").modal('hide');
                });
                </script>
              </div>
            </div>
          </div>
        <div class="btn-row">
          <!-- Button trigger modal -->
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#callModal">
            <span class="glyphicon glyphicon-earphone"></span> Call 
          </button>

          <!-- Modal -->
          <div class="modal fade" id="callModal" tabindex="-1" role="dialog" aria-labelledby="callModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="callModalLabel">Call Number</h4>
                </div>
                <form role="form" id="phone_form" action="javascript:void(0);" style="display:block" >
                      <div class="modal-body">
                          <div class="form-group">
                              <div class="row">
                                  <div class="col-sm-6">
                                      <label for="call-num" class="control-label">Phone Number/Contact</label>
                                      <div class="input-group">
                                          <input type="text" id="call-num" class="phone-num form-control" data-cip-id="phone">
                                          <span class="input-group-btn">
                                              <button type="button" id="call-address-book-btn" class="btn btn-default address-book-btn"><span class="glyphicon glyphicon-book"></span></button>
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="alert alert-info" role="alert"><strong>Note:</strong> Pressing <em>Call Number</em> will place a call as if the number is dialed on the device. The call will not be routed through this Control Center.</div>
                      </div>
    
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="call-go" class="btn btn-primary">Call Number</button>
                      </div>
                </form>

              </div>
            </div>
          </div>

          <script type="text/javascript">
          var call_skip_validation;

          $(document).ready(function(){
              $("#phone_form > div.modal-footer > button.btn.btn-default").click(function(){
                  $("#call-go").popover("destroy");
              });
              call_skip_validation = false;
          });

          $('#phone_form').submit( function(event) {
              event.preventDefault();

              phoneNumEl = $("#call-num");

              var phoneNumber;
              if (!call_skip_validation && phoneNumEl.intlTelInput("isValidNumber") === false) {
                  var xcontent = 'Please enter a valid phone number; you may select a country by clicking on the flag. <button class="btn btn-primary" id="phone-call-anyway">Call Anyway</button>';
                  $("#call-go").popover(
                      {
                          container: "body",
                          html : true, 
                          content: xcontent,
                          title: "Warning: Invalid Phone Number",
                          placement: "bottom",
                          trigger: "manual"
                      }
                  ).popover('show');
                  $("#phone-call-anyway").click(function(){
                      call_skip_validation = true;
                      $('#phone_form').submit();
                  });
                  return false;
              }
              else {
                  phoneNumEl.popover('destroy');
              }
              if (call_skip_validation) {
                  phoneNumber = phoneNumEl.val();
              }
              else {
                  phoneNumber = phoneNumEl.intlTelInput("getNumber");
              }

              //phoneNumber = phoneNumber.replace(/\D/g,'');
              var messagedata = "call(" + phoneNumber + ")";
              $.ajax({
                  url: "send_message.php",
                  type: 'GET',
                  data: {
                      regId: '<?php echo $_SESSION["registration"] ?>',
                      message: messagedata
                  },
                  success: function(){ $("#call-num").val(""); }
              });
              $("#callModal").modal('hide');
              call_skip_validation = false;
          });
          </script>

          <!-- Button trigger modal -->
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#openPageModal">
            Open URL
          </button>

          <!-- Modal -->
          <div class="modal fade" id="openPageModal" tabindex="-1" role="dialog" aria-labelledby="openPageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="openPageModalLabel">Open URL</h4>
                </div>
                    <form id="url_form" action="javascript:void(0);">
                        <div class="modal-body">
                              <label for="open-url" class="control-label">URL</label>
                              <div class="input-group" style="padding-right: 0; padding-left: 0; margin-left">
                                  <span class="input-group-addon" style="display: table-cell; float: none">http://</span>
                                  <input type="text" id="open-url" class="form-control" placeholder="www.example.com">
                              </div>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" id="openPage-go" class="btn btn-primary">Open URL</button>
                        </div>
                    </form>

              </div>
            </div>
          </div>

          <script type="text/javascript">
          $('#url_form').submit( function(event){
              event.preventDefault();
              var messagedata = "open(" + $("#open-url").val() + ")";
              $.ajax({
                  url: "send_message.php",
                  type: 'GET',
                  data: {
                      regId: '<?php echo $_SESSION["registration"] ?>',
                      message: messagedata
                  },
                  success: function(){ $("#open-url").val(""); }
              });
              $("#openPageModal").modal('hide');
          });
          </script>
         
          <!-- Button trigger modal -->
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sendSmsModal">
            <span class="glyphicon glyphicon-send"></span> Send SMS
          </button>

          <!-- Modal -->
          <div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="sendSmsModalLabel">Send SMS</h4>
                </div>
                <form role="form" id="sms_form" action="javascript:void(0);" style="display:block">
                  <div class="modal-body">
                      <div class="form-group" style="margin-bottom: 10px">
                          <div class="row">
                              <div class="col-sm-6">
                                  <label for="sms-num" class="control-label">Phone Number/Contact</label>
                                  <div class="input-group">
                                      <input type="text" id="sms-num" class="phone-num form-control" data-cip-id="phone">
                                      <span class="input-group-btn">
                                          <button type="button" id="sms-address-book-btn" class="btn btn-default address-book-btn"><span class="glyphicon glyphicon-book"></span></button>
                                      </span>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="form-group"  style="padding-bottom: 10px">
                          <label for="sms-msg" class="control-label">Message</label>
                          <textarea class="form-control" placeholder="Message" rows="2" id="sms-msg"></textarea>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="sms-go" class="btn btn-primary">Send SMS</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <script type="text/javascript">
          var sms_skip_validation;
          $(document).ready(function(){
              $("#sms_form > div.modal-footer > button.btn.btn-default").click(function(){
                  $("#sms-go").popover("destroy");
              });
                      
              sms_skip_validation = false;
          });

          $('#sms_form').submit( function(event){
              event.preventDefault();

              phoneNumEl = $("#sms-num");
              var phoneNumber;
              if (!sms_skip_validation && phoneNumEl.intlTelInput("isValidNumber") === false) {
                  var xcontent = 'Please enter a valid phone number; you may select a country by clicking on the flag.<br/><button class="btn btn-primary" id="sms-send-anyway">Send Anyway</button>';
                  $("#sms-go").popover(
                      {
                          //container: "body",
                          html : true, 
                          content: xcontent,
                          title: "Warning: Invalid Phone Number",
                          placement: "bottom",
                          trigger: "manual"
                      }
                  ).popover('show');
                  $("#sms-send-anyway").click(function(){
                      sms_skip_validation = true;
                      $('#sms_form').submit();
                  });
                  return false;
              }
              else {
                  phoneNumEl.popover('destroy');
              }
              if (sms_skip_validation) {
                  phoneNumber = phoneNumEl.val();
              }
              else {
                  phoneNumber = phoneNumEl.intlTelInput("getNumber");
              }
              //phoneNumber = phoneNumber.replace(/\D/g,'');

              var messagedata = "sendsms(" + phoneNumber + "," + $("#sms-msg").val() + ")";
              $.ajax({
                  url: "send_message.php",
                  type: 'GET',
                  data: {
                      regId: '<?php echo $_SESSION["registration"] ?>',
                      message: messagedata
                  },
                  success: function(){ $("#sms-msg, #sms-num").val(""); }
              });
              sms_skip_validation = false;
              $("#sendSmsModal").modal('hide');
          });
          </script>
        </div>
      </div>
    </div>
  </div>
</div>
