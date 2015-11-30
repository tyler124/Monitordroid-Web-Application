<?php session_start(); ?>
<!DOCTYPE html>
<?php
	if(!isset($_SESSION['email'])){
	    header("location:index.php");
	    exit();
    }

    include_once 'db_functions.php';
    $db = new DB_Functions();
    $users = $db->getUserByEmail($_SESSION['email']);
    $maxNumberOfDevices = $db->getMaxDevices($_SESSION['email']);
    $isEnterprise = $maxNumberOfDevices > 12;
    
    if ($users != false)
        $no_of_users = $users->rowCount();
    else
        $no_of_users = 0;

	?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="apple-touch-icon" sizes="57x57" href="./apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="./apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="./apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="./apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="./apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="./apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="./apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="./apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="./apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="./favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="./android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="./favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="./favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="./manifest.json">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="./mstile-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!--Bootstrap includes-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <!--end bootstrap-->
    <link rel="stylesheet" href="./main_style.css">
    <style>
    td {
        cursor: default;
    }
    .type-selected td {
        background-color: #eaeaea
    }
    .type-selected {
        border-color: #e2e2e2;
        border-width: 1px;
    }
    .type-sel-container, .type-sel-all-container {
        text-align: center;
    }

    .device-selected td {
        background-color: #eaeaea
    }
    .device-selected {
        border-color: #e2e2e2;
        border-width: 1px;
    }
    .device-sel-container, .device-sel-all-container {
        text-align: center;
    }
    .device-sel-all-container:hover, .type-sel-all-container:hover {
        background-color: #e2e2e3;
    }
    html {
        height: 100%;
        width: 100%;
    }
    body {
        color: #fff;
        font-family: Helvetica, Arial, sans-serif;
        background: #525763;
        background: -moz-radial-gradient(center, ellipse cover, #525763 0%, #41464f 100%);
        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,#525763), color-stop(100%,#41464f));
        background: -webkit-radial-gradient(center, ellipse cover, #525763 0%,#41464f 100%);
        background: -o-radial-gradient(center, ellipse cover, #525763 0%,#41464f 100%);
        background: -ms-radial-gradient(center, ellipse cover, #525763 0%,#41464f 100%);
        background: radial-gradient(ellipse at center, #525763 0%,#41464f 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#525763', endColorstr='#41464f',GradientType=1 );
        margin: 0;
        background-repeat: no-repeat;
        background-attachment: fixed;
        padding-top: 0;
    }
    .shadow {
        position: fixed;
        left: 0px;
        top: 0px;
        right: 0px;
        bottom: 0px;
        overflow-y:auto;
        -webkit-box-shadow: 0px 0px 100px rgba(0,0,0,.5) inset;
        -moz-box-shadow: 0px 0px 100px rgba(0,0,0,.5) inset;
        box-shadow: 0px 0px 100px rgba(0,0,0,.5) inset;
        padding-top: 15px;
    }
    h2 small {
        color: rgb(200, 200, 200);
    }
    p.help-block {
        color: rgb(225, 225, 225);
    }
    .body {
        margin: 0;
        padding: 0;
        width: auto;
        height: auto;
        position: absolute;
        display: block;
        bottom: 0;
        top: 0;
        left: 0;
        right: 0;
        /*pointer-events: none;
        padding-top: 10px;*/
        text-align: center;
    }
    .device-panel {
        width: 500px !important;
        display: inline-block;
        color: black;
    }
    .tasks-panel {
        width: 350px !important;
        display: inline-block;
        color: black;
    }
    .types-panel {
        width: 350px !important;
        display: inline-block;
        color: black;
    }
    .device-manage {
        margin-left: auto;
        margin-right: auto;
        width: 900px;
        text-align: left;
    }
    .device-name .device-text:hover {
        cursor: text;
    }
    .device-name:hover {
        /*cursor: pointer;*/
        background-color: #e2e2e3;
    }
    .device-text input[type="text"] {
        padding: 0;
        min-width: 100px;
        margin-right: -15px;
        margin-left: 2px;
    }
    .selector-select:hover{ 
        cursor: pointer;
    }
    .sku-text {
        /*display: none;*/
        float:right;
        margin-right: 1px;
        font-variant: small-caps;
        visibility: hidden;
    }
    .row.account-info {
        width: 500px;
    }
    .row.account-info > div.col-xs-12 {
        padding-right: 0;
    }
    .account-info p {
        float: left;
    }
    .account-info {
        text-align: right;
    }
    .sku-container {
        display: inline-block;
        margin-top: 5px;
    }
    .sku-container:hover > .sku-text {
        visibility: visible;
    }
    </style>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <script type="text/javascript">
    var expand = function(textbox) {
        if (!textbox.startW) { textbox.startW = textbox.offsetWidth; }

        var style = textbox.style;

        //Force complete recalculation of width
        //in case characters are deleted and not added:
        style.width = 0;

        var desiredW = textbox.scrollWidth;
        //Optional padding to reduce "jerkyness" when typing:
        desiredW += textbox.offsetHeight - 10;

        style.width = desiredW + 'px';
    };
    var inNameChange;
    $(document).ready(function() {

        var nameChange = function(){
            var $this = $(this);
            if ($this.hasClass("device-text")) {
                $this.unbind("click");
                $this = $this.parent();
                $this.unbind("dblclick");
            }
            else {
                $this.children(".device-text").unbind("click");
                $this.unbind("dblclick");
            }
            
            $this.children(".device-text").html(
                $("<input/>", {value: $this.children(".device-text").text(), type: "text"})
            );
            parentThis = $this;
            inNameChange = false;
            $this.children(".device-text").children("input").bind('blur keypress', function (event) {
                if (!inNameChange && (event.type == 'blur' || event.keyCode == '13')) {
                    inNameChange = true;
                    var xthis = $(this);
                    //Disable textbox to prevent multiple submit
                    xthis.attr("disabled", "disabled");

                    if (parentThis.attr("data-origname") === xthis.val()) {
                        xthis.replaceWith(xthis.val());
                        parentThis.dblclick(nameChange);
                        parentThis.children(".device-text").click(nameChange);
                        inNameChange = false;
                        return;
                    }
                    $.ajax({
                        method: "POST",
                        url: "devicechangename.php",
                        data: {"id": parentThis.attr("data-id"), "new_name": xthis.val()}
                    }).done(function(){
                        parentThis.attr("data-origname", xthis.val());
                        xthis.replaceWith(xthis.val());
                    }).fail(function(){
                        alert("Device name could not be changed. User does not have access.");
                        xthis.replaceWith(parentThis.attr("data-origname"));
                    }).always(function(){
                        parentThis.dblclick(nameChange);
                        parentThis.children(".device-text").click(nameChange);
                        inNameChange = false;
                    });
                }
                else if (!inNameChange) {
                    expand($(this)[0]);
                }
            });
            $this.children(".device-text").children("input").focus();
            expand($this.children(".device-text").children("input")[0]);
        };
        $('.device-name').dblclick(nameChange);
        $(".device-text").click(nameChange);
        //<?php if ($isEnterprise) { ?>

        $(".device-table > tbody > tr > td").click(function(e) {
            if ($(this).parent().children(".device-name").children(".device-text").children("input").length === 0 &&
                !$(e.target).is(':checkbox')) {
                var chkbox = $(this).parent().children(".device-sel-container").children(".device-select");
                chkbox.prop("checked", !chkbox[0].checked).change();
            }
        });
        $(".device-table > tbody > tr > td.device-sel-container > input.device-select").change(function(e){
            if (this.checked) {
                if ($(".device-select:checked").length == $(".device-select").length) {
                    $(".device-select-all").prop("checked", true);
                }
                $(this).parent().parent().addClass("device-selected");
            }
            else {
                if ($(".device-select-all")[0].checked) {
                    $(".device-select-all").prop("checked", false);
                }
                $(this).parent().parent().removeClass("device-selected");
            }
        });
        $(".selector-select").click(function(){
            $(this).parent().parent().parent().find(".selector-active-text").text($(this).text());
        });


        $(".device-table > thead > tr > th.device-sel-all-container").click(function(e) {
            if (!$(e.target).is(':checkbox')) {
                //var chkbox = $(this).parent().children(".device-sel-all-container").children(".device-select-all");
                var chkbox = $(this).children(".device-select-all");
                chkbox.prop("checked", !chkbox[0].checked).change();
            }
        });
        $(".device-table > thead > tr > th.device-sel-all-container > input.device-select-all").change(function(e){
            var row = $(this).parent().parent().parent().parent().children("tbody").children("tr");
            if (this.checked) {
                row.addClass("device-selected");
                row.children("td.device-sel-container").children("input.device-select").prop("checked", true).change();
            }
            else {
                row.removeClass("device-selected");
                row.children("td.device-sel-container").children("input.device-select").prop("checked", false).change();
            }
        });


        $(".type-table > thead > tr > th.type-sel-all-container").click(function(e) {
            if (!$(e.target).is(':checkbox')) {
                var chkbox = $(this).children(".type-select-all");
                chkbox.prop("checked", !chkbox[0].checked).change();
            }
        });
        $(".type-table > thead > tr > th.type-sel-all-container > input.type-select-all").change(function(e){
            var row = $(this).parent().parent().parent().parent().children("tbody").children("tr");
            if (this.checked) {
                row.addClass("type-selected");
                row.children("td.type-sel-container").children("input.type-select").prop("checked", true).change();
            }
            else {
                row.removeClass("type-selected");
                row.children("td.type-sel-container").children("input.type-select").prop("checked", false).change();
            }
        });



        $(".type-table > tbody > tr > td").click(function(e) {
            if (!$(e.target).is(':checkbox')) {
                var chkbox = $(this).parent().children(".type-sel-container").children(".type-select");
                chkbox.prop("checked", !chkbox[0].checked).change();
            }
        });
        $(".type-table > tbody > tr > td.type-sel-container > input.type-select").change(function(e){
            if (this.checked) {
                if ($(".type-select:checked").length == $(".type-select").length) {
                    $(".type-select-all").prop("checked", true);
                }

                $(this).parent().parent().addClass("type-selected");
            }
            else {
                if ($(".type-select-all")[0].checked) {
                    $(".type-select-all").prop("checked", false);
                }
                $(this).parent().parent().removeClass("type-selected");
            }
        });

        //<?php } ?>

    });
    </script>

    <title>Manage Devices</title>
</head>
<body>
    <div class="shadow">
        <div class="super-container">
            <div class="device-manage">
                <p class="lead"><a href="./">Return to App</a></p>
                <h2 >Manage Devices <small><?php echo $_SESSION['email']; ?></small></h2>
                <div class="row account-info">
                    <div class="col-xs-12">
                        <p class="help-block" style="display:inline-block">This account is limited to <?php echo $maxNumberOfDevices; ?> device(s) </p>
                        <div class="sku-container" style="display:inline-block"><span class="sku-text">SKU: <?php if ($isEnterprise) {echo "Enterprise"; } else { echo "Personal"; }?></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-7">
                        <div class="panel panel-default device-panel">
                          <div class="panel-heading">
                            <h3 class="panel-title">Devices<span class="pull-right"><small><?php echo $no_of_users; ?>/<?php echo $maxNumberOfDevices; ?></small></span></h3>
                          </div>
                          <div class="panel-body">
                            <?php
                            if ($no_of_users > $maxNumberOfDevices) { ?>
                            <div class="device-alert alert alert-danger fade in" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <strong>Warning</strong> Looks like you have too many devices for your current account.<br/> You can delete a device here, or email <a class="alert-link" href="mailto:sales@monitordroid.com">sales@monitordroid.com</a> for enterprise pricing options.
                            </div>
                            <?php 
                            }
                            if ($no_of_users > 0) {
                                ?><div class="row">
                                    <table class="table device-table table-hover">
                                        <?php if ($isEnterprise) { ?><col/><?php }?><col style="width: inherit"/><col style="width: 114px"/><col style="width:73px"/>
                                        <thead>
                                            <tr><?php if ($isEnterprise) { ?><th class="device-sel-all-container"><input type="checkbox" class="device-select-all"/></th><?php }?><th>Name</th><th>Date Created</th><th>Type</th><th></th></tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        while ($row = $users->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <tr>
                                                <?php if ($isEnterprise) { ?><td class="device-sel-container"><input type="checkbox" class="device-select"></td><?php }?>
                                                <td class="device-name" data-id="<?php echo $row["id"]; ?>" data-origname="<?php echo $row["name"]; ?>"><span class="device-text"><?php echo $row["name"]; ?></span><span class="pull-right" style="position: relative; left: -5px"><span class="glyphicon glyphicon-pencil"></span></span></td>
                                                <td><?php echo $row["created_at"]; ?></td>
                                                <td><?php echo $row["devicename"]; ?></td>
                                                <td>
                                                    <form action="deletedeviceverify.php" method="post">
                                                        <input type="hidden" name="rowid" value="<?php echo $row["id"] ?>"/>
                                                        <button type="submit" class="btn btn-primary">Delete</button>
                                                    </form>
                                                </td> 
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                            <div class="device-alert alert alert-danger fade in" role="alert">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                                <strong>Ahem...</strong> We couldn't find any devices... Don't know how to add them? Download the <a href="https://play.google.com/store/apps/details?id=com.monitordroid.app" alt="Google Play App" class="alert-link">Android App</a> and check out our <a href="http://www.monitordroid.com/tutorial/" alt="tutorial" class="alert-link">tutorial</a>.
                            </div>
                            <?php } ?>
                          </div>
                        </div>
                    </div>
                    <?php if ($isEnterprise) { ?>
                    <div class="col-xs-5">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="panel panel-default tasks-panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Device Tasks</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <button type="button" class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-default">View</button>
                                            <button type="button" class="btn btn-default">Download</button>
                                            <span style="display:inline-block;position:absolute;right:29px">
                                                <button type="button" class="btn btn-primary">Controls</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="panel panel-default types-panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Types</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <table class="table type-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="type-sel-all-container"><input type="checkbox" class="type-select-all"/></th>
                                                        <th>Name</th>
                                                    </tr>
                                                </thead>
                                                <col/><col style="width: inherit"/>
                                                <tbody>
                                                    <tr>
                                                        <td class="type-sel-container"><input type="checkbox" class="type-select"/></td>
                                                        <td class="type-name">Contacts</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="type-sel-container"><input type="checkbox" class="type-select"/></td>
                                                        <td class="type-name">Text Messages</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="type-sel-container"><input type="checkbox" class="type-select"/></td>
                                                        <td class="type-name">Browser History</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="type-sel-container"><input type="checkbox" class="type-select"/></td>
                                                        <td class="type-name">Location</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="type-sel-container"><input type="checkbox" class="type-select"/></td>
                                                        <td class="type-name">Files</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="type-sel-container"><input type="checkbox" class="type-select"/></td>
                                                        <td class="type-name">App List</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>