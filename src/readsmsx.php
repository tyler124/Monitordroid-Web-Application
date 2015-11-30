<?php 
    if (!isset($_SESSION)) { session_start(); }
    if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
        header("location:index.php");
        exit();
    }

    $rName = $_SESSION["registration"];
    $rowId = $_SESSION["rowid"];
?>
<span id="sms_tab_elcont">
<style type="text/css">
#success-header {
    display: none;
    font-size: 18pt;
    font-weight: bolder;
}
#sms-container > tr > td:nth-child(1) {
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
#no-data-warning {
    display: none;
}
</style>

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
    if (number === null || number === undefined) {
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

var generateDisplay = function(json) {
    $('#txtMsgs').css('display', 'none');
    var date_display_options = {
        weekday: "short", year: "numeric", month: "short",
        day: "numeric", hour: "2-digit", minute: "2-digit"
    };

    var sms_data;
    try {
        sms_data = jQuery.parseJSON( json );
    } catch(e) {
        sms_data = json;
    }

    var sms_display;
    //console.log(sms_data);
    if ($.isEmptyObject(sms_data)) {
        $("#txtHint").css("display", "none");
        $('#txtMsgs').css('display', 'none');
        $("#no-data-warning").css("display", "block");
        autocollapse();
        return;
    }
    if (sms_data.hasOwnProperty("message")) {
        $('#sms-container').html(json.message);
        $("#txtHint").css("display", "none");
        $("#txtMsgs").css('display', 'block');
        autocollapse();
        return;
    }

    var contacts_data;
    var xhr;
    if (sms_data[0] && sms_data[0].contact == null && experimentalResolveContacts) {
        xhr = $.ajax({
            url: "contactsdata_np.php",
            type: "POST", 
            data: {
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
                contacts_data = JSON;
                //console.log(contacts_data);
            }

            //console.log("initial date format: " + sms_data[0].date);
            //for ( var i = 0; i < sms_data.length; i++ ) {
            for (var i in sms_data) {
                if (sms_data[i] === null) {
                    continue;
                }
                //var msgDate = new Date(sms_data[i].date);
                var dateTxt = moment(sms_data[i].date).format("ddd, MMM D, YYYY, h:mm A");
                sms_display += "<tr>";
                sms_display += "<td>" + sms_data[i].mailbox + "</td>";
                sms_display += "<td data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"auto bottom\" title=\"" + sms_data[i].phonenumber + "\">" + lookupPhoneNumber(sms_data[i].phonenumber, contacts_data) + "</td>";
                //sms_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + msgDate.toLocaleTimeString('en-US', date_display_options) + "</td>";
                sms_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + dateTxt + "</td>";
                sms_display += "<td>" + sms_data[i].message + "</td>";
                sms_display += "</tr>";
            }
            $('#sms-container').html(sms_display);
            $.bootstrapSortable(false, 'reversed'); // initialize sorter
            
            $("[data-toggle='tooltip']").tooltip();
            $("#txtMsgs").css('display', 'block');
            $("#txtHint").css("display", "none");
            console.log("just hid txthint");
            
            autocollapse();
        });
    }
    else {
        //console.log("initial date format: " + sms_data[0].date);
        for ( var i = 0; i < sms_data.length; i++ ) {
            var dateTxt = moment(sms_data[i].date).format("ddd, MMM D, YYYY, h:mm A");
            sms_display += "<tr>";
            sms_display += "<td>" + sms_data[i].mailbox + "</td>";
            if (sms_data[i].contact && (sms_data[i].contact != "?")) {
                sms_display += "<td data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"auto bottom\" title=\"" + sms_data[i].phonenumber + "\">" + sms_data[i].contact + "</td>";
            }
            else {
                sms_display += "<td data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"auto bottom\" title=\"" + sms_data[i].phonenumber + "\">" + sms_data[i].phonenumber + "</td>";
            }
            sms_display += "<td data-dateformat=\"ddd, MMM D, YYYY, h:mm A\">" + dateTxt + "</td>";
            sms_display += "<td>" + sms_data[i].message + "</td>";
            sms_display += "</tr>";
        }
        $('#sms-container').html(sms_display);
        $.bootstrapSortable(false, 'reversed'); // initialize sorter
        
        $("[data-toggle='tooltip']").tooltip();
        $("#txtMsgs").css('display', 'block');
        $("#txtHint").css("display", "none");
        console.log("just hid txthint");
        
        autocollapse();
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

var autoLoadMessages = (Cookies.get("options_alwaysAutoLoad")==="true") ? true : false;
console.log("autoloadmessages", autoLoadMessages);


var dateTimeLastUpdated = null;
var scrollUpdateEnabled;
var currMsgNum;
var handleScroll;
var stageTwo = function(){

    var loadedCallback = function(result) {
        if (currMsgNum > 1) {
            console.log("scrolling window" + document.body.scrollTop);
            $('html, body').scrollTop(getDocHeight() - 250);
            console.log("scroll position is now " + document.body.scrollTop);
        }
        
        setTimeout(function(){scrollUpdateEnabled = true;}, 150);
        generateDisplay(result);
        $("#loadMore").css("display", "none");
        if (!autoLoadMessages) {
          $("#explicit-load-more").css("display", "block");
        }
        updateFilter();
        $("#update_btn").prop("disabled", false);
    };

    // will poll server and call loadedCallback with result after data has been loaded
    // will also not show textHint if currMsgNum is greater than one, and thus we are
    // loading more messages
    handleCallback("readsms", "smsdata.php", loadedCallback, "<?php echo $rName ?>", "<?php echo $rowId ?>", (currMsgNum > 1) ? true : false);
    if (currMsgNum > 1) {
        $("#loadMore").css("display", "block");
    }

};
var getTextNodesIn = function(el) {
    return $(el).find(":not(iframe)").addBack().contents().filter(function() {
        return this.nodeType == 3;
    });
};
var updateFilter = function() {
    if ((globalQuery == "") &&
        $("#msg-search-keyword").val() === "") {
        return;
    }
    var tomatch = "";
    if ($("#msg-filter-go").hasClass("active")) {
        tomatch = $("#msg-search-keyword").val();
    }
    $('#sms-container > tr:not(:Contains(' + tomatch + ')' + ':Contains(' + globalQuery + '))').addClass("xhide");
    return;
};
var removeFilter = function() {
    $(".xhide").removeClass("xhide");
};
var timer;
var msg_filter_type_keyup = function() {
    clearTimeout(timer);
    timer = setTimeout(function (event) {
        removeFilter();
        updateFilter();
    }, 200);
};
var msg_filter_active_click = function() {
    $("#msg-filter-go").removeClass("active");
    $("#msg-search-keyword").unbind("keyup");
    removeFilter();
    if (globalQuery !== "") {
        updateFilter();
    }
    $("#msg-filter-go").unbind("click", msg_filter_active_click);
    $("#msg-filter-go").bind("click", msg_filter_inactive_click);
};
var msg_filter_inactive_click = function() {
    $("#msg-filter-go").addClass("active");
    updateFilter();
    $("#msg-search-keyword").bind("keyup", msg_filter_type_keyup);
    $("#msg-filter-go").unbind("click", msg_filter_inactive_click);
    $("#msg-filter-go").bind("click", msg_filter_active_click);
};

var updatePageNum = function() {
    $("#update_btn").prop("disabled", true);
    $("#no-data-warning").css("display", "none");
    //var resolveSMSContacts = (Cookies.get("options_resolveSMSContacts")==="true") ? "1" : "0";
    var dateTimeLastUpdated = Date.now();
    sendGCMRefreshMessage("readsms-" + currMsgNum, "<?php echo $rName?>", stageTwo);
};

var updateProc = function() {
    currMsgNum = 1;
    localStorage.setItem("currentReadSMSPagination", currMsgNum);
    //$("#txtHint").css("display", "block");
    $("#update_btn").prop("disabled", true);
    $("#no-data-warning").css("display", "none");

    updatePageNum();
    $('html, body').animate({
        scrollTop: 0
    }, 'fast');
};

var getDocHeight = function() { // more accurate $(document).height() from  James Padolsey
    var D = document;
    return Math.max(
        D.body.scrollHeight, D.documentElement.scrollHeight,
        D.body.offsetHeight, D.documentElement.offsetHeight,
        D.body.clientHeight, D.documentElement.clientHeight
    );
};

var loadMoreInit = function() {
    scrollUpdateEnabled = false;
    $("#loadMore").css("display", "block");
    currMsgNum += 1;
    localStorage.setItem("currentReadSMSPagination", currMsgNum);
    updatePageNum();
    $("#update_btn").prop("disabled", true);
};

var handleScroll = function() {
    theWindow = $(window);
    theBody = $("body");

    if (scrollUpdateEnabled && autoLoadMessages &&
        (theBody.hasScrollBar()) &&
        (Math.ceil(theWindow.scrollTop() + theWindow.height()) >= getDocHeight())) {
        loadMoreInit();
    }
};

var sms_skip_validation;
var contactsModeActive;

$(document).ready(function() {
    scrollUpdateEnabled = true;
    var scriptsToUnload = {"./lib/bootstrap-sortable/Scripts/bootstrap-sortable.js": true, "./lib/bootstrap-sortable/Scripts/moment.min.js": true};
    var pageUnloadProc = function() {
        try {
            $("#sms_tab_elcont").html(
                $("#sms_tab_elcont").html().replace(/<script\s+src="([^"]+)"><\/script>/gi, function(str, file) {
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
        tabsUnloadData["readsmsx.php"] = function(){
            console.log("unloading smsx");
            clearInterval(updateIntervalID);
            pageUnloadProc();
        };
    }
    else {
        tabsUnloadData["readsmsx.php"] = function(){
            pageUnloadProc();
        };
    }

    var n = localStorage.getItem("currentReadSMSPagination"); //start it at the last value on each page load
    currMsgNum = n ? Number(n) : 1;

    $("#sms_form > div.modal-footer > button.btn.btn-default").click(function(){
        $("#sms-go").popover("destroy");
    });

    initializeContactsMode($("#smsModal"), $("#sms_form"));
    sms_skip_validation = false;

    $('#sms_form').submit( function(event){
        event.preventDefault();

        phoneNumEl = $("#sms-num");
        var phoneNumber;
        if (!sms_skip_validation && phoneNumEl.intlTelInput("isValidNumber") === false) {
            var xcontent = 'Please enter a valid number; you may select a country by clicking on the flag.<br/><button class="btn btn-primary" id="sms-send-anyway">Send Anyway</button>';
            $("#sms-go").popover(
                {
                    html : true, 
                    content: xcontent,
                    title: "Warning: Invalid Number",
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

    
    
    if (!autoLoadMessages) {
        $("#explicit-load-more").css("display", "block");
        $("#explicit-load-more").click(function(){
            $("#explicit-load-more").css("display", "none");
            autoLoadMessages = true;
            $(window).bind('scroll', handleScroll);
            $('html, body').animate({
                scrollTop: getDocHeight()
            }, 'fast');
            loadMoreInit();
        });
    }

    document.title = "Monitordroid - Text Messages";
    globalQuery = "";
    $("#update_btn").click(updateProc);
    var loadSMSCallback = function(result) {
        generateDisplay(result);
        if (!autoLoadMessages || autoLoadMessages == "false") {
            $('#explicit-load-more').css("display", "block");
        }
        $("#txtHint").css("display", "none");
    };

    $("#txtMsgs").css('display', 'none');
    //handleDelay("lastRefreshreadsms", loadSMSCallback, 0/*loadTime * 1000*/);
    handleCallback("readsms", "smsdata.php", loadSMSCallback, "<?php echo $rName ?>", "<?php echo $rowId ?>");


    $("#select-inbox").click(function(){
        if (!$(this).hasClass("active")) {
            $("#select-both").removeClass("active");
            $("#select-outbox").removeClass("active");
            $("#select-inbox").addClass("active");
            globalQuery = "Inbox";
            removeFilter();
            updateFilter();
        }
    });
    $("#select-outbox").click(function(){
        if (!$(this).hasClass("active")) {
            $("#select-both").removeClass("active");
            $("#select-inbox").removeClass("active");
            $("#select-outbox").addClass("active");
            globalQuery = "Outbox";
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
            $("#select-outbox").removeClass("active");
            $("#select-inbox").removeClass("active");
            globalQuery = "";
            removeFilter();
            if ($("#msg-filter-go").hasClass("active")) {
                updateFilter();
            }
        }
    });
    $("#msg-filter-go").bind("click", msg_filter_active_click);
    $("#msg-search-keyword").bind("keyup", msg_filter_type_keyup);

    if (autoLoadMessages) {
        $(window).bind('scroll', handleScroll);
    }
});
</script>

<!-- Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="sendSmsModalLabel">Send SMS</h4>
      </div>
      <form role="form" id="sms_form" action="javascript:void(0);" style="display:block" class="<?php echo $_SESSION["name"] ?>">
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
          <button type="submit" id="sms-go" class="<?php echo $_SESSION["name"] ?> btn btn-primary">Send SMS</button>
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
            <button type="button" class="btn btn-primary btn-no-group" data-toggle="modal" data-target="#sendSmsModal">Send SMS</button>
            <div class="btn-group" style="position:relative">
                <button class="btn btn-default active" type="button" id="select-both">Both</button>
                <button class="btn btn-default" type="button" id="select-inbox">Inbox<span class="hidden-xs hidden-sm"> Only</span></button>
                <button class="btn btn-default" type="button" id="select-outbox">Outbox<span class="hidden-xs hidden-sm"> Only</span></button>
            </div>
            <div class="input-group filter">
              <label class="sr-only" for="msg-search-keyword">Search Messages For</label>
              <form action="javascript:void(0);" style="width: 292px">
                <div class="input-group">
                  <input type="text" id="msg-search-keyword" class="form-control" placeholder="Find..." style="width: 250px;">
                  <span class="input-group-btn">
                    <button class="btn btn-default active" type="button" id="msg-filter-go"><span class="glyphicon glyphicon-search"></span></button>
                  </span>
                </div>
              </form>
            </div><!-- /input-group -->

        </div><!-- /form-group -->
    </div><!-- /form-inline -->
</span>
<span id="scroll-pane">
    <div id="txtHint">
        <div class="loading-msg">Loading messages...</div>
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
    <div id="txtMsgs">
        <table class="table table-striped sortable" style="table-layout: fixed">
            <thead class="sms">
                <tr>
                    <th class="sms sort">Mailbox</th>
                    <th class="sms sort">Contact</th>
                    <th class="sms sort">Date</th>
                    <th class="sms sort">Body</th>
                </tr>
            </thead>
            <tbody class="sms" id="sms-container">
            </tbody>
        </table>
    </div>
    <div id="loadMore">
        <div class="loading-msg">Loading more messages...</div>
        <div class="spinner"></div>
    </div>
    <div id="explicit-load-more" class="alert alert-info" role="alert"><span class="caret"></span> Load more messages <span class="caret"></span></div>
</span>
</span>