<?php 
  if (!isset($_SESSION)) { session_start(); }
  if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
    header("location:index.php");
    exit();
  }
  $rName = $_SESSION["registration"];
  $rowId = $_SESSION["rowid"];
  unset($_SESSION['audio_filetime']);
?>
<span id="recordx_tab_elecont">

<style type="text/css">
.loading-msg {
  margin-top: 35px;
  margin-bottom: 30px;
  display: block;
}
.webkit-only {
  display: none;
  display: -webkit-inline-box;
}
.alert-hidden {
  display: none;
}
#txtHint {
  display: block;
}
#audio-disp {
  display: none;
}
#audio-last {
  display: none;
}
.spinner {
  display: block;
}

/*#txtHint {
  display: none;
}*/
#success-header { 
	display: none;
	font-size: 18pt;
	font-weight: bolder;
}
.error-msg {
	display: none;
	font-size: 12pt;
	text-align: center;
	font-weight: bold;
	color: red;
}
#audio-err-msg {
	display: none;
	margin: 2em;
}
#test {
  padding-top: 5px;
}
/*.spinner {
  display: none;
}*/
audio {
  margin-top: 30px;
}
.record-spinner {
  margin: 100px auto;
  width: 50px;
  height: 30px;
  text-align: center;
  font-size: 10px;
  display: none;
}
.record-spinner > div {
  background-color: #333;
  height: 100%;
  width: 6px;
  display: inline-block;
  position: relative;
  -webkit-animation: stretchdelay 1.2s infinite ease-in-out;
  animation: stretchdelay 1.2s infinite ease-in-out;
}
.record-spinner .rect2 {
  -webkit-animation-delay: -1.1s;
  animation-delay: -1.1s;
}

.record-spinner .rect3 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

.record-spinner .rect4 {
  -webkit-animation-delay: -0.9s;
  animation-delay: -0.9s;
}

.record-spinner .rect5 {
  -webkit-animation-delay: -0.8s;
  animation-delay: -0.8s;
}

@-webkit-keyframes stretchdelay {
  0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
  20% { -webkit-transform: scaleY(1.0) }
}

@keyframes stretchdelay {
  0%, 40%, 100% { 
    transform: scaleY(0.4);
    -webkit-transform: scaleY(0.4);
  }  20% { 
    transform: scaleY(1.0);
    -webkit-transform: scaleY(1.0);
  }
}
#txtHint > .warning-msg {
    display: none;
}
#txtHint > .record-timer {
  text-align: center;
}
#txtHint > .record-timer:not([data-time-s=""])::after {
  content: " second(s) remaining";
  color: #999;
}
#no-data-warning {
  display: none;
}
</style>
<div id="test"></div>
<div class="modal fade" id="recordAudioModal" tabindex="-1" role="dialog" aria-labelledby="recordAudioModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="recordAudioModalLabel">Record Audio</h4>
      </div>
      <form role="form" id="record_audio_form" action="javascript:void(0);" class="<?php echo $_SESSION["name"] ?>">
          <div class="modal-body">
              <div class="form-group">
                  <h4 style="margin-bottom: 5px">Record for...</h4>
                  <div class="input-group">
                    <input type="number" id="audio-record-seconds" max="30" min="1" class="form-control"/>
                    <span class="input-group-addon">minute(s)</span>
                  </div>
              </div>
              <p class="help-block">Maximum duration: 30 minutes</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="record-audio-go" class="<?php echo $_SESSION["name"] ?> btn btn-primary">Record Audio</button>
          </div>
      </form>
      <script type="text/javascript">
      var audioLoadTime = 10000;

      tabsUnloadData["recordx.php"] = function(){
        if (recordingActive) {
          $("#prodTabs .record-timer").attr("data-time-s", $("#txtHint > .record-timer").attr("data-time-s"));
        }

        var pageUnloadProc = function() {
          if (! $("#record_tab").parent().hasClass("active")) {
            var xcontent = 'Recording will continue in the background';
            $("#record_tab").popover(
              {
                container: 'body',
                content: xcontent,
                placement: "bottom",
                trigger: "manual"
              }
            ).popover('show');
            setTimeout(function(){
              $("#record_tab").popover('destroy');
            }, 1100);

            window.record_interval_write_to_page = false;
            autocollapse();
          }
        };
        console.log("unloading recordx");
        if (recordingActive) {
          setTimeout(pageUnloadProc, 50);
        }
      };
      var dateoptions = { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric' };

      var updateAudioTime = function(){
        var d = new Date(0);
        if (!Cookies.get("audio_filetime")) {
          setTimeout(updateAudioTime, 100); // wait until cookie is set by audioecho script
          return;
        }
        else {
          d.setUTCSeconds(Number(Cookies.get("audio_filetime")));
        }
        $("#last-record").text(d.toLocaleDateString('en-US', dateoptions));
        setTimeout(function(){Cookies.remove("audio_filetime");}, 15);
      };
      $('#audio-record-seconds').keypress(function(event) {
        var key = window.event ? event.keyCode : event.which;
        if (event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39) {
          return true;
        }
        else if ( key < 48 || key > 57 ) {
          event.preventDefault();
          event.stopPropagation();
          return false;
        }
        else if (Number($("#audio-record-seconds").val()) > 30) {
          event.preventDefault();
          event.stopPropagation();
          return false;
        }
        else return true;
      }).keyup(function() {
        if (Number($(this).val()) > 30) {
          $(this).val("30");
        }
      });
      var recordingActive = true;
      var updateAudioDone = function() {
        recordingActive = false;
        $(".alert-info, .alert-warning").removeClass("alert-hidden");
        $('#txtHint').css("display", "none");
        var audio = $('#audio-el');
        audio[0].load();
        audio.on("error", function(e) {
          $("#no-data-warning").fadeIn();
        });
        $("#audio-disp").css("display", "inline-block");
        $("#audio-last").css("display", "inline");
        $('#audio-record-launch').prop("disabled", false);
        $("#last-record").text("");
        $('.spinner').css("display","none");
        
        setTimeout(updateAudioTime, 100);
      };
      var stageTwo = function() {
        $("#txtHint .warning-msg").css("display", "block");
        $('.record-spinner').css("display", "block");
        $(".alert-info, .alert-warning").addClass("alert-hidden");
        $('#audio-record-launch').prop("disabled", true);
        $('#audio-disp').css("display", "none");
        $("#audio-last").css("display", "none");
        $('#txtHint').css("display", "block");
        setTimeout(function() {
          $('.record-spinner').css("display", "none");
          $('.spinner').css("display","block");
          $('.loading-msg').text("Please wait as we load your audio...");
          setTimeout(function(){
            handleCallback("record", "audiouploadready.php", updateAudioDone, "<?php echo $rName ?>", "<?php echo $rowId ?>");
          }, Number($("#audio-record-seconds").val()) * 200 * 60 + 300);
        }, Number($("#audio-record-seconds").val()) * 1000 * 60);
      };
      $('#txtHint').css("display", 'block');
      
      var updateTime = function(selector, t) {
          $(selector).text(t.toFixed(0));
          $(selector).attr("data-time-s", t);
      };

      var initRecordingProc = function() {

        var a = setInterval(function(){
          var selector = "#prodTabs .record-timer";
          if (window.record_interval_write_to_page) {
            selector = "#txtHint > .record-timer";
          }

          var t = Number($(selector).attr("data-time-s")) - 1;
          if (t < 0) {
            //$(".record-timer").text("");
            clearInterval(window.record_interval_id);
          }
          else {
            updateTime(selector, t);
          }
        }, 1000);
        return a;
      };

      $(document).ready(function() {
        $('#txtHint').css("display", 'block');
        
        document.title = "Monitordroid - Recording";
        //setTimeout(updateAudioTime, 225);
        var loadTime = Number(localStorage.getItem("lastRefreshrecordtime" + "For" + "<?php echo $rowId ?>")) / 1000;

        
        var lastUpdTime = Math.ceil(new Date(localStorage.getItem("lastRefreshrecord" + "For" + "<?php echo $rowId ?>")) / 1000);
        
        console.log('loadtime', loadTime);
        console.log('lastUpdTime', lastUpdTime);
        var deltaToCompleteTime;
        if (loadTime != null) {
          var expectedCompleteTime = lastUpdTime + loadTime;
          deltaToCompleteTime = expectedCompleteTime - (new Date() / 1000); // should subtract current time
          console.log('deltaToCompleteTime', deltaToCompleteTime);
          if (deltaToCompleteTime > 0) { // if positive then we still have to wait
            setTimeout(function(){
              handleCallback("record", "audiouploadready.php", updateAudioDone, "<?php echo $rName ?>", "<?php echo $rowId ?>");
            }, deltaToCompleteTime * 1000); // 
          }
          else { // otherwise load immediately
            handleCallback("record", "audiouploadready.php", updateAudioDone, "<?php echo $rName ?>", "<?php echo $rowId ?>");
          }
        }
        else {

          handleCallback("record", "audiouploadready.php", updateAudioDone, "<?php echo $rName ?>", "<?php echo $rowId ?>");
        }
        if (window.record_interval_id != null || deltaToCompleteTime > 0) {
          //clearInterval(window.record_interval_id);          
          $("#txtHint > .record-timer").attr("data-time-s", (deltaToCompleteTime > 0) ? deltaToCompleteTime : $("#prodTabs .record-timer").attr("data-time-s"));
          if (window.record_interval_id == null) {
            window.record_interval_id = initRecordingProc();
          }


          $("#prodTabs .record-timer").text("");
          //$("#prodTabs .record-timer").attr("data-time-s", "");
          window.record_interval_write_to_page = true;
        }

      });

      $("#record_audio_form").submit( function(event){
          recordTime = Number($("#audio-record-seconds").val());
          if (recordTime <= 0) {
            alert("Please enter a number greater than zero.")
            return;
          }
          event.preventDefault();
          var messagedata = "record-" + recordTime;
          recordingActive = true;
          audioLoadTime = (Number($("#audio-record-seconds").val()) * 1220 * 60 + 220);
          console.log(audioLoadTime);
          localStorage.setItem("lastRefreshrecordtime" + "For" + "<?php echo $rowId ?>", audioLoadTime);
          sendGCMRefreshMessage(messagedata, '<?php echo $_SESSION["registration"] ?>', function() {
            audioLoadTimeDisp = audioLoadTime / (1000 * 60);
            stageTwo();
          });
          $("#recordAudioModal").modal('hide');


          window.record_interval_write_to_page = true; // currently on page


          /*var date = localStorage.getItem("lastRefreshrecord" + "For" + "<?php echo $rowId ?>");
          var currTime = new Date();

          var dateDiff = (currTime.getTime() - new Date(date)) - audioLoadTime;
          dateDiff *= -1;*/
          

          var at = audioLoadTime / 1000;
          updateTime("#txtHint > .record-timer", at);

          window.record_interval_id = initRecordingProc();
          

      });
      </script>
    </div>
  </div>
</div>
<div style="margin-top: -40px; padding: 0">
<div class="form-inline">
  <button type="button" class="btn btn-success" data-toggle="modal" id="audio-record-launch" data-target="#recordAudioModal" disabled>
    <span class="glyphicon glyphicon-record"></span> Record Audio
  </button>
  <span id="audio-last" class="help">Recording From: <span id="last-record"></span></span>
</div>
<div class="alert alert-danger" role="alert" id="no-data-warning">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Warning:</span>
  <strong>Oops</strong> looks like we couldn't find any data for this category. Please click <em>Record Audio</em>.
</div>
<div class="alert alert-danger" role="alert" id="device-error">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	<span class="sr-only">Error:</span>
	Humm... Looks like there was a problem getting your audio. <span id="device-error-msg"></span>
</div>
<div id="audio-disp" style="margin-top: 6px">
  <audio controls preload="auto" style="width:480px" id="audio-el">
    <source src="./audioecho.php" type="audio/mp4" />
    Your browser does not support HTML5 audio.
  </audio>
  <p>
    <a href="./audioecho.php?d=download">Download audio (m4a)</a>
  </p>
</div>
<div id="txtHint">
  <div class="loading-msg">Recording your audio...</div>
  <div class="record-spinner">
    <div class="rect1"></div>
    <div class="rect2"></div>
    <div class="rect3"></div>
    <div class="rect4"></div>
    <div class="rect5"></div>
  </div>

  <br/>
  <div class="spinner"></div>
  <div class="record-timer" data-time-s=""></div>
</div>
<div class="alert alert-info alert-hidden" role="alert">
  <span class="glyphicon glyphicon-info-sign" aria-hidden="true" id="one-recording-alert"></span>
  <strong>Please note</strong> you can only have one recording at a time across all devices.
  This is the last recording made, and may be from a different device. To record for the current device, click Record Audio above.
</div>
<div class="alert alert-warning webkit-only alert-hidden" role="alert" id="webkit-alert">
  <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
  <strong>Warning</strong> in Safari and Chrome, long audio files may not play properly. If the audio file does not play with the controls above, please click Download Audio and listen to it on your computer
</div>
</div>
</span>