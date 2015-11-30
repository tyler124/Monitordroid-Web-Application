// md_core_index.js
var universalErrorLog = "";
$("#js-replace").text("we couldn't load something.");
$("#js-replace-2").html("");
$("#err-no-pageload").css("display", "none");
$(document).ajaxError(function(event, jqxhr, settings, thrownError){
  msg = "AJAXError for " + settings.url;
  universalErrorLog += msg;
  $("#indexTxtHint").css("display", "none");
  $("#err-no-pageload").css("display", "block");
  $.ajax({
    method: "POST",
    url: "generate_error.php",
    data: { msg: msg,
            url: window.location.href,
            linenumber: 0,
            column: 0,
            errorObj: "" }
  });
  return false;
});
var refreshAll = function(reg) {
  for ( i = 0; i < msgList.length; i++ ) {
    console.log("sendgcmmsg "+ msgList[i] + " i=" + i);
    sendGCMRefreshMessage(msgList[i], reg);
  }
};
var reloadOnThisTab = function() {
  var activeTab = $('#prodTabs > li.active > a');
  if (activeTab[0] === undefined) {
    activeTab = $("#collapsed > li.active > a");
  }
  location.hash = activeTab.attr('href');
  location.reload(true);
};
var setTabActive = function(current) {
  $("#indexTxtHint").css("display", "block");
  var last = $('#prodTabs > li.active > a');
  lasturl = last.attr('data-link');
  //var current = $(this);

  $('#prodTabs > li.active').removeClass('active');
  $('#collapsed > li.active').removeClass('active');
  var loadurl = current.attr('data-link'),
      targ = current.attr('data-target');
  var setTabActivePart2 = function() {
    $(targ).empty();
    window.location.hash = current.attr('href');
    console.log("now loading new page");
    $.get(loadurl, function(data) {
        $(targ).html(data);
    }).done(function(){
      $("#err-no-pageload").css("display", "none");
      $("#indexTxtHint").css("display", "none");
    });
    current.parent().addClass('active');
  };
  unloadPoll();
  if (tabsUnloadData[lasturl] != null) {
    $.when(tabsUnloadData[lasturl]()).then(setTabActivePart2);
  }
  else {
    setTabActivePart2();
  }
};
var autocollapse = function() {
  var tabs = $('#prodTabs');
  var winWidth = $(window).width();
  var collapsed;
  if (winWidth > 1038) {
    $("#lastTab").css('display', 'none');
    collapsed = $('#collapsed').children('li');
    if (!collapsed[0]) {
      return;
    }
    $(collapsed[0]).insertBefore(tabs.children('li:last-child'));
    return;
  }
  else {
    /*if (winWidth < 752 && $('#prodTabs > li.active > a').attr('data-link') != "controlx.php") {
      $("body").css("padding-top", "168px");
    }
    else {
      $("body").css("padding-top", "132px");
    }*/

    $("#lastTab").css('display', 'block');
    var tabsHeight = tabs.innerHeight();
    var count;
    
    if (tabsHeight >= 50) {
      while(tabsHeight > 50) {
        var children = tabs.children('li:not(:last-child)');
        count = children.size();
        $(children[count-1]).prependTo('#collapsed');
        
        tabsHeight = tabs.innerHeight();
      }
    }
    else {
      while(tabsHeight < 50 && (tabs.children('li').size()>0)) {
        
        collapsed = $('#collapsed').children('li');
        count = collapsed.size();
        $(collapsed[0]).insertBefore(tabs.children('li:last-child'));
        tabsHeight = tabs.innerHeight();
      }
      if (tabsHeight>50) { // double chk height again
        autocollapse();
      }
    }
  }
  return;
};

var tabsUnloadData = {};
$(document).ready(function() {
  window.onerror=function(msg, url, linenumber, column, errorObj){
    universalErrorLog += msg + " " + url + ":" + linenumber + ":" + column + " (" + errorObj + ")";
    $.ajax({
      method: "POST",
      url: "generate_error.php",
      data: { msg: msg,
              url: url,
              linenumber: linenumber,
              column: column,
              errorObj: errorObj }
    });
    return false;
  };

  var optionsIgnoreHide = false;
  $("#js-replace").text("we couldn't load something.");
  $("#err-no-pageload").css("display", "none");
  var deviceSendMessage;
  var sendAjaxMessageToDevice = function(data, reg) {
    $.ajax({
        url: "send_message.php",
        type: 'GET',
        data: {
            regId: reg,
            message: data
        }
    });
  };
  $("#settings_startlocate").click(function(){
    sendAjaxMessage("location-" + (Cookies.get("options_autolocateUpdFreq") ? Cookies.get("options_autolocateUpdFreq") : 10));
  });
  $("#settings_stoplocation").click(function(){
    sendAjaxMessageToDevice("stoplocation", $(this).attr("data-reg"));
  });
  $(".iform-home").css("max-height", $(window).height() - 217);
  autocollapse();

  $(window).on('resize', autocollapse); // when window is ersized

  $('input[type="number"]').keypress(function(event) {
    var key = window.event ? event.keyCode : event.which;
    if (event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39) {
        return true;
    }
    else if ( key < 48 || key > 57 ) {
        event.preventDefault();
        return false;
    }
    else return true;
  });
  var feedbackPopover = function(type, extra) {
    var alertType = "alert-success";
    var glyphiconType = "glyphicon-ok-sign";
    var action = "Submitted";
    if (extra == null) {
      extra = "";
    }
    else if (extra.startsWith("error")) {
      alertType = "alert-danger";
      glyphiconType = "glyphicon-remove-sign";
      action = "Could Not Be Submitted";
    }
    var xcontent = '<div class="alert ' + alertType +'" role="alert"><span class="glyphicon glyphicon ' + glyphiconType + '"></span><strong>' + type + " " + action + ' </strong>' + extra + '</div>';
    $("#feedback-launch").popover(
      {
        html: true,
        content: xcontent,
        placement: "top",
        trigger: "manual"
      }
    ).popover('show');
    setTimeout(function(){
      $("#feedback-launch").popover('destroy');
    }, 1100);
  }
  if (Cookies.get('options_resolveSMSContacts') == "true") {
    $('#sms-contacts-option1').prop("checked", true);
    $('#sms-contacts-option1').parent().addClass("active");
  }
  else {
    $('#sms-contacts-option2').prop("checked", true);
    $('#sms-contacts-option2').parent().addClass("active");
  }
  if (Cookies.get('options_alwaysAutoLoad') == "true") {
    $('#always-auto-load-option1').prop("checked", true);
    $('#always-auto-load-option1').parent().addClass("active");
  }
  else {
    $('#always-auto-load-option2').prop("checked", true);
    $('#always-auto-load-option2').parent().addClass("active");
  }
  if (Cookies.get('options_resolveLocationMarkers') == "true") {
    $('#map-markers-option1').prop("checked", true);
    $('#map-markers-option1').parent().addClass("active");
  }
  else {
    $('#map-markers-option2').prop("checked", true);
    $('#map-markers-option2').parent().addClass("active");
  }
  if (Cookies.get('options_autolocateUpdFreq') != undefined) {
    $('#autolocate-update-frequency').val(Cookies.get('options_autolocateUpdFreq'));
  }
  else {
    $('#autolocate-update-frequency').val(10);
  }
  if (Cookies.get('options_autoRefreshFreq') != undefined ) {
    $('#autorefresh-update-frequency').val(Cookies.get('options_autoRefreshFreq'));
  }
  else {
    $('#autorefresh-update-frequency').val(0);
  }
  if (Cookies.get('options_experimentalResolveContacts') != "false") {
    $('#experimental-resolve-contacts-option1').prop("checked", true);
    $('#experimental-resolve-contacts-option1').parent().addClass("active");
  }
  else {
    $('#experimental-resolve-contacts-option2').prop("checked", true);
    $('#experimental-resolve-contacts-option2').parent().addClass("active");
  }
  $("#start-tutorial-again").bind("click", function() {
    $("#home_tab").tab();
    setTabActive($("#home_tab"));
    $("#dummy-tutorial-element").html("");
    $.when( $.ajax( "controlx.php" ) ).done( function() {
      optionsIgnoreHide = true;
      $("#optionsModal").modal("hide");
      $.get("tutorialx.php", function(data) {
        optionsIgnoreHide = false;
        $("#dummy-tutorial-element").html(data);
      });
    });
  });
  $('#prodTabs > li > a:not(#more-tab-link)').click( function(e) {
    e.preventDefault();
    setTabActive($(this));
  });
  $('#collapsed > li > a').click( function(e) {
    e.preventDefault();
    setTabActive($(this));
  });
  $('#options-form > div.iform-home > div:nth-child(1) > div > label.btn.btn-primary').click(function(){
    if ($('#sms-contacts-option1').parent().hasClass("active")) {
      $('#sms-contacts-option1').prop("checked", false);
      $('#sms-contacts-option1').parent().removeClass("active");
      $('#sms-contacts-option2').prop("checked", true);
      $('#sms-contacts-option2').parent().addClass("active");
    }
  });
  $('#options-advanced-settings > div:nth-child(1) > div > div > label.btn.btn-primary').click(function(){
    if ($('#experimental-resolve-contacts-option1').parent().hasClass("active")) {
      $('#experimental-resolve-contacts-option1').prop("checked", false);
      $('#experimental-resolve-contacts-option1').parent().removeClass("active");
      $('#experimental-resolve-contacts-option2').prop("checked", true);
      $('#experimental-resolve-contacts-option2').parent().addClass("active");
    }
  });

  $('#commit-options').click(function() {
    if ($('#always-auto-load-option1').parent().hasClass("active")) {
      Cookies.set('options_alwaysAutoLoad', 'true', { expires: 5000 });
    }
    else {
      Cookies.set('options_alwaysAutoLoad', 'false', { expires: 5000 }); 
    }

    if ($('#sms-contacts-option1').parent().hasClass("active")) {
      Cookies.set('options_resolveSMSContacts', 'true', { expires: 5000 });
    }
    else {
      Cookies.set('options_resolveSMSContacts', 'false', { expires: 5000 }); 
    }
    if ($('#map-markers-option1').parent().hasClass("active")) {
      Cookies.set('options_resolveLocationMarkers', 'true', { expires: 5000 });
    }
    else {
      Cookies.set('options_resolveLocationMarkers', 'false', { expires: 5000 }); 
    }
    if ($('#experimental-resolve-contacts-option1').parent().hasClass("active")) {
      Cookies.set('options_experimentalResolveContacts', 'true', { expires: 5000 });
    }
    else {
      Cookies.set('options_experimentalResolveContacts', 'false', { expires: 5000 }); 
    }
    if ($('#autorefresh-update-frequency').val() > 0) {
      Cookies.set('options_autoRefreshPages', 'true', { expires: 5000 });
    }
    else {
      Cookies.set('options_autoRefreshPages', 'false', { expires: 5000 }); 
    }
    Cookies.set('options_autolocateUpdFreq', $('#autolocate-update-frequency').val(), { expires: 5000, path: '/' });
    Cookies.set('options_autoRefreshFreq', $('#autorefresh-update-frequency').val(), { expires: 5000, path: '/' });

    setTimeout(reloadOnThisTab, 25);
  });
  $('#optionsModal').on("hide.bs.modal", function() {
    if (!optionsIgnoreHide) {
      reloadOnThisTab();
    }
    else {
      optionsIgnoreHide = true;
    }
  });
  var activateTab = function(target) {
    $(target+"_tab").tab();
  };

  var initHomePg = function(){
    $.get("controlx.php", function(data) {
        $("#tab-curr").html(data);
    }).done(function(){
      $("#err-no-pageload").css("display", "none");
      $("#indexTxtHint").css("display", "none");
    });
  };
  var hash = window.location.hash;
  if (hash !== "") {
    try {
      activateTab(setTabActive($(hash+"_tab"))); 
    }
    catch(err) { // if tab does not exist send to home
      initHomePg();
    }
  }
  else {
    initHomePg();
  }
  /*if (Cookies.get('md_visited') != "true") {
    $('#first-visit-modal').modal('show');
    Cookies.set('md_visited', 'true', { expires: 4000 });
    $("#start-tutorial-btn").one("click", function() {
      $.get("tutorialx.php", function(data) {
          $("#dummy-tutorial-element").html(data);
      });
    });
  }*/


});
