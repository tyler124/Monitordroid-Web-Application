<?php 
    if (!isset($_SESSION)) { session_start(); }
    if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
        header("location:index.php");
        exit();
    }

    $rName = $_SESSION["registration"];
    $rowId = $_SESSION["rowid"];
?>
<span id="bhist_tab_elcont">
<style type="text/css">
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
#txtMsgs {
	margin-top: -14px;
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
.filter-highlight { background-color: yellow; }
#loadMore {
	display: none;
}
#txtMsgs > div > a {
	word-wrap: break-word;
}
#no-data-warning {
    display: none;
}
</style>

<script type="text/javascript">
$("#bhist_tab_elcont").unload(function() {
	$(window).unbind("scroll");
	$("#msg-search-keyword").unbind("keyup");
});
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

var currMsgNum;
var scrollUpdateEnabled;

//if we keep setting it persistently (i.e. for page refreshes) then...
//   php if (!isset($_SESSION["currMsgNum"])) { echo "1"; } else { echo $_SESSION["currMsgNum"]; } >;
var handleScroll;
var dateTimeLastUpdated = null;
var autoLoadMessages = (Cookies.get("options_alwaysAutoLoad")==="true") ? true : false;
console.log("autoloadmessages", autoLoadMessages);

var stageTwo = function(){
    var loadedCallback = function(result) {
        if (currMsgNum > 1) {
            console.log("scrolling window" + document.body.scrollTop);
            $('html, body').scrollTop(getDocHeight() - 250);

            console.log("scroll position is now " + document.body.scrollTop);
        }
        setTimeout(function(){scrollUpdateEnabled = true;}, 150);
        

        if (!autoLoadMessages) {
          $("#explicit-load-more").css("display", "block");
        }

		$('#txtMsgs').html(result);
        $("#update_btn").prop("disabled", false);
		$("#txtHint").css("display", "none");
		$("#loadMore").css("display", "none");
		updateFilter();
    };

    // will poll server and call loadedCallback with result after data has been loaded
    // will also not show textHint if currMsgNum is greater than one, and thus we are
    // loading more browser history
    handleCallback("getbrowserhistory", "browserhistorydata.php", loadedCallback,
                   "<?php echo $rName ?>", "<?php echo $rowId ?>", (currMsgNum > 1) ? true : false);
    if (currMsgNum > 1) {
        $("#loadMore").css("display", "block");
    }

};
var getTextNodesIn = function(el) {
    return $(el).find(":not(iframe)").addBack().contents().filter(function() {
        return this.nodeType == 3;
    });
};
function updateFilter() {
    if ((globalQuery === "") &&
    	$("#msg-search-keyword").val() === "") {
    	return;
    }
    var tomatch = "";
    if ($("#msg-filter-go").hasClass("active")) {
    	tomatch = $("#msg-search-keyword").val();
    }
		$('a.list-group-item:not(:Contains(' + tomatch + ')' + ':Contains(' + globalQuery + '))').addClass("xhide");
	return;
}
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

function updatePageNum() {
	dateTimeLastUpdated = Date.now();
	sendGCMRefreshMessage("getbrowserhistory-" + currMsgNum, "<?php echo $rName?>", stageTwo);
}

var scaleScrollPane = function() {
	var winHeight = $(window).height();
	var subtract = 210;

	if ($(window).width() < 354) {
		$("#msg-search-keyword").css('width', '220px');
	}
	else {
		$("#msg-search-keyword").css('width', '250px');
	}
	if ($(window).width() < 768) {
		subtract = 220;
	}
};

$(window).resize(scaleScrollPane);

function getDocHeight() { // more accurate $(document).height() from  James Padolsey
    /*var D = document;
    return Math.max(
        D.body.scrollHeight, D.documentElement.scrollHeight,
        D.body.offsetHeight, D.documentElement.offsetHeight,
        D.body.clientHeight, D.documentElement.clientHeight
    );*/
    console.log("scroll calling getDocHeight");
    var D = document;
    return Math.max(
        D.body.scrollHeight, D.documentElement.scrollHeight,
        D.body.offsetHeight, D.documentElement.offsetHeight,
        D.body.clientHeight, D.documentElement.clientHeight
    );

    //return $(document).height();//-$(window).height();
}

var loadMoreInit = function() {
    scrollUpdateEnabled = false;
    $("#loadMore").css("display", "block");
    currMsgNum += 1;
    localStorage.setItem("currentBrowserHistoryPagination", currMsgNum);
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
var updateProc = function() {
	currMsgNum = 1;
    localStorage.setItem("currentBrowserHistoryPagination", currMsgNum);
    $("#update_btn").prop("disabled", true);
    $("#no-data-warning").css("display", "none");
    updatePageNum();

    $('html, body').animate({
        scrollTop: 0
    }, 'fast');

};

$(document).ready(function() {
    var n = localStorage.getItem("currentBrowserHistoryPagination"); //start it at the last value on each page load
    currMsgNum = n ? Number(n) : 1;

	scaleScrollPane();
    scrollUpdateEnabled = true;
<?php if(!isset($_COOKIE["options_alwaysAutoLoad"]) || ($_COOKIE["options_alwaysAutoLoad"] != "true")) { ?>
	$("#explicit-load-more").click(function(){
		$("#explicit-load-more").css("display", "none");		        
        $(window).bind('scroll', handleScroll);
        $('html, body').animate({
            scrollTop: getDocHeight()
        }, 'fast');
        autoLoadMessages = true;
        loadMoreInit();
	});
<?php } ?>
	document.title = "Monitordroid - Browser History";
	globalQuery = "";

	$("#update_btn").click(function(){
		updateProc();
	});

    
	// if we just loaded wait a minute
	var loadBrowserHistCallback = function(result) {
        if (result === "") {
            $("#no-data-warning").css("display", "block");
        }
        else {
			$('#txtMsgs').html(result);
			if (!autoLoadMessages || autoLoadMessages == "false") {
				$('#explicit-load-more').css("display", "block");
			}
        }
		$("#txtHint").css("display", "none");
	};

	//handleDelay("lastRefreshgetbrowserhistory", loadBrowserHistCallback,  8000);

    handleCallback("getbrowserhistory", "browserhistorydata.php", loadBrowserHistCallback, "<?php echo $rName ?>", "<?php echo $rowId ?>");


	if (Cookies.get('options_autoRefreshPages') == "true") {
		var updateIntervalID = setInterval(updateProc, Number(Cookies.get('options_autoRefreshFreq') ? Cookies.get('options_autoRefreshFreq') : 1) * 1000 * 60);
        console.log("set interval", Number(Cookies.get('options_autoRefreshFreq')));

		tabsUnloadData["browserhistoryx.php"] = function(){
			console.log("unloading browser history");
		    clearInterval(updateIntervalID);
		};
	}
	$("#msg-filter-go").bind("click", msg_filter_active_click);
	$("#msg-search-keyword").bind("keyup", msg_filter_type_keyup);

	if (autoLoadMessages) {
        $(window).bind('scroll', handleScroll);
	}

});
</script>
</head>
<div id="test"></div>
<span id="load-buttons">
	<div class="form-inline">
		<div class="form-group">
			<button class="btn btn-success" id="update_btn">Update</button>
		    <div class="input-group filter">
		      <label class="sr-only" for="msg-search-keyword">Search Browser History For</label>
		      <form action="javascript:void(0);" >
			      <input type="text" id="msg-search-keyword" class="form-control" placeholder="Find..." style="width: 250px;">
			      <span class="input-group-btn">
			        <button class="btn btn-default active" type="button" id="msg-filter-go"><span class="glyphicon glyphicon-search"></span></button>
			      </span>
			  </form>
			</div><!-- /input-group -->
		</div><!-- /form-group -->
	</div><!-- /form-inline -->
</span>
<span id="scroll-pane">
	<div id="txtHint">
		<div class="loading-msg">Loading browser history...</div>
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
	<div id="txtMsgs" style="margin-top: 2px"></div>
	<div id="loadMore">
		<div class="loading-msg">Loading more browser history...</div>
		<div class="spinner"></div>
	</div>
	<?php if(!isset($_COOKIE["options_alwaysAutoLoad"]) || $_COOKIE["options_alwaysAutoLoad"] == "false") { ?>
	<div id="explicit-load-more" class="alert alert-info" role="alert"><span class="caret"></span> Load more browser history <span class="caret"></span></div>
	<?php } ?>
</span>
</span>
