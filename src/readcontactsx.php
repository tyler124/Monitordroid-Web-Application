<?php session_start();
    if (!isset($_SESSION['registration']) && !isset($_SESSION['rowid'])) {
        header("location:index.php");
        exit();
    }
    $rName = $_SESSION["registration"];
    $rowId = $_SESSION["rowid"];
?>
<span id="contacts_tab_elcont">
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
.xhide {
	display: none;
}
#txtHint > .warning-msg {
	display: none;
}
#no-data-warning {
	display: none;
}
</style>
	
<link rel="stylesheet" type="text/css" href="./lib/bootstrap-sortable/Contents/bootstrap-sortable.css"></style>
<script type="text/javascript" src="./lib/bootstrap-sortable/Scripts/bootstrap-sortable.js"></script>

<script type="text/javascript">
var generateDisplay = function(json) {

    var contacts_data;
    try {
        contacts_data = jQuery.parseJSON( json );
    } catch(e) {
        contacts_data = json;
    }

	var contacts_display;

    if ($.isEmptyObject(contacts_data)) {
        $("#no-data-warning").css("display", "block");
        return "";
    }

	for ( var i = 0; i < contacts_data.length; i++ ) {
		contacts_display += "<tr>";
		contacts_display += "<td>" + contacts_data[i].phonenumber + "</td>";
		if (contacts_data[i].name === null) {
			contacts_display += "<td>" + "unknown" + "</td>";
		}
		else {
			contacts_display += "<td>" + contacts_data[i].name + "</td>";
		}
		contacts_display += "</tr>";
	}
	return contacts_display;
};
var dateTimeLastUpdated = null;

function stageTwo(){

    var loadedCallback = function(result) {
        $('#contacts-container').html(generateDisplay(result));
        $.bootstrapSortable(false, 'reversed');
        $("#contacts").css("display", "block");
        $("#txtHint").css("display", "none");
        $("#load-buttons").css("display", "block");
        $("#update_btn").prop("disabled", false);
    };
    //poll("contactsdata.php", "<?php echo $rName ?>", "<?php echo $rowId ?>", date, loadedCallback, "contacts");

    handleCallback("contacts", "contactsdata.php", loadedCallback, "<?php echo $rName ?>", "<?php echo $rowId ?>");
};

jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
    return function( elem ) {
        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});
var updateFilter = function() {
	if (($("#case-sensitive").is(':checked'))) {
		$('#contacts-container > tr:not(:contains('+$("#contacts-search-keyword").val()+'))').addClass("xhide");
	}
	else {
		$('#contacts-container > tr:not(:Contains('+$("#contacts-search-keyword").val()+'))').addClass("xhide");

	}
};
var timer;
var removeFilter = function() {
	$(".xhide").removeClass("xhide");
};
var contacts_filter_type_keyup = function() {
	clearTimeout(timer);
    timer = setTimeout(function (event) {
        removeFilter();
		updateFilter();
    }, 200);
};
var contacts_filter_active_click = function() {
	$("#contacts-filter-go").removeClass("active");
	$("#contacts-search-keyword").unbind("keyup");
	removeFilter();
	$("#contacts-filter-go").unbind("click", contacts_filter_active_click);
	$("#case-sensitive").unbind("change");
	$("#contacts-filter-go").bind("click", contacts_filter_inactive_click);
};
var contacts_filter_inactive_click = function() {
	$("#contacts-filter-go").addClass("active");
	updateFilter();
	$("#contacts-search-keyword").bind("keyup", contacts_filter_type_keyup);
	$("#case-sensitive").bind("change", contacts_filter_type_keyup);
	$("#contacts-filter-go").unbind("click");
	$("#contacts-filter-go").bind("click", contacts_filter_active_click);
};

var updateProc = function() {
	$("#txtHint").css("display", "block");
	$("#txtHint > .warning-msg").css("display", "block");
	$("#update_btn").prop("disabled", true);
    var dateTimeLastUpdated = Date.now();
	sendGCMRefreshMessage("contacts", "<?php echo $rName?>", function(result){ stageTwo(); });
};

$(document).ready(function() {
	document.title = "Monitordroid - Contacts";

    $('#contacts').css("display", "none");

    var loadContactsCallback = function(result) {
        $('#explicit-load-more').css("display", "block");
        $("#txtHint").css("display", "none");
        $('#contacts-container').html(generateDisplay(result));
        $.bootstrapSortable(false, 'reversed');
        $('#contacts').css("display", "block");
    };
    handleCallback("contacts", "contactsdata.php", loadContactsCallback, "<?php echo $rName ?>", "<?php echo $rowId ?>");

	$("#update_btn").bind("click", updateProc);
	$("#contacts-filter-go").bind("click", contacts_filter_active_click);
	$("#contacts-search-keyword").bind("keyup", contacts_filter_type_keyup);

    var scriptsToUnload = {"./lib/bootstrap-sortable/Scripts/bootstrap-sortable.js": true};
    var pageUnloadProc = function() {
        $("#contacts_tab_elcont").html(
            $("#contacts_tab_elcont").html().replace(/<script\s+src="([^"]+)"><\/script>/gi, function(str, file) {
                if(scriptsToUnload[file]) {
                    return "";
                } else {
                    scriptsToUnload[file] = true;
                    return str;
                }
            })
        );
    };
	if (Cookies.get('options_autoRefreshPages') == "true") {
		var updateIntervalID = setInterval(updateProc, Number(Cookies.get('options_autoRefreshFreq') ? Cookies.get('options_autoRefreshFreq') : 1) * 1000 * 60);
		tabsUnloadData["readcontactsx.php"] = function(){
			console.log("unloading contacts");
		    clearInterval(updateIntervalID);
		    pageUnloadProc();
		};
	}
	else {
		tabsUnloadData["readcontactsx.php"] = function(){
		    pageUnloadProc();
		};
	}

});
</script>

<div id="test"></div>
<span id="load-buttons">
	<div class="form-inline">
		<button class="btn btn-success" id="update_btn">Update</button>
		<div class="input-group filter">
	      <label class="sr-only" for="contacts-search-keyword">Search Contacts For</label>
	      <form id="contacts-filter" action="javascript:void(0);">
		      <input type="text" id="contacts-search-keyword" class="form-control" placeholder="Find..." style="width: 250px;">
		      <span class="input-group-btn">
		        <button class="btn btn-default active" type="button" id="contacts-filter-go"><span class="glyphicon glyphicon-search"></span></button>
		      </span>
		  </form>
	    </div><!-- /input-group -->
    </div>
</span>
<div class="alert alert-danger" role="alert" id="no-data-warning">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Warning:</span>
  <strong>Oops</strong> looks like we couldn't find any data for this category. Please click <em>Update</em> above.
</div>
<div class="alert alert-danger" role="alert" id="device-error">
  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>
  <strong>Oh No!</strong> <span id="device-error-msg"></span>
</div>
<div id="txtHint"><div class="loading-msg">Loading contacts...</div><br/><div class="spinner"></div><div class="warning-msg">Loading your contacts will take around 8 seconds.</div></div>
<div id="contacts" style="margin-top: 2px">
	<table class="table table-striped sortable">
		<thead>
			<tr>
				<th>Phone Number</th>
				<th>Name</th>
			</tr>
		</thead>
		<tbody id="contacts-container">
		</tbody>
	</table>
</div>
