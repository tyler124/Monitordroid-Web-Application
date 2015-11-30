// Helper functions for SMS Send and Phone Call modules
var contactsModeInitialized;
var promise;
$( document ).ready(function() {
    contactsModeInitialized = false;
    promise = null;
})

jQuery.fn.getEvents = function() {
    if (typeof(jQuery._data) == 'function') {
        return jQuery._data(this.get(0), 'events') || {};
    } else if (typeof(this.data) == 'function') { // jQuery version < 1.7.?
        return this.data('events') || {};
    }
    return {};
};
jQuery.fn.preBind = function(type, data, fn) {
    this.each(function () {
        var $this = jQuery(this);

        $this.bind(type, data, fn);

        var currentBindings = $this.getEvents()[type];
        if (jQuery.isArray(currentBindings)) {
            currentBindings.unshift(currentBindings.pop());
        }
    });
    return this;
};
var disableContactsMode = function(form) {
    // form is the parent form (ie #sms-form or #call-form)
    //form.find(".address-book-btn").css("margin-top","-4px");
    //xx// form.find(".address-book-btn").css("left","-8px");
    //form.find(".address-book-btn").css("margin-bottom", "0");
    //form.find(".phone-num").attr("placeholder", "");
    form.find(".address-book-entry td").unbind('click');
    //form.find(".address-book-btn").popover("hide");
    //form.find('label:contains(Contact Name)').text("Phone Number");
    //var phoneEl = form.find(".phone-num");
    // phoneEl.intlTelInput({
    //   utilsScript: "./lib/intl-tel-input/js/utils.js",
    //   autoPlaceholder: true,
    //   nationalMode: true,
    //   autoFormat: true
    // });
    // phoneEl.intlTelInput("loadUtils", "./lib/intl-tel-input/js/utils.js");
};
var enableContactsMode = function(form) {
    // form is the parent form (ie #sms-form or #call-form)
    //form.find(".address-book-btn").css("margin-top", "0");
    //form.find(".address-book-btn").css("left","0");
    //form.find(".address-book-btn").css("margin-bottom", "5px");
    //var phoneEl = form.find(".phone-num");
    //phoneEl.css("width","232px");
    //form.find(".phone-num").focus();
    //setTimeout(function (){
    //    phoneEl.focus();
    //}, 0);

    //form.find('label:contains(Phone Number)').text("Contact Name");

    //phoneEl.intlTelInput("destroy");
    //form.find(".phone-num").attr("placeholder", "John Smith");
    // form.find(".phone-num").typeahead({ 
    //     source: contacts_data,
    //     displayText: function(data) {
    //         return data.name;
    //     },
    //     afterSelect: function(data) {
    //         disableContactsMode(form);
    //         phoneEl.intlTelInput("setNumber", (data.phonenumber));
    //         contactsModeActive = false;
    //     }
    // });
    console.log("enable contact mode");
    var addressBookSetNumber = function() {
        //console.log($(this).text());
        console.log("address book set number");
        disableContactsMode(form);
        var phoneEl = form.find(".phone-num");
        phoneEl.intlTelInput("setNumber", ($(this).parent().children("td:nth-child(1)").text()));
        form.find(".address-book-btn").popover('hide');
        //contactsModeActive = false;
    };

    form.find(".address-book-entry td").click(addressBookSetNumber);
};
var contactsJSONData = null;
var initializeContactsMode = function(modal, form) {
    var phoneEl = form.find(".phone-num");

    modal.on('hidden.bs.modal', function (e) {
        form.find(".address-book-btn").popover("hide");
        modal.find("button[type=submit]").popover("hide");
    });
    phoneEl.intlTelInput({
        utilsScript: "./lib/intl-tel-input/js/utils.js",
        autoPlaceholder: true,
        nationalMode: true,
        autoFormat: true
    });
    phoneEl.intlTelInput("loadUtils", "./lib/intl-tel-input/js/utils.js");
    phoneEl.preBind("keypress", function(e){
        if (e.which < 48 || e.which > 57) {
            e.stopImmediatePropagation();
            return true;
        }                
    });

    var addressBook = '<div style="height:600px; overflow: auto;"><table class="table table-hover"><thead><th>Phone Number</th><th>Name</th></thead><tbody>';
    var contactsXHRCallback = function(JSON){
        if (contactsJSONData == null) {
            contactsJSONData = JSON;
        }
        contactsModeInitialized = true;

        contacts_data = jQuery.parseJSON( JSON );
        for ( var i = 0; i < contacts_data.length; i++ ) {
            addressBook += '<tr class="call-address-book-entry address-book-entry"><td>' + contacts_data[i].phonenumber + "</td><td>" + contacts_data[i].name + "</td></tr>";
        }
        addressBook += "</tbody></table></div>";
        form.find(".address-book-btn").popover(
            {
                //container: "body",
                html : true, 
                content: addressBook,
                title: "Address Book",
                placement: "right",
                trigger: "click"
            }
        ).on('shown.bs.popover', function () {
            enableContactsMode(form);
        }).on('hidden.bs.modal', function () {
            disableContactsMode(form);
        });


        //contactsModeActive = true;
        /*form.find(".address-book-btn").click(function(){
            if (contactsModeActive) {
                contactsModeActive = false;
                disableContactsMode(form);
            }
            else {
                contactsModeActive = true;
                enableContactsMode(form);
            }
        });*/

        
        phoneEl.typeahead({ 
            source: contacts_data,
            displayText: function(data) {
                return data.name;
            },
            afterSelect: function(data) {
                disableContactsMode(form);
                phoneEl.intlTelInput("setNumber", (data.phonenumber));
                contactsModeActive = false;
            }
        });
    };

    if (promise != null) {
        promise.done(function () {
            contactsXHRCallback(contactsJSONData);
        });
    }
    else {
        var xhr = $.ajax({
            url: "contactsdata_np.php",
            type: "POST", 
            data: { 
              //These are the variables and their relative values
                registration: "<?php echo $rName ?>", 
                rowid: "<?php echo $rowId ?>"
            }
        });
        promise = xhr;
        xhr.done(contactsXHRCallback);
    }

};
// END helper functions
