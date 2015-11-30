<?php session_start(); ?>
<span id="calls_tab_elcont">
<style type="text/css">
#success-header {
    display: none;
    font-size: 18pt;
    font-weight: bolder;
}
#calls-container > tr > td:nth-child(1) {
    font-style: italic;
}
#txtHint {
    margin-bottom: 4px;
}
.error-msg {
    display: none;
    font-size: 12pt;
    text-align: center;
    font-weight: bold;
    color: red;
}
#explicit-load-more {
    display: none;
    text-align: center;
    width: 100%;
    margin-bottom: 5px;
}
#explicit-load-more:hover {
    cursor: pointer;
    background-image: linear-gradient(to bottom, #a4d4ec 0, #9acfea 100%);
}
.xhide {
    display: none;
}
.xlowfocus {
    opacity: 0.4;
    color: gray;
}
#load_more_btn {
    margin-top: 10px;
}
#explicit-load-more {
    display: none;
}
.filter-highlight { background-color: yellow; }
#loadMore {
    display: none;
}
table {
    margin-bottom: 0;
}
#txtHint > .warning-msg {
    display: none;
}
#calls-container > tr > td:nth-child(4)::after {
    content: " sec(s)";
    color: rgba(0,0,0,.6);
}
#no-data-warning {
    display: none;
}
</style>

<?php
    if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
        header("location:index.php");
        exit();
    }
    $rName = $_SESSION["registration"];
    $rowId = $_SESSION["rowid"];
?>
<link rel="stylesheet" type="text/css" href="./lib/bootstrap-sortable/Contents/bootstrap-sortable.css"></style>
<script type="text/javascript" src="./lib/bootstrap-sortable/Scripts/bootstrap-sortable.js"></script>
<script type="text/javascript" src="./lib/bootstrap-sortable/Scripts/moment.min.js"></script>
<link rel="stylesheet" href="./lib/intl-tel-input/css/intlTelInput.css">
<script type="text/javascript" src="./lib/intl-tel-input/js/intlTelInput.min.js"></script>
<script type="text/javascript" src="./lib/bootstrap-3-typeahead/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript" src="./phone_contactsmode.js"></script>
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
</style>
<script type="text/javascript">
var experimentalResolveContacts = (Cookies.get("options_experimentalResolveContacts")!=="false") ? true : false;

var lookupPhoneNumber = function(number, contacts_data) {
    if (number == null) {
        return "";
    }
    else {
        var filteredLookupNum = number.replace(/\D/g,'');

        for (var i = 0; i < contacts_data.length; i++) {
            var filteredContactNum = contacts_data[i].phonenumber.replace(/\D/g,'');
            if (filteredContactNum == filteredLookupNum ||
                filteredContactNum.substring(1) == filteredLookupNum ||
                filteredLookupNum.substring(1) == filteredContactNum) {
                if (contacts_data[i].name === null) {
                    return number;
                }
                else {
                    return contacts_data[i].name;
                }
            }
        }
        return number;
    }
}; 

var scaleUIElements = function() {
    var winHeight = $(window).height();
    var subtract = 210;
    if ($(window).width() < 332) {
        $("#call-search-keyword").css('width', '220px');
    }
    else {
        $("#call-search-keyword").css('width', '250px');
    }
    if ($(window).width() < 359) {
        $("#calllog").css("margin-top", "38px");
    }
    else {
        $("#calllog").css("margin-top", "2px");
    }
};

var generateDisplay = function(json) {
    var date_display_options = {
        weekday: "short", year: "numeric", month: "short",
        day: "numeric", hour: "2-digit", minute: "2-digit"
    };

    var calls_data;
    try {
        calls_data = jQuery.parseJSON( json );
    } catch(e) {
        calls_data = json;
    }

    var calls_display;

    var contacts_data;
    var xhr;

    if ($.isEmptyObject(calls_data)) {
        scaleUIElements();
        autocollapse();
        $("[data-toggle='tooltip']").tooltip();
        $("#txtHint").css("display", "none");
        $("#no-data-warning").css("display", "block");
        return;
    }
    if (calls_data[0] && calls_data[0].contact == null && experimentalResolveContacts) {
        xhr = $.ajax({
            url: "contactsdata_np.php",
            type: "POST", 
            data: { 
              //These are the variables and their relative values
                registration: "<?php echo $rName ?>", 
                rowid: "<?php echo $rowId ?>"
            }
        });
        var toReturn = null;
        xhr.done(function(JSON){
            var contacts_data;
            try {
                contacts_data = jQuery.parseJSON( JSON );
            } catch(e) {
                contacts_data = json;
            }

            //FORMAT FOR calls
            //{\"phoneNumber\":\"0\",\"callDuration\":\"0\",\"callDate\":\"1438000550000\",\"callType\":\"Incoming\"}
            //console.log("initial date format: " + calls_data[0].date);
            for ( var i = 0; i < calls_data.length; i++ ) {
                //var msgDate = new Date(Number(calls_data[i].callDate));
                var dateTxt = moment(Number(calls_data[i].callDate)).format("ddd, MMM D, YYYY, h:mm A");
                calls_display += "<tr>";
                calls_display += "<td>" + calls_data[i].callType + "</td>";
                calls_display += "<td data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"auto bottom\" title=\"" + calls_data[i].phoneNumber + "\">" + lookupPhoneNumber(calls_data[i].phoneNumber, contacts_data) + "</td>";
                //calls_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + msgDate.toLocaleTimeString('en-US', date_display_options) + "</td>";
                calls_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + dateTxt + "</td>";
                calls_display += "<td>" + calls_data[i].callDuration + "</td>";
                calls_display += "</tr>";
            }
            $('#calls-container').html(calls_display);
            $.bootstrapSortable(false, 'reversed'); // initialize sorter
            setTimeout(scaleUIElements, 15);
            scaleUIElements();
            autocollapse();
            $("[data-toggle='tooltip']").tooltip();
            $("#txtHint").css("display", "none");
            $("#calllog").css('display', 'block');
        });
    }
    else {
        //console.log("initial date format: " + calls_data[0].date);
        for ( var i = 0; i < calls_data.length; i++ ) {
            //var msgDate = new Date(Number(calls_data[i].callDate));
            var dateTxt = moment(Number(calls_data[i].callDate)).format("ddd, MMM D, YYYY, h:mm A");
            calls_display += "<tr>";
            calls_display += "<td>" + calls_data[i].callType + "</td>";
            calls_display += "<td data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"auto bottom\" title=\"" + calls_data[i].phoneNumber + "\">" + calls_data[i].phoneNumber + "</td>";
            //calls_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + msgDate.toLocaleTimeString('en-US', date_display_options) + "</td>";
            calls_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + dateTxt + "</td>";
            calls_display += "<td>" + calls_data[i].callDuration + "</td>";
            calls_display += "</tr>";
        }
        $('#calls-container').html(calls_display);
        $.bootstrapSortable(false, 'reversed'); // initialize sorter
        setTimeout(scaleUIElements, 15);
        scaleUIElements();
        autocollapse();
        $("[data-toggle='tooltip']").tooltip();
        $("#txtHint").css("display", "none");
        $("#calllog").css('display', 'block');
    }
    
};

window.globalQuery = "";
jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
    return function( elem ) {
        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});
jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
};
(function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    };
})(jQuery);

var scrollUpdateEnabled;
var currMsgNum = 1; //start it at 1 on each page load
//if we keep setting it persistently (i.e. for page refreshes) then...
//   php if (!isset($_SESSION["currMsgNum"])) { echo "1"; } else { echo $_SESSION["currMsgNum"]; } >;
var handleScroll;
var dateTimeLastUpdated = null;

var stageTwo = function(){

    var loadedCallback = function(result) {
        window.scrollTo(0, document.body.scrollTop - 250);
        //$(window).bind('scroll', handleScroll);
        scrollUpdateEnabled = true;
        generateDisplay(result);
        $("#loadMore").css("display", "none");
        updateFilter();
        $("#update_btn").prop("disabled", false);
    };
    handleCallback("calls", "calllogdata.php", loadedCallback, '<?php echo $rName ?>', '<?php echo $rowId ?>');
    //poll("calllogdata.php", "<?php echo $rName ?>", "<?php echo $rowId ?>", date, loadedCallback, "calls");

};
var getTextNodesIn = function(el) {
    return $(el).find(":not(iframe)").addBack().contents().filter(function() {
        return this.nodeType == 3;
    });
};
var updateFilter = function() {
    if ((globalQuery === "") &&
        $("#call-search-keyword").val() === "") {
        return;
    }
    var tomatch = "";
    if ($("#call-filter-go").hasClass("active")) {
        tomatch = $("#call-search-keyword").val();
    }
    $('#calls-container > tr:not(:Contains(' + tomatch + ')' + ':Contains(' + globalQuery + '))').addClass("xhide");
    return;
};
var removeFilter = function() {
    $(".xhide").removeClass("xhide");
};
var timer;
var call_filter_type_keyup = function() {
    clearTimeout(timer);
    timer = setTimeout(function (event) {
        removeFilter();
        updateFilter();
    }, 200);
};
var call_filter_active_click = function() {
    $("#call-filter-go").removeClass("active");
    $("#call-search-keyword").unbind("keyup");
    removeFilter();
    if (globalQuery !== "") {
        updateFilter();
    }
    $("#call-filter-go").unbind("click", call_filter_active_click);
    $("#call-filter-go").bind("click", call_filter_inactive_click);
};
var call_filter_inactive_click = function() {
    $("#call-filter-go").addClass("active");
    updateFilter();
    $("#call-search-keyword").bind("keyup", call_filter_type_keyup);
    $("#call-filter-go").unbind("click", call_filter_inactive_click);
    $("#call-filter-go").bind("click", call_filter_active_click);
};

var updatePage = function() {
    $("#txtHint").css("display", "block");
    $("#no-data-warning").css("display", "none");
    $("#txtHint .warning-msg").css("display", "block");
    $("#update_btn").prop("disabled", true);
    dateTimeLastUpdated = Date.now();
    sendGCMRefreshMessage("calls", "<?php echo $rName?>", function(result){ stageTwo(); });
};

var updateProc = function() {
    window.scrollTo(0, 0);
    $("#txtHint").css("display", "block");
    $("#txtHint > .warning-msg").css("display", "block");
    updatePage();
    $("#update_btn").prop("disabled", true);
};

$(window).resize(scaleUIElements);

var calls_skip_validation;
var contactsModeActive;

$(document).ready(function() {
    var scriptsToUnload = {"./lib/bootstrap-sortable/Scripts/bootstrap-sortable.js": true, "./lib/bootstrap-sortable/Scripts/moment.min.js": true};
    var pageUnloadProc = function() {
        try {
            $("#calls_tab_elcont").html(
                $("#calls_tab_elcont").html().replace(/<script\s+src="([^"]+)"><\/script>/gi, function(str, file) {
                    if(scriptsToUnload[file]) {
                        return "";
                    } else {
                        scriptsToUnload[file] = true;
                        return str;
                    }
                })
            );
        }
        catch (err) {
            return false;
        }
    };
    if (Cookies.get('options_autoRefreshPages') == "true") {
        var updateIntervalID = setInterval(updateProc, (Number(Cookies.get('options_autoRefreshFreq') ? Cookies.get('options_autoRefreshFreq') : 1) * 1000 * 60));
        tabsUnloadData["calllogx.php"] = function(){
            console.log("unloading callsx");
            clearInterval(updateIntervalID);
            pageUnloadProc();
        };
    }
    else {
        tabsUnloadData["calllogx.php"] = function(){
            pageUnloadProc();
        };
    }

    //contactsModeActive = false;
    $("#phone_form > div.modal-footer > button.btn.btn-default").click(function(){
        $("#call-go").popover("destroy");
    });

    initializeContactsMode($("#callModal"), $("#phone_form"));
    calls_skip_validation = false;

    $('#phone_form').submit( function(event){
        event.preventDefault();

        phoneNumEl = $("#call-num");
        var phoneNumber;
        if (!calls_skip_validation && phoneNumEl.intlTelInput("isValidNumber") === false) {
            var xcontent = 'Please enter a valid number; you may select a country by clicking on the flag.<br/><button class="btn btn-primary" id="call-anyway">Send Anyway</button>';
            $("#call-go").popover(
                {
                    //container: "body",
                    html : true, 
                    content: xcontent,
                    title: "Warning: Invalid Number",
                    placement: "bottom",
                    trigger: "manual"
                }
            ).popover('show');
            $("#call-anyway").click(function(){
                calls_skip_validation = true;
                $('#phone_form').submit();
            });
            return false;
        }
        else {
            phoneNumEl.popover('destroy');
        }
        if (calls_skip_validation) {
            phoneNumber = phoneNumEl.val();
        }
        else {
            phoneNumber = phoneNumEl.intlTelInput("getNumber");
        }
        //phoneNumber = phoneNumber.replace(/\D/g,'');

        var calldata = "call(" + phoneNumber + ")";
        $.ajax({
            url: "send_message.php",
            type: 'GET',
            data: {
                regId: '<?php echo $_SESSION["registration"] ?>',
                message: calldata
            },
            success: function(){ $("#call-num").val(""); }
        });
        calls_skip_validation = false;
        $("#callModal").modal('hide');
    });
    
    document.title = "Monitordroid - Call Log";
    globalQuery = "";
    $("#update_btn").click(updateProc);

    // if we just loaded wait a minute
    /*var loadCallsCallback = function() {
        $.ajax({
            url: "calllogdata.php",
            type: "POST",
            data: {
                registration: "<?php echo $rName ?>",
                rowid: "<?php echo $rowId ?>"
            },
            success:function(result){
                generateDisplay(result);
            }
        });
    };*/
    $("#calllog").css('display', 'none');
    //handleDelay("lastRefreshcalls", loadCallsCallback, 2.5 * 1000);
    handleCallback("calls", "calllogdata.php", generateDisplay, "<?php echo $rName ?>", "<?php echo $rowId ?>");


    $("#select-incoming").click(function(){
        if (!$(this).hasClass("active")) {
            $("#select-both").removeClass("active");
            $("#select-outgoing").removeClass("active");
            $("#select-incoming").addClass("active");
            globalQuery = "incoming";
            removeFilter();
            updateFilter();
        }
    });
    $("#select-outgoing").click(function(){
        if (!$(this).hasClass("active")) {
            $("#select-both").removeClass("active");
            $("#select-incoming").removeClass("active");
            $("#select-outgoing").addClass("active");
            globalQuery = "outgoing";
            removeFilter();
            updateFilter();
        }
    });
    $("#select-both").click(function(){
        if ($(this).hasClass("active")) {
            return;
        }
        else {
            $("#select-both").addClass("active");
            $("#select-outgoing").removeClass("active");
            $("#select-incoming").removeClass("active");
            globalQuery = "";
            removeFilter();
            if ($("#call-filter-go").hasClass("active")) {
                updateFilter();
            }
        }
    });
    $("#call-filter-go").bind("click", call_filter_active_click);
    $("#call-search-keyword").bind("keyup", call_filter_type_keyup);

});
</script>
<!-- Modal -->
<div class="modal fade" id="callModal" tabindex="-1" role="dialog" aria-labelledby="callModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="callModalLabel">Call Number</h4>
      </div>
      <form role="form" id="phone_form" action="javascript:void(0);" class="<?php echo $_SESSION["name"] ?>" style="display:block" >
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
          <button type="submit" id="call-go" class="<?php echo $_SESSION["name"] ?> btn btn-primary">Call Number</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="test"></div>
<span id="load-buttons">
    <div class="form-inline">
        <div class="form-group">
            <button class="btn btn-success btn-no-group" id="update_btn">Update</button>
            <button type="button" class="btn btn-primary btn-no-group" data-toggle="modal" data-target="#callModal"><span class="glyphicon glyphicon-earphone"></span> Call</button>
            <div class="btn-group" style="position:relative">
                <button class="btn btn-default active" type="button" id="select-both">All</button>
                <button class="btn btn-default" type="button" id="select-incoming">Incoming<span class="hidden-xs hidden-sm"> Only</span></button>
                <button class="btn btn-default" type="button" id="select-outgoing">Outgoing<span class="hidden-xs hidden-sm"> Only</span></button>
            </div>
            <div class="input-group filter">
              <label class="sr-only" for="call-search-keyword">Search Messages For</label>
              <form action="javascript:void(0);" style="width: 292px">
                <div class="input-group">
                  <input type="text" id="call-search-keyword" class="form-control" placeholder="Find..." style="width: 250px;">
                  <span class="input-group-btn">
                    <button class="btn btn-default active" type="button" id="call-filter-go"><span class="glyphicon glyphicon-search"></span></button>
                  </span>
                </div>
              </form>
            </div><!-- /input-group -->

        </div><!-- /form-group -->
    </div><!-- /form-inline -->
</span>
<span id="scroll-pane">
    <div id="txtHint">
        <div class="loading-msg">Loading calls...</div>
        <br/>
        <div class="spinner"></div>
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
    <div id="calllog">
        <table class="table table-striped sortable" style="table-layout: fixed">
            <thead class="calls">
                <tr>
                    <th class="calls sort">Type</th>
                    <th class="calls sort">Contact</th>
                    <th class="calls sort">Date</th>
                    <th class="calls sort">Duration</th>
                </tr>
            </thead>
            <tbody class="calls" id="calls-container">
            </tbody>
        </table>
    </div>
</span>
</span>