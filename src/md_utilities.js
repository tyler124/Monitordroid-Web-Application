var LOADING_TIMEOUT = 12;
var rown;

var initializeGlobals = function(setrown)  {
  rown = setrown;
};

var handleDelay = function(sessionvar, callback, loadTime) {
  console.log(sessionvar);
  date = sessionStorage.getItem(sessionvar);
  if (date === null) {
    sessionStorage.setItem(sessionvar, "Sat, 28 May 2015 05:07:43 GMT");
    console.log("WARNING: Date session storage for requested delay is not avaliable");
    callback();
    return;
  }
  var currTime = new Date();
  //console.log("time curr = " + currTime.getTime());
  var dateDiff = (currTime.getTime() - new Date(date)) - loadTime;
  console.log("dateDiff = " +dateDiff);
  if (dateDiff > 0) {
    callback();
  }
  else {
    $("#txtHint").css("display", "block");
    var myCallback = function() {
      callback();
      $("#txtHint").css("display", "none");
    };
    setTimeout(myCallback, dateDiff * (-1) );
    //return dateDiff * (-1);
  }
};
var iters = 0;
var failedUpdateLoadOld = false;
var inProgress = false;
var clearingPoll = false;
var atimeout;
function poll (datascript, reg, row, date, callback, gcmMsg, updateResolution, noSuccessUpdate) {
  /*}).fail(function(jqXHR, txtStatus, errorThrown){*/
  var fm = function() {
    console.log("running fm");
    if (updateResolution === undefined){
      updateResolution = 15;
    }
    if (noSuccessUpdate === undefined) {
      noSuccessUpdate = false;
    }
    $.ajax({
      url: datascript,
      type: "POST",
      data: {
          registration: reg,
          rowid: row,
          reqtime: date
      },
      global: false
    }).done(function(result, textStatus, jqXHR){
      if (textStatus == "success") {

        if (!noSuccessUpdate) {
          if (failedUpdateLoadOld) {
            failedUpdateLoadOld = false;
          }
          else {
            if (gcmMsg !== undefined) {
              var sessionFailureVar = "lastRefresh" + gcmMsg + "For" + row + "Failed";
              localStorage.setItem(sessionFailureVar, "false");
            }
            $("#no-data-warning").fadeOut();
            $("#device-error").fadeOut();
          }
          iters = 0;
          inProgress = false;
          $("#new-spinner").remove();
          callback(result);

          return false;

        } else {
          callback(result);
          $("#txtHint").after($('<div/>', {
              class: 'spinner',
              id: 'new-spinner'
          }));

          setTimeout(function(){
            $("#update_btn").prop("disabled", false);
          }, 300);
          return false;
        }
      } else if (textStatus == "nocontent") {
        if (iters > LOADING_TIMEOUT) {
            // device is not responding
            iters = 0;
            $("#update_btn").prop("disabled", false);
            $("#txtHint").fadeOut();
            var deviceErrMsg = 'Your device is unavailable. It is probably not connected to the internet right now, or Monitordroid may have been uninstalled. If this persists and you think it is an error please submit <a data-target="#feedback-modal" data-toggle="modal">feedback</a>. The data you see here is from your last successful update.';
            if (gcmMsg == "readsms") {
              deviceErrMsg += " Your device may also not have SMS capabilities.";
            }
            else if (gcmMsg == "calls") {
              deviceErrMsg += " Your device may also not have voice call capabilities.";
            }
            else if (gcmMsg == "location") {
             deviceErrMsg += " Your device may also not have GPS/location capabilities."; 
            }
            $("#new-spinner").remove();
            $("#device-error-msg").html(deviceErrMsg);
            $("#device-error").fadeIn();
            failedUpdateLoadOld = true;
            if (gcmMsg !== undefined) {
              var sessionFailureVar = "lastRefresh" + gcmMsg + "For" + row + "Failed";
              localStorage.setItem(sessionFailureVar, "true");
            }

            //return false;
            poll(datascript, reg, row, 0, callback, gcmMsg, updateResolution);
            return false;
        }
        iters += 1;
        if (!clearingPoll) {
          // if it failed last time display the old data immediately.
          if (iters <= 1 && // only check on the first iteration
           localStorage.getItem("lastRefresh" + gcmMsg + "For" + row + "Failed") == "true") {
            $("#device-error-msg").html('We\'re trying to load your data after it failed to load last time. The data you see here is from your last successful update.');
            $("#device-error").fadeIn();

            poll(datascript, reg, row, 0, callback, gcmMsg, updateResolution, true);
          }
          if (iters > (LOADING_TIMEOUT / 2)) {
            setTimeout(function(){
              poll(datascript, reg, row, date, callback, gcmMsg, updateResolution);
            }, updateResolution * 180);
          }
          else {
            setTimeout(function(){
              poll(datascript, reg, row, date, callback, gcmMsg, updateResolution);
            }, updateResolution * 110);
          }
          return false;
        }
        else {
          clearingPoll = false;
          inProgress = false;
        }
      }
      else {
        return false;
      }
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log("error in devicex", jqXHR);
      $("#device-error").fadeIn();
      $("#update_btn").prop("disabled", false);
      $("#txtHint").fadeOut();
      $("#new-spinner").remove();
      $("#device-error-msg").text("ERROR: " + textStatus + " (" + errorThrown + ").");
      return false;
    });
  };
  atimeout = setTimeout(fm, 500);
  inProgress = true;
}
var unloadPoll = function(){
  
  if (inProgress) {
    console.log("unloading poll");
    window.clearTimeout(atimeout);
    clearingPoll = true;

    setTimeout(function(){
      clearingPoll = false;
    }, 150);
  }
};

var lastUpdated = {};

var sendGCMRefreshMessage = function(msg, reg, callable, row) {
  if (callable === null) {
    callable = function(){};
  }
  console.log("sendgcmmsg fun "+ msg);
  var gcmUpdateKey = "lastRefresh" + (((msg.split("-")[0]).split(";"))[0]).split("(")[0];
  if (row === undefined) {
    gcmUpdateKey = gcmUpdateKey + "For" + rown;
  }
  else {
    gcmUpdateKey = gcmUpdateKey + "For" + row;
  }
  if (localStorage) {
    localStorage.setItem(gcmUpdateKey, new Date().toUTCString());
  }
  else {
    alert("ATTN: This website requires a modern browser that supports localStorage and sessionStorage.");
  }
  lastUpdated[gcmUpdateKey] = (+ new Date()) / 1000;
  //console.log((+ new Date()) / 1000);

  var deferred = $.ajax({
    url: "send_message_post.php",
    type: "POST", //Or even get
    data: {
      message: msg,
      regId: reg
    }
  }).done(callable);
};

var handleCallback = function(gcmMsg, datascript, callback, reg, row, isLoadmore, updateResolution) {

  var gcmUpdateKey = "lastRefresh" + gcmMsg + "For" + row;
  console.log("gcmupdkey", gcmUpdateKey);
  console.log("lastupdary", lastUpdated);

  var date;

  if (! lastUpdated.hasOwnProperty(gcmUpdateKey)) {
    date = ((+ new Date(localStorage.getItem(gcmUpdateKey))) / 1000);
    if (date === null) {
      localStorage.setItem(gcmUpdateKey, "Sat, 28 May 2000 05:07:43 GMT");
      console.log("WARNING: Date session storage for requested delay is not avaliable");
      date = 0;
    }
    lastUpdated[gcmUpdateKey] = date;
  }
  else {
    date = lastUpdated[gcmUpdateKey];
    
  }
  console.log('eval date', date);
  date = ~~date;
  
  if (isLoadmore != undefined && isLoadmore !== true) {
    console.log("date is ", date);
    $("#txtHint").css("display", "block");
  }
  iters = 0;

  console.log('datelastupd', date);

  if (updateResolution !== undefined) {
    poll(datascript, reg, row, date, callback, gcmMsg, updateResolution);
  } else {
    poll(datascript, reg, row, date, callback, gcmMsg);
  }
  
};



var loadScript = function(src,callback) {
  loadScript.scripts = {};
  if (loadScript.scripts[src] === true) {
    return;
  }
  loadScript.scripts[src] = true;
  var script = document.createElement("script");
  script.type = "text/javascript";
  if(callback)script.onload=callback;
  document.getElementsByTagName("head")[0].appendChild(script);
  script.src = src;
};
