<?php session_start(); ?>
<style type="text/css">
.buffer {
  width: 20em;
  /*position: absolute;*/
  margin-left:auto;
  margin-right:auto;
  text-align:center;
  /*-webkit-transform: translateX(-50%);
      -ms-transform: translateX(-50%);
          transform: translateX(-50%); }*/
}

.layer {
  fill: #4b4b4b;
  -webkit-transition: all 21s ease-out;
          transition: all 21s ease-out; }

.loading .layer1 {
  -webkit-animation: 4s linear infinite;
          animation: 4s linear infinite;
  -webkit-animation-name: cycle1;
          animation-name: cycle1; }

.loading .layer2 {
  -webkit-animation: 4s linear infinite;
          animation: 4s linear infinite;
  -webkit-animation-name: cycle2;
          animation-name: cycle2; }

.loading .layer3 {
  -webkit-animation: 4s linear infinite;
          animation: 4s linear infinite;
  -webkit-animation-name: cycle3;
          animation-name: cycle3; }

@-webkit-keyframes cycle1 {
  0%, 65% {
    fill: #b2b2b2; }
  75%, 82% {
    fill: #4b4b4b; }
  92%, 100% {
    fill: #b2b2b2; } }

@keyframes cycle1 {
  0%, 65% {
    fill: #b2b2b2; }
  75%, 82% {
    fill: #4b4b4b; }
  92%, 100% {
    fill: #b2b2b2; } }

@-webkit-keyframes cycle2 {
  0%, 40% {
    fill: #b2b2b2; }
  50%, 86% {
    fill: #4b4b4b; }
  96%, 100% {
    fill: #b2b2b2; } }

@keyframes cycle2 {
  0%, 40% {
    fill: #b2b2b2; }
  50%, 86% {
    fill: #4b4b4b; }
  96%, 100% {
    fill: #b2b2b2; } }

@-webkit-keyframes cycle3 {
  0%, 15% {
    fill: #b2b2b2; }
  25%, 90% {
    fill: #4b4b4b; }
  100% {
    fill: #b2b2b2; } }

@keyframes cycle3 {
  0%, 15% {
    fill: #b2b2b2; }
  25%, 90% {
    fill: #4b4b4b; }
  100% {
    fill: #b2b2b2; } }



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
.file-body {
    display: none;
}
#audio-err-msg {
    display: none;
    margin: 2em;
}
.usr-file {
    max-width:100%; 
    max-height:100%;
    margin:auto;
    display:block;
}
.xhide {
    display: none;
}
.phide {
    display: none;
}
#file-list > div > a:hover {
    cursor: pointer;
}
#txtHint .warning-msg {
    display: none;
}
#files {
    display: none;
}
.file-ajax:hover td {
    background-color: #e9e5e5;
}
tr.disabled {
    display: none;
    background-color: #ddd !important;
}
tr td {
    cursor: pointer;
}

tr.disabled td {
    cursor: not-allowed;
}
table tbody tr td:nth-child(3):not([data-value=""])::after {
    content: " KB";
    color: rgba(0,0,0,.6);
}
a:hover {
    cursor: pointer;
}
#path-display {
    width: 250px;
}
@media (max-width: 768px) {
    #filesTable > thead > tr > th:nth-child(1) {
        width: 17.5%;
    }
    #filesTable > tbody > tr > td:nth-child(2) {
        overflow-x: hidden;
    }
    .form-inline .input-group {
        display: inline-table;
        vertical-align: middle;
    }
    body {
        padding-top: 132px;
    }
}
@media (max-width: 616px) {
    body {
        padding-top: 168px;
    }
}
@media (max-width: 512px) {
    #path-display {
        width: 180px;
    }
    .form-inline .input-group.filter {
        display: inline-table;
        vertical-align: middle;
    }
    .form-inline .input-group {
        position: relative;
        display: table;
        border-collapse: separate;
    }
}
#filesTable > thead > tr > th {
    overflow-x: hidden;
}
#load-buttons > div > div:nth-child(4) > div > button.btn.btn-default.dropdown-toggle {
    border-bottom-right-radius: 4px;
    border-top-right-radius: 4px;
}
</style>
    
<?php
    if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid']) && !isset($_SESSION['name'])) {
        header("location:index.php");
        exit();
    }
    $rName = $_SESSION["registration"];
    $rowId = $_SESSION["rowid"];
?>

<link rel="stylesheet" type="text/css" href="./lib/bootstrap-sortable/Contents/bootstrap-sortable.css"></style>
<script type="text/javascript" src="./lib/bootstrap-sortable/Scripts/bootstrap-sortable.js"></script>

<script type="text/javascript">
jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
    return function( elem ) {
        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});
var updateFilter = function() {
    $('tr.dfile:not(:Contains('+$("#file-list-search-keyword").val()+'))').addClass("xhide");
};
var timer;
var removeFilter = function() {
    $(".xhide").removeClass("xhide");
};
var file_list_filter_type_keyup = function() {
    clearTimeout(timer);
    timer = setTimeout(function (event) {
        removeFilter();
        updateFilter();
    }, 200);
};
var file_list_filter_active_click = function() {
    $("#file-list-filter-go").removeClass("active");
    $("#file-list-search-keyword").unbind("keyup");
    removeFilter();
    $("#file-list-filter-go").unbind("click", file_list_filter_active_click);
    $("#file-list-filter-go").bind("click", file_list_filter_inactive_click);
};
var file_list_filter_inactive_click = function() {
    $("#file-list-filter-go").addClass("active");
    updateFilter();
    $("#file-list-search-keyword").bind("keyup", file_list_filter_type_keyup);
    $("#file-list-filter-go").unbind("click", file_list_filter_inactive_click);
    $("#file-list-filter-go").bind("click", file_list_filter_active_click);
};

var calcTime = function(offset) {
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
    return Math.floor(nd.getTime() / 1000);
};

var globalPath;

var display_extension = function(ext) {
    if (ext == "directory" ) {
        return '<span class="glyphicon glyphicon-folder-open"></span><span class="phide">folder directory</span>';
    }
    else {
        return ext;
    }
};

var loadFile = function(xthis){
    //alert("Development in progress");
    //with ({xthis: $(this)}) {
    if (Number(xthis.data("size")) > 5000) {
        $("#file-too-large-modal").modal("show");
        return;
    }
    var suid = xthis.data("name");
    var ext = xthis.data("ext");
    $("#file-title").text(suid);
    $("#file-loading").show();
    $("#file-body").hide();
    $("#fileDispModal").modal("show");
    var denverTime = String(calcTime(-2));
    $("#fileDispModal").on('hide.bs.modal', function (e) {
        $("#file-body").html("");
    });

    var fileStageTwo = function() {
        setTimeout(function() {
            console.log("dtime is " + denverTime);
            $.ajax({
              url: "checkfileload.php",
              type: "POST", 
              data: { start_time: denverTime, ext: ext }
            }).done(function(result) {
                if (result == "true") {
                    if (ext.toLowerCase() == "gif" || ext.toLowerCase() == "jpg" ||
                        ext.toLowerCase() == "jpeg" || ext.toLowerCase() == "png") {
                       $("#file-body").html('<image class="usr-file" src="./getfile.php?ext=' + ext + '" alt="Device Image">');
                    }
                    else if (ext.toLowerCase() == "txt") {
                        $.ajax({
                            method: "get",
                            url: "./getfile.php",
                            data: { "ext": ext } 
                        }).done(function(res){
                            $("#file-body").html(res);
                        });
                    }
                    else {
                        $("#file-body").html('<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span><span class="sr-only">File Download</span> File successfully loaded. Press Download below to retrieve it.</div>');
                    }
                    
                    $("#file-download-btn-container").html('<a class="btn btn-primary" aria-label="Download File" href="./getfile.php?ext=' + ext + '&name=' + suid + '">Download <span class="glyphicon glyphicon-download" aria-hidden="true"></span></a>');
                    $("#file-loading").fadeOut(function(){
                        $("#file-body").fadeIn();
                    });
                    
                }
                else {
                   fileStageTwo();
                }
              });
        }, 10000);
    };
    var stf = "uploadfile(" + suid + ");" + globalPath;
    console.log(stf);
    $.ajax({
      url: "send_message.php",
      type: "GET",
      data: {
        message: stf,
        regId: "<?php echo $rName ?>",
      },
      success:function(result){
        fileStageTwo();
        $("#file-download-btn-container").html('');
      }
    });
    //}
};

var gotoDirectory = function(){
    globalPath = $(this).attr("data-target");
    updatePage();
};

$('#change-path-goto').click( function(event){
    event.preventDefault();
    globalPath = $("#path-display").val();
    if (globalPath.slice(-1) != "/") {
        globalPath = globalPath + "/";
    }
    if (globalPath.substr(0, 1) != "/") {
        globalPath =  "/" + globalPath;
    }
    updatePage();
});

var changeDirectory = function(element) {
    console.log("cd");
    if (element.attr("data-ext") == "up") {
        if (globalPath == "/") {
            return;
        }
        var s = globalPath.split("/");
        s.pop();
        s.pop();
        globalPath = s.join('/');
    }
    else {
        globalPath += element.attr("data-name") + "/";
    }
    if (globalPath.substring(0,1) != '/') {
        globalPath = "/" + globalPath;
    }
    if (globalPath.slice(-1) != "/") {
        globalPath = globalPath + "/";
    }
    if (globalPath != "/") {
        $("tr[data-ext=up]").removeClass("disabled");
    }
    else {
        $("tr[data-ext=up]").addClass("disabled");
    }

    updatePage();
};

var handleFileClick = function(evt){
    evt.stopPropagation();
    evt.preventDefault();
    if ($(this).attr("data-ext") == "directory" ||
        $(this).attr("data-ext") == "up") {
        $(".file-ajax").unbind("click");
        $(".goto").unbind("click");
        changeDirectory($(this));
    }
    else {
        loadFile($(this));
    }
};

var initializeAjaxElements = function(result) {
    var files_data;

    try {
        
        files_data = jQuery.parseJSON( result );
    }
    catch (err) {
        $('#files-container').html(result);
        $("#txtHint").css("display", "none");
        $("#files").css('display', 'block');
        $("#update_btn").prop("disabled", false);
        $("#change-path-goto").prop("disabled", false);
        $(".goto").prop("disabled", false);
        $("#path-display").val(globalPath);
        $(".file-ajax").click(handleFileClick);
        $(".goto").click(gotoDirectory);

        autocollapse();
        return;
    }
    if ($.isEmptyObject(files_data)) {
        $("#txtHint").css("display", "none");
        //$("#no-data-warning").css("display", "block");
        $("#files").css('display', 'block');
        $("#path-display").val(globalPath);
        $("#update_btn").prop("disabled", false);
        $("#change-path-goto").prop("disabled", false);
        $(".goto").prop("disabled", false);
        $(".file-ajax").click(handleFileClick);
        $(".goto").click(gotoDirectory);
        autocollapse();
        return;
    }
    console.log(files_data);
    globalPath = files_data[0].filepath;
    delete files_data[0].filepath;

    if (globalPath == "/") {
        $("tr[data-ext=up]").addClass("disabled");
    }

    var file_display = "";

    for ( var i = 1; i < files_data.length; i++ ) {
        file_display += '<tr class="file-ajax dfile" data-ext="' + files_data[i].extension + '" data-name="' +
        files_data[i].fileName + '" data-size="' +
        (files_data[i].hasOwnProperty("file_size") ? files_data[i].file_size.substring(0, files_data[i].file_size.length - 2) : '0') + '">';
        file_display += "<td>" + display_extension(files_data[i].extension) + "</td>";
        file_display += "<td>" + files_data[i].fileName + "</td>";
        if (files_data[i].hasOwnProperty("file_size")) {
            file_display += "<td>" + files_data[i].file_size.substring(0, files_data[i].file_size.length - 2) + "</td>";
        }
        else {
            file_display += "<td></td>";
        }
        file_display += "</tr>";
    }

    $('#files-container').html(file_display);
    $.bootstrapSortable(false, 'reversed'); // initialize sorter
    $("#txtHint").css("display", "none");
    $("#files").css('display', 'block');
    autocollapse();
    
    $("#path-display").val(globalPath);
    $("#update_btn").prop("disabled", false);
    $("#change-path-goto").prop("disabled", false);
    $(".goto").prop("disabled", false);
    $("#txtHint").css("display", "none");
    $(".file-ajax").click(handleFileClick);
    $(".goto").click(gotoDirectory);
};

var stageTwo = function() {
    /*var date;
    var localDate = localStorage.getItem("lastRefreshgetfilelist");
    if (localDate === null) {
      localStorage.setItem("lastRefreshreadsms", "Sat, 28 May 2000 05:07:43 GMT");
      console.log("WARNING: Date session storage for requested delay is not avaliable");
      date = 0;
    }
    else {
      date = (+ new Date(localDate)) / 1000;
    }
    $("#txtHint").css("display", "block");
    var iters = 0;*/

    var loadedCallback = function(result) {
      window.scrollTo(0, document.body.scrollTop - 250);
      //$(window).bind('scroll', handleScroll);
      scrollUpdateEnabled = true;
      generateDisplay(result);
      $("#loadMore").css("display", "none");
      updateFilter();
      $("#update_btn").prop("disabled", false);
      $(".goto").prop("disabled", false);
      $("#change-path-goto").prop("disabled", false);
    };
    //poll("viewfilesdata.php", "<?php echo $rName ?>", "<?php echo $rowId ?>", date, initializeAjaxElements, "getfilelist");
    handleCallback("getfilelist", "viewfilesdata.php", initializeAjaxElements, "<?php echo $_SESSION["registration"] ?>", "<?php echo $_SESSION["rowid"] ?>");

    /*setTimeout(function() { 
        $.ajax({
            url: "viewfilesdata.php",
            type: "POST", //Or even get
            data: { 
              //These are the variables and their relative values
                registration: "<?php echo $rName ?>", 
                rowid: "<?php echo $rowId ?>"
            }
        }).done(function(result){
            initializeAjaxElements(result);
        });
    }, 14000);*/
};

var updatePage = function() {
    $("#txtHint").css("display", "block");
    $("#txtHint .warning-msg").css("display", "block");
    //$("#file-list").css("display", "none");
    window.scrollTo(0, 0);
    $("#update_btn").prop("disabled", true);
    $(".goto").prop("disabled", true);
    $("#change-path-goto").prop("disabled", true);
    if (localStorage) {
        localStorage.setItem("filesPath", globalPath);
    }
    if (globalPath.indexOf(";") >= 0) {
        globalPath = globalPath.replace(';','');
    }
    $("#path-display").val(globalPath);
    sendGCMRefreshMessage("getfilelist;" + globalPath, "<?php echo $rName?>", function(result){ stageTwo(); });
};

$(document).ready(function() {
    if (localStorage.getItem("filesPath")) {
        globalPath = localStorage.getItem("filesPath");
    }
    else {
        globalPath = "/";
    }
    document.title = "Monitordroid - View Files";
    /*$.ajax({
        url: "viewfilesdata.php",
        type: "POST", //Or even get
        data: { 
          //These are the variables and their relative values
            registration: "<?php echo $rName ?>", 
            rowid: "<?php echo $rowId ?>"
        },
        success:function(result){
            initializeAjaxElements(result);
            //$(".file-ajax").click(handleFileClick);

        }
    });*/
    handleCallback("getfilelist", "viewfilesdata.php", initializeAjaxElements, "<?php echo $rName ?>", "<?php echo $rowId ?>");

    $("#update_btn").bind("click", updatePage);
    $("#file-list-filter-go").bind("click", file_list_filter_active_click);
    $("#file-list-search-keyword").bind("keyup", file_list_filter_type_keyup);
    $("#fileDispModal").on('hide.bs.modal', function(){
        $("#file-body").html("");
    });
    /*if (Cookies.get('options_autoRefreshPages') == "true") {
        var updateIntervalID = setInterval(updatePage, (Number(Cookies.get('options_autoRefreshFreq') ? Cookies.get('options_autoRefreshFreq') : 1)) * 1000 * 60);
        tabsUnloadData["viewfilesx.php"] = function(){
            clearInterval(updateIntervalID);
            if(typeof(Storage) !== "undefined") {
                localStorage.setItem("filesPath", globalPath);
            }
        };
    }*/
    //else {
        /*tabsUnloadData["viewfilesx.php"] = function(){
            if(typeof(Storage) !== "undefined") {
                localStorage.setItem("filesPath", globalPath);
            }
        }*/
    //}

});
</script>
<!--<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>-->
<?php
    if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
        header("location:index.php");
        exit();
    }
    $rName = $_SESSION["registration"];
    $rowId = $_SESSION["rowid"];
?>
<div class="alert alert-danger" role="alert" id="audio-err-msg">
    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
    <span class="sr-only">Error:</span>
    Humm... Looks like there was a problem getting your files.
</div>
<div class="alert alert-danger" role="alert" id="device-error">
  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>
  <strong>Oh No!</strong> <span id="device-error-msg"></span>
</div>
<div id="test"></div>
<span id="load-buttons">
    <div class="form-inline">
        <button class="btn btn-success" id="update_btn">Refresh</button>
        <div class="input-group filter">
          <label class="sr-only" for="file-list-search-keyword">Search Files For</label>
          <form id="file-list-filter" action="javascript:void(0);">
              <input type="text" id="file-list-search-keyword" class="form-control" placeholder="Find..." style="width: 150px;">
              <span class="input-group-btn">
                <button class="btn btn-default active" type="button" id="file-list-filter-go"><span class="glyphicon glyphicon-search"></span></button>
              </span>
          </form>
        </div><!-- /input-group -->
        <div class="input-group path">
          <!--<form id="path-select" action="javascript:void(0);">-->
          <input type="text" id="path-display" class="form-control" placeholder="Path">
          <div class="input-group-btn" style="float: left">
            <button type="button" class="btn btn-success" id="change-path-goto">Go</button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" title="Common Paths" aria-expanded="false">
              <span class="caret" title="Common Paths"></span>
              <span class="sr-only">Toggle Common Paths Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
              <li><a class="goto" data-target="/Music/">/Music/</a></li>
              <li><a class="goto" data-target="/Download/">/Download/</a></li>
              <li role="separator" class="divider"></li>
              <li><a class="goto" data-target="/">/</a></li>
            </ul>
          </div>
        </div><!-- /input-group -->
        <div class="input-group">
          <div class="input-group-btn" style="display: inline-block">
            <button type="button" class="btn btn-success goto" style="border-top-left-radius: 4px; border-bottom-left-radius: 4px" data-target="/DCIM/">Photos</button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" title="Common Paths" aria-expanded="false">
              <span class="caret" title="Common Paths"></span>
              <span class="sr-only">Toggle Common Paths Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
              <li><a class="goto" data-target="/DCIM/Camera/">/DCIM/Camera/</a></li>
              <li><a class="goto" data-target="/Pictures/">/Pictures/</a></li>
            </ul>
          </div>
        </div>
    </div>
</span>
<div id="txtHint">
    <div class="loading-msg">Loading files...</div>
    <br/>
    <div class="spinner"></div>
</div>
<div id="files">
    <table id="filesTable" class="table table-striped sortable" style="table-layout: fixed">
        <thead class="file">
            <tr>
                <th class="file sort">Extension</th>
                <th class="file sort">Filename</th>
                <th class="file sort">Size</th>
            </tr>
        </thead>
        <tbody>
            <tr class="no-sort file-ajax" data-name="up" data-ext="up"><td class="no-sort"><span class="glyphicon glyphicon-level-up" style="font-size: 20px;"></span></td><td class="no-sort">..</td><td class="no-sort"></td></tr>
        </tbody>
        <tbody id="files-container">
        </tbody>
    </table>
</div>
<div class="modal fade" id="file-too-large-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" >
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">File Too Large</h4>
        </div>
        <div class="modal-body">
        <div class="alert alert-danger fade in" role="alert">
            <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
            <strong>This file is too large</strong> to load. Please select a file that is under 5 MB (5000 KB).
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
        </div>
    </div>
  </div>
</div>
<div class="modal fade" id="fileDispModal" tabindex="-1" role="dialog" aria-labelledby="file-title" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="file-title">TITLE</h4>
      </div>
      <div class="modal-body" id="file-body">
      </div>
      <div class="modal-body buffer loading" id="file-loading">
        <svg width="80%" height="100%" x="0px" y="0px" viewBox="0 0 313.3 321.4" style="enable-background:new 0 0 313.3 321.4;" xml:space="preserve">
          <path class="layer layer1" d="M157.1,0c3.8,0,7.9,1.5,11.4,3.2c45.6,21.1,91.2,42.4,136.7,63.8c3.3,1.5,8,2.1,8.1,7c0.1,5-4.7,5.6-7.9,7.1
            c-45.8,21.5-91.7,42.9-137.6,64.3c-7.4,3.4-14.8,3.5-22.2,0C99,123.6,52.3,101.9,5.8,80c-2.4-1.1-5.4-4-5.4-6c0-2,3.1-4.8,5.5-5.9
            C52.4,46.2,99.1,24.4,145.8,2.8C149.1,1.2,154.5,0,157.1,0z"/>
          <path class="layer layer2" d="M156.6,235c-3.1,0-7.7-1.7-11.3-3.3c-46.5-21.6-93-43.3-139.4-65.1c-2.4-1.1-5.8-4.1-5.6-5.8c0.3-2.4,3.2-5,5.7-6.3
            c7.5-4,15.3-7.3,23-10.9c7.9-3.7,15.7-3.6,23.6,0.1c30.4,14.3,61,28.4,91.4,42.7c8.6,4.1,16.8,4,25.4-0.1
            c30.4-14.3,60.9-28.4,91.4-42.7c7.7-3.6,15.4-3.7,23.1-0.1c8.2,3.8,16.4,7.4,24.4,11.5c2.1,1.1,4.9,3.7,4.7,5.4
            c-0.2,2.1-2.7,4.8-4.9,5.9c-46.8,22.1-93.7,44-140.7,65.8C164.2,233.5,159.9,235,156.6,235z"/>
          <path class="layer layer3" d="M156.8,321.4c-4.6,0-9.3-2.3-13.4-4.2c-45.2-20.9-90.3-42-135.4-63.1c-3.3-1.5-8-2.1-8-7.1c0-5,3.8-5.5,7-7.1
            c13.9-6.5,14.9-6.5,21.8-9.8c8-3.8,16-3.7,24.1,0.1c29.9,14,59.8,27.7,89.6,41.9c9.6,4.6,18.5,4.7,28.2,0.1
            c29.8-14.2,59.8-27.9,89.6-41.9c8.2-3.9,16.3-4,24.5,0c7.8,3.8,15.8,7.2,23.5,11.1c2.1,1.1,4.8,3.3,4.8,5.4
            c-0.1,2.4-2.7,4.8-4.9,5.9c-32.2,15.3-64.5,30.3-96.8,45.4c-13.9,6.5-27.7,13.1-41.7,19.3C165.7,319.2,161.5,321.4,156.8,321.4z"/>
        </svg>
        <p class="help-block" style="text-align:center;margin-top:25px">Loading your file...</p>
      </div>

      <div class="modal-footer" id="file-modal-footer">
        <span id="file-download-btn-container"></span>
        <button type="button" class="pic-modal-close btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

