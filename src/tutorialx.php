<?php if(!isset($_SESSION)) { session_start(); } ?>
<style type="text/css">
.flash{

	-moz-animation: flash 1s ease-out;
	-moz-animation-iteration-count: 1;

	-webkit-animation: flash 1s ease-out;
	-webkit-animation-iteration-count: 1;

	-ms-animation: flash 1s ease-out;
	-ms-animation-iteration-count: 1;

}
@-webkit-keyframes flash {
		0% { background-color:none;}
		50% { background-color:#fbf8b2;}
		100% {background-color:none;}
}

@-moz-keyframes flash {
		0% { background-color:none;}
		50% { background-color:#fbf8b2;}
		100% {background-color:none;}
}

@-ms-keyframes flash {
		0% { background-color:none;}
		50% { background-color:#fbf8b2;}
		100% {background-color:none;}
}
.popover {
	width: 300px;
	z-index: 12000;
}
.end-tutorial {
	margin-right: 5px;
}
#prodTabs > li:nth-child(4) > div.popover{
		width: 250px; /* Max Width of the popover (depending on the container!)*/
}
</style>
<script type="text/javascript">
var fileListLoaded;
fileListLoaded = function() {
	//console.log(settings.status);
	/*if (settings.url != "viewfilesdata.php") {
		console.log("not right file");
	}*/
	if ($("#txtHint").css("display") != "none") {
		console.log("waiting for file list to load");
		setTimeout(fileListLoaded, 150);
		//$(document).ajaxComplete(fileListLoaded);
		//return false;
	}
	else {
		console.log("file list loaded");
		$("#update_btn").popover("destroy");
		var xcontent = '<p>You can type in a file path here, and click on Go to update the file list.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-9">Next &#187;</button>';
		$("#path-display").popover(
			{
				html : true, 
				content: xcontent,
				title: "File System",
				placement: "bottom",
				trigger: "manual"
			}
		).popover('show');
		$("#next-btn-9").one("click", function() {
			$("#path-display").popover("destroy");
			var xcontent = '<p>This is a handy shortcut to your photos. Sometimes you will have to go through a few more folders to find them; this varies by phone.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-10">Next &#187;</button>';
			$("#load-buttons > div > div:nth-child(4) > div > button.btn.btn-success.goto").popover(
				{
					html : true, 
					content: xcontent,
					title: "Photos",
					placement: "bottom",
					trigger: "manual"
				}
			).popover('show');
			$("#next-btn-10").one("click", function() {
				$("#load-buttons > div > div:nth-child(4) > div > button.btn.btn-success.goto").popover("destroy");
				var xcontent = '<p>You can see a list of your files and folders here. If you are not in your root directory (signified by "/"), you can go to the parent folder (up one level) by clicking the top item on this list.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-11">Next &#187;</button>';
				$("#filesTable > thead > tr > th:nth-child(1)").popover(
					{
						container: 'body',
						html : true, 
						content: xcontent,
						title: "File List",
						placement: "auto bottom",
						trigger: "manual"
					}
				).popover('show');
				$("#next-btn-11").one("click", function() {
					$("#filesTable > thead > tr > th:nth-child(1)").popover("destroy");
					var xcontent = '<p>On every tab, you can sort contents by clicking on the column titles. Clicking here will sort by filename alphabetically.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-12">Next &#187;</button>';
					$("#filesTable > thead > tr > th:nth-child(2)").popover(
						{
							html : true, 
							container: "body",
							content: xcontent,
							title: "Sorting",
							placement: "bottom",
							trigger: "manual"
						}
					).popover('show');
					$("#next-btn-12").one("click", function() {
						$("#filesTable > thead > tr > th:nth-child(2)").popover("destroy");
						var xcontent = '<p>If you ever have any questions, comments or suggestions -- or would like to report a bug -- please click on this Feedback button, or email us at <a href="mailto:help@monitordroid.com">help@monitordroid.com</a>. Your feedback helps us build a better product for you.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-13">Next &#187;</button>';
						$("#feedback-launch").popover(
							{
								html : true, 
								content: xcontent,
								title: "Feedback",
								placement: "auto left",
								trigger: "manual"
							}
						).popover('show');
						$("#next-btn-13").one("click", function() {
							$("#feedback-launch").popover("destroy");
							var xcontent = '<p>That concludes our tutorial! There are many more easy-to-use features -- we encourage you to explore our webapp. Thank you for choosing Monitordroid!</p><button class="btn btn-default end-tutorial">End Tutorial</button>';
							$("body > div:nth-child(2) > nav > div > div:nth-child(3)").popover(
								{
									html : true, 
									content: xcontent,
									placement: "bottom",
									trigger: "manual"
								}
							).popover('show');
						});
					});
				});
			});
		});
	}
};
var files_tab_tut = function() {
	setTimeout(function(){$.when($.ajax("viewfilesx.php")).then( function() {
		$("#more-tab-link").popover("destroy");
		$("#view_files_tab").popover("destroy");
		$("#update_btn").popover(
			{
				html : false, 
				container: 'body',
				content: 'Press Refresh now to grab a list of the files on your device.',
				placement: "auto left",
				trigger: "manual"
			}
		).popover('show');
		$("#update_btn").click(function(){
			console.log("update btn clicked");
			setTimeout(fileListLoaded, 150);
		});
	});}, 150);
};
var after_load_incoming_outgoing = function() {
	$("#update_btn").popover('destroy');
	$.when( $.ajax( "smsdata.php" ) ).done(incoming_outgoing_tut);
};
var incoming_outgoing_tut = function() {
	$("#update_btn").unbind('click', after_load_incoming_outgoing);
	$("#next-btn-6").unbind('click');

	$("#update_btn").popover('destroy');
	var xcontent = '<p>View only incoming or outgoing messages by clicking on one of these buttons.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-7">Next &#187;</button>';
	$("#select-inbox").popover(
		{
			html : true, 
			content: xcontent,
			title: "Viewing Inbox/Outbox",
			placement: "bottom",
			trigger: "manual"
		}
	).popover('show');
	var select_box_fun = function() {
		$('#select-inbox').popover('destroy');
		var xcontent = '<p>To look for messages from a specific number or search for keywords, press filter and type them here.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-8">Next &#187;</button>';
		$("#msg-search-keyword").popover(
			{
				//container: 'body',
				html : true, 
				content: xcontent,
				title: "Filtering Messages",
				placement: "bottom",
				trigger: "manual"
		}).popover('show');
		$("#next-btn-8").one("click", function() {
			$("#msg-search-keyword").popover("destroy");
			var xcontent;
			console.log($("#collapsed > li > #view_files_tab").length);
			if ($("#collapsed > li > #view_files_tab").length === 0) {
				xcontent = '<p>When ready, click on the View Files & Photos tab.</p>';
				$("#view_files_tab").popover(
					{
						//container: 'body',
						html : true, 
						content: xcontent,
						title: "View Files/Photos",
						placement: "bottom",
						trigger: "manual"
					}
				).popover('show');
				$("#view_files_tab").one("click", files_tab_tut);
			}
			else {
				xcontent = '<p>When ready, click on More and then on the View Files & Photos tab.</p>';
				$("#more-tab-link").popover(
					{
						//container: 'body',
						html : true, 
						content: xcontent,
						title: "View Files/Photos",
						placement: "auto left",
						trigger: "manual"
					}
				).popover('show');
				$("#view_files_tab").one("click", files_tab_tut);
			}
			/*$("#call_log_tab").click(function(){
				;
			})*/
		});
	};
	$('#next-btn-7').one('click', select_box_fun);
};
var sms_tab_tut = function() {
	setTimeout(function(){$.when($.ajax("readsmsx.php")).then( function() {
		var xcontent = '<p>You can press update at any time to retrieve your text messages.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-6">Next &#187;</button>';
		$("#update_btn").popover({
			container: 'body',
			html : true, 
			content: xcontent,
			title: "Tabs",
			placement: "right",
			trigger: "manual"
		}).popover('show');
		
		$("#update_btn").bind("click", after_load_incoming_outgoing);
		$("#next-btn-6").bind("click", incoming_outgoing_tut);
	});}, 100);
	console.log("sms_tab_tut");
	$('#sms_tab').popover('destroy');
};
$(document).ready(function(){
	console.log("loading tutorial");
	disableCollapse($("#audio-panel"));
	disableCollapse($("#tools-panel"));
	$(document).on('click', "button.end-tutorial", function() {
	    reloadOnThisTab();       
	});

	var xcontent = '<p>Welcome to the Tutorial. Press here to view and cycle through your devices.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-0">Next &#187;</button>';
	$("#device-choice-dropdown").popover(
	    {
	        //container: 'body',
	        html : true, 
	        content: xcontent,
	        title: "View Devices",
	        placement: "right",
	        trigger: "manual"
	}).popover('show');
	$("#next-btn-0").one("click", function() {
	    $("#device-choice-dropdown").popover("destroy");
	    var xcontent = '<p>You\'re on the home tab. We\'ll go to other tabs later</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-1">Next &#187;</button>';
	    $("#home_tab").popover(
	        {
	            //container: 'body',
	            html : true, 
	            content: xcontent,
	            title: "Home Tab",
	            placement: "right",
	            trigger: "manual"
	    }).popover('show');
	    $("#next-btn-1").one("click", function() {
			$('#home_tab').popover('destroy');
			xcontent = '<p>Press this button now to see info about your device.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-2">Next &#187;</button>';
			$("#device-info-hook").popover({
				//container: 'body',
				html : true, 
				content: xcontent,
				title: "Device Info",
				placement: "auto left",
				trigger: "manual"
			}).popover('show');
			$('#next-btn-2').one('click', function() {
				$('#device-info-hook').popover('destroy');
				xcontent = '<p>There are two volume controls. The set with headphone icons is for your device\'s media volume. The other set with telephone icon is for your ringer volume.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-3">Next &#187;</button>';
				$("#audio-panel > div.panel-heading").popover({
					container: 'body',
					html : true, 
					content: xcontent,
					title: "Volume",
					placement: "left",
					trigger: "manual"
				}).popover('show');
				$('#next-btn-3').one('click', function() {
					$('#audio-panel > div.panel-heading').popover('destroy');
					xcontent = '<p>When turned on, autolocate updates your device\'s location every 10 minute (the duration between updates can be changed by going to settings on top right). Note: Manually updating the location will automatically turn off autolocate.</p><button class="btn btn-default end-tutorial">End Tutorial</button><button class="btn btn-primary" id="next-btn-5">Next &#187;</button>';
					$("#startlocate").popover({
						container: 'body',
						html : true, 
						content: xcontent,
						title: "Autolocate",
						placement: "auto left",
						trigger: "manual"
					}).popover('show');
					$('#next-btn-5').click(function() {
						$('#startlocate').popover('destroy');
						$('#sms_tab').popover(
							{
								placement: 'bottom',
								title: 'Text Messages',
								content:'Click here on text messages.',
								trigger: 'manual'
							}
						).popover('show');
						$('#sms_tab').one("click", sms_tab_tut);
					});
				});
			});
		});
	});
});
</script>