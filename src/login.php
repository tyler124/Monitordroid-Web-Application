<?php if (!isset($_SESSION)) { session_start(); }
if (isset($_SESSION['loggedIn'])) {
    require_once "access.php";
    if (userIsLoggedIn()) {
        header("Location: ./");
        exit();
    }
}
 ?>

<!DOCTYPE html>
<html>
<!-- Monitordroid Web Application
     By Monitordroid Inc.
     Last Updated July 2015 -->
    <head>
      <title>Monitordroid Login</title>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
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
      <meta name="description" content="Monitordroid web application, the device management software for Android devices.">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
      <!--Bootstrap includes-->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
      <!--End bootstrap-->
      <script src="./lib/js_crookies.js"></script>
      <script src="./md_utilities.js"></script>
      <link rel="stylesheet" href="./main_style.css?v=2.0">
      <?php include_once 'db_functions.php';
      require_once 'access.php';?>
<style>
html {
    height: 100%;
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
.super-container {
    position: fixed;
    display: block;
    left: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    overflow-y:auto;
    -webkit-box-shadow: 0px 0px 100px rgba(0,0,0,.5) inset;
    -moz-box-shadow: 0px 0px 100px rgba(0,0,0,.5) inset;
    box-shadow: 0px 0px 100px rgba(0,0,0,.5) inset;
}
#login legend {
    margin-top: 2px;
    margin-bottom: 0.5em;
    margin-left: 4px;
    font-weight: 400;
}
#login form i {
    background-color: #363b41;
    border-radius: 3px 0px 0px 3px;
    color: #606468;
    display: block;
    float: left;
    height: 50px;
    line-height: 50px;
    text-align: center;
    width: 50px;
    font-size: 15px;
}
div.help-text {
    text-align: center;
    margin-bottom: 8px;
    font-size: 11pt;
    font-style: italic;
    color: #d8d8d8;
    line-height: 110%;
}
input[type="button"],input[type="text"], input[type="password"] {
    background-color: #3b4148;
    border-radius: 0px 3px 3px 0px;
    color: #d8d8d8;
    padding: 0 16px;
    width: 198px;
    border: none;
    font-size: 14px;
    height: 50px;
    line-height: 1.5em;
    -webkit-appearance: none;
    opacity: 0.72;
}
input:focus {
    opacity: 0.88;
}
button.login-btn{
    display: block;
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
    background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
    background: -moz-linear-gradient(center top, #0096FF, #005DFF);
    background: linear-gradient(#0096FF, #005DFF);
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
    border-radius: 3px;
    color: #fff;
    border-color:#0074a2;
    border-style:solid;
    border-width:1px;
    box-shadow:rgba(120,200,230,0.498039) 0 1px 0 0 inset,rgba(0,0,0,0.14902) 0 1px 0 0;
    box-sizing: border-box;
    cursor:pointer;
    letter-spacing:normal;
    line-height:32px;
    margin-top:2px;
    margin-bottom:10px;
    padding-bottom:14px;
    padding-top:8px;
    padding-right:11px;
    padding-left:20px;
    text-align:left;
    text-decoration:none solid #fff;
    text-indent:0;
    text-transform:none;
    white-space:nowrap;
    word-spacing:0;
    writing-mode:lr-tb;
    font-size: 14pt;
    width: 248px;
    height: 50px;
}
button.login-btn:hover{
    border-color:#005dff;
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#0078cc), to(#005DFF));
    background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#0078cc), to(#005DFF));
    background: -moz-linear-gradient(center top, #0078cc, #005DFF);
    background: linear-gradient(#0078cc, #005DFF);
}
#password-forgot-cancel {
    border-color:#ffa200;
    margin-bottom: 10px;
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffa200), to(#ff6900));
    background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#ffa200), to(#ff6900));
    background: -moz-linear-gradient(center top, #ffa200, #ff6900);
    background: linear-gradient(#ffa200, #ff6900);
}
#password-forgot-cancel:hover{
    border-color:#ef4900;
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ff6900), to(#ef4900));
    background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#ff6900), to(#ef4900));
    background: -moz-linear-gradient(center top, #ff6900, #ef4900);
    background: linear-gradient(#ff6900, #ef4900);
}

div#login {
    margin-left: auto;
    margin-right: auto;
    width: 250px;
    margin-top: 0;
}
form {
    display: inline-block;
    padding: 2px;
}
.error-text {
    color: rgb(230,100,100);
    text-align: center;
    font-size: 12pt;
    height: 50px;
}
#password-forgot-form {
    display: none;
}
#password-forgot-email {
    margin-right: -2px;
}
#password-forgot-init:hover {
    cursor: pointer;
}
.temphidden {
    display: none;
}
a {
    color: rgb(121, 222, 283);
}
a:hover {
    color: rgb(131, 152, 213);
}
.centered {
    padding-top: 12%;
}
html.waiting,
html.waiting * { cursor: wait !important; }
</style>
<script type="text/javascript">
$(document).ready(function(){
    $(function () {
      var hash = $('input[name="hash"]');
      hash.val(window.location.hash.substr(1)); // do not send the # symbol
    });
    $("#login-error").css("visibility", "hidden");
    $("#js-replace").html("");

    if ($("#loginerr").text() !== "") {
        $("#login-error").css("visibility", "visible");
    }
    $("input").focusout(function(){
        if ($(this).val() !== "") {
            $(this).css("opacity", "0.88");
        }
        else {
            $(this).css("opacity", "0.72");
        }
    });
    $("#log-in").click(function(){
        
    });
    $("#usr-login").submit(function(){
        if (($("#password").val() === "") || ($("#email").val() === "")) {
            $("#loginerr").text("Please fill in both fields.");
            $("#login-error").css("visibility", "visible");
            return false;
        }
        else {
            $("#log-in").html("<div class=\"spinner spinner-white spinner-insert\"></div>");
        }
    });
    $("#password-forgot-form").submit(function(e){
        $('html').addClass('waiting');  // set waiting
        e.preventDefault();
        $("#password-forgot-cancel").prop("disabled");
        $.ajax({ method: "POST", url: "./usr_forgot_password.php", data: {email: $("#password-forgot-email").val()} })
        .done(function(){
            $('html').removeClass('waiting');
            $("#password-forgot-error").css("display","none");
            $("#email-disp").text($("#password-forgot-email").val());
            $("#password-forgot-success").css("display", "block");
        })
        .fail(function(){
            $('html').removeClass('waiting');
            $("#password-forgot-success").css("display","none");
            $("#password-forgot-error").css("display", "block");
        });
    });
    $("#password-forgot-init").click(function(){
        if ($("#password-forgot-email").val() === "") {
            $("#password-forgot-email").val($("#email").val());
        }
        $("#password-forgot-init").fadeOut();
        $("#usr-login").slideUp();
        $("#password-forgot-form").slideDown();
        $("#password-forgot-cancel").click(function(){
            $("#password-forgot-init").fadeIn();
            $("#usr-login").slideDown();
            $("#password-forgot-form").slideUp();
        });
    });
});
</script>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<div class="super-container">
    <div class="centered">
    	<!--<fieldset>-->
        <div id="login">
            <div class="alert jsinvisible alert-danger fade in" role="alert" id="login-error">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <strong>Error</strong> <span id="loginerr"><?php if (isset($_SESSION["loginError"])) {
                                                                    echo $_SESSION["loginError"];
                                                                    unset($_SESSION["loginError"]);
                                                                } ?></span><span id="js-replace">Javascript is disabled/not supported by your browser.<br>Try switching browsers (we suggest <a class="alert-link" href="https://www.google.com/chrome/browser/">Google Chrome</a>), <a class="alert-link" href="./?rnd=<?php echo rand(0, 1000); ?>">refreshing</a>, or emailing us at <a class="alert-link" href="mailto:help@monitordroid.com">help@monitordroid.com</a></span>
            </div>
            <div style="margin-left: 6px"><img src="./logos/MD_2_horizontal_white_lg_text_lightercolors.png" alt="Monitordroid" width="236" height="79"></div>
            <form method="post" id="usr-login" action="loginverf.php">
                <p><i class="fa fa-user"></i><input type="text" name="email" id="email" placeholder="email"  /></p>
                <p><i class="fa fa-lock"></i><input type="password" name="password" id="password" placeholder="password" /></p>
                <input type="hidden" name="action" value="login"/>
                <input type="hidden" name="hash" value=""/>
                <button id="log-in" class="login-btn" type="submit">Log in</button>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="keepmeloggedin" value="true"> Remember Me
                  </label>
                </div>
            </form>
            <form method="post" id="password-forgot-form">
                <p><i class="fa fa-user"></i><input type="text" name="email" id="password-forgot-email" placeholder="email"/></p>
                <button class="login-btn" id="password-forgot-go" type="submit">Reset Password</button>
                <button class="login-btn" id="password-forgot-cancel" type="button">Cancel</button>
                <div class="alert temphidden alert-danger fade in" role="alert" id="password-forgot-error">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <strong>Error</strong> An account with this email does not exist.
                </div>
                <div class="alert temphidden alert-success fade in" role="alert" id="password-forgot-success">
                    <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>
                  <strong>Success</strong> Please check your account email (<span id="email-disp"></span>) for further instructions.
                </div>
            </form>

            <div class="help-text"><a id="password-forgot-init">Forgot your password?</a></div>
            <div class="help-text"><a href="https://www.monitordroid.com/checkout/">Buy</a> or grab a <a href="https://www.monitordroid.com/free-trial">free trial</a></div>
        </div>
        <!--</fieldset>-->
    </div>
</div>
</body>
</html>
