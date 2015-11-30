<?php session_start(); ?>

<style type="text/css">
.error-msg {
    display: none;
    font-size: 12pt;
    text-align: center;
    font-weight: bold;
    color: red;
}
.noclick {
    cursor: default;
}
#txtHint .warning-msg {
    display: none;
}
.app-icon {
    margin-right: 5px;
}
#no-data-warning {
    display: none;
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/yui/3.18.0/yui/yui-min.js"></script>
<script type="text/javascript">
var dateTimeLastUpdated = null;
var stageTwo = function() {

    var loadedCallback = function(result) {
        $('#app-install-list').html("<a href=\"#\" class=\"list-group-item active\"><h4 class=\"list-group-item-heading\">Google Play Apps</h4></a>" + result);
        $("#applist").css("display", "block");
        $("#update_btn").prop("disabled", false);
        $("#txtHint").css("display", "none");
        addLinks();
    };
    handleCallback("getapps", "applistdata.php", loadedCallback, '<?php echo $rName ?>', '<?php echo $rowId ?>');
    //poll("applistdata.php", "<?php echo $rName ?>", "<?php echo $rowId ?>", date, loadedCallback, "getapps");

};
var updatePage = function() {
    $("#no-data-warning").css("display", "none");
    $("#txtHint").css("display", "block");
    $("#txtHint .warning-msg").css("display", "block");
    $("#update_btn").prop("disabled", true);
    var dateTimeLastUpdated = Date.now();
    sendGCMRefreshMessage("getapps", "<?php echo $rName?>", function(result){ stageTwo(); });
};

function isURLReal(fullyQualifiedURL, container) {
    console.log("running isurlreal for "+ fullyQualifiedURL);
    var URL = encodeURIComponent(fullyQualifiedURL),
        dfd = $.Deferred();
        
    if(localStorage && localStorage.getItem('urlReal_' + fullyQualifiedURL)) {

        if (localStorage.getItem('urlReal_' + fullyQualifiedURL) == "true") {
            var imgurl = localStorage.getItem('urlIcon_' + fullyQualifiedURL);
            container.prepend("<img class=\"app-icon\" src=\"" + imgurl + "\" width=\"25\" />");

            return dfd.resolve(true);
        }
        else {
            return dfd.reject(false);
        }
    }
    else {
        var checkURLPromise = $.ajax({dataType: "text", url: 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22' + URL + '%22&format=json'});

        checkURLPromise.done(function(response) {
            var json = $.parseJSON(response);
            // results should be null if the page 404s or the domain doesn't work
            if (json.query.results) { 
                var indx = response.indexOf("cover-image");
                var searcharea = response.substring(indx, indx + 150);
                var re = new RegExp('\"src\":\"(https:\/\/[^\"]+)\"');
                var matchObj = searcharea.match(re);
                var url = matchObj[1];
                url = url.substring(0, url.length-3) + "25";

                localStorage.setItem("urlIcon_" + fullyQualifiedURL, url);
                container.prepend("<img class=\"app-icon\" src=\"" + url + "\" width=\"25\" />");
                dfd.resolve(true);
            } else {
                dfd.reject(false);
            }
        })
        .fail(function() {
            dfd.reject('failed');
        });
    }
    return dfd.promise();
}

var addLinks = function() {
    // tried to get 
    //var noapp = false;
    console.log("running addLinks");
    $("#app-install-list > .list-group-item:not(.active)").each(function() {
        var xthis = $(this);
        //xthis.hover(function() {
        //xthis.unbind('mouseenter mouseleave');
        isURLReal(xthis.attr('href'), xthis).fail(function(){
            if (localStorage && !localStorage.getItem('urlReal_' + xthis.attr('href'))) {
                localStorage.setItem('urlReal_' + xthis.attr('href'), "false");
            }

            xthis.text(xthis.text() + ' (' + xthis.attr('href').substring(46) + ')');
            xthis.removeAttr('href');
            xthis.addClass('noclick');
            $("#app-install-list-system").append(xthis.clone());
            xthis.remove();
        }).done(function(){
            if (localStorage && !localStorage.getItem('urlReal_' + xthis.attr('href'))) {
                localStorage.setItem('urlReal_' + xthis.attr('href'), "true");
            }
        });
    //});
    });
};

$(document).ready(function() {
    //var alreadyRan = false;
    
    document.title = "Monitordroid - App List";
    var loadApplistCallback = function(result) {
        if (result !== "") {
            $('#app-install-list').append(result);
            $("#txtHint").css("display", "none");
            addLinks();
        }
        else {
            $("#txtHint").css("display", "none");
            $("#no-data-warning").css("display", "block");
        }
    };

    handleCallback("getapps", "applistdata.php", loadApplistCallback, "<?php echo $rName ?>", "<?php echo $rowId ?>");

    if (Cookies.get('options_autoRefreshPages') == "true") {
        var updateIntervalID = setInterval(updatePage, Number(Cookies.get('options_autoRefreshFreq') ? Cookies.get('options_autoRefreshFreq') : 1) * 1000 * 60);
        tabsUnloadData["applistx.php"] = function(){
            clearInterval(updateIntervalID);
        };
    }

    $("#update_btn").bind("click", updatePage);

});
</script>
<span id="load-buttons">
    <div class="form-inline">
        <button class="btn btn-success" id="update_btn">Update</button>
    </div>
</span>
<div class="alert alert-danger" role="alert" id="no-data-warning">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Warning:</span>
  <strong>Oops</strong> looks like we couldn't find any data for this category. Please click <em>Update</em> above.
</div>
<div id="txtHint">
    <div class="loading-msg">Loading app list...</div>
    <br/>
    <div class="spinner"></div>
</div>
<div class="row">
    <div class="alert alert-danger" role="alert" id="device-error">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
      <span class="sr-only">Error:</span>
      <strong>Oh No!</strong> <span id="device-error-msg"></span>
    </div>
    <div id="applist-google-play" class="col-xs-12">
        <div class="list-group" id="app-install-list">
            <a class="list-group-item active noclick">
                <h4 class="list-group-item-heading">Google Play Apps</h4>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div id="applist-system" class="col-xs-12">
        <div class="list-group">
            <a class="list-group-item active" data-toggle="collapse" href="#app-install-list-system" aria-expanded="false" aria-controls="app-install-list-system">
                <h4 class="list-group-item-heading">Other Apps<small style="float:right">Click to Expand</small></h4>
            </a>
            <div id="app-install-list-system" class="collapse">
            </div>
        </div>
    </div>
</div>
