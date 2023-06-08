<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
 require_once "php/config.php";
 require_once "keepmeloggedin.php";
$error=logmein($link);
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"]!== "admin"){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>

</head>
<?php $color=$_SESSION["color"]; ?>
<?php echo(" <body style='background-color:$color'> ");?>
    <script src="/system0/html/php/login/v3/js/load_page.js"></script>
     <script>
     	function load_user()
        {
            $(document).ready(function(){
            $('#content').load("/system0/html/php/login/v3/html/admin_page.html");
            });
        }
        load_user();
     </script>
    <div id="content"></div>
    <h1 class="my-5"> <center>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to system0!</center></h1>
    <p>
        <center>
        <label>Currently it is</label>
        <span id="span"></span>
        </center>

    </p>
</body>
</html>

<script>
var span = document.getElementById('span');

function time() {
  var d = new Date();
  var s = d.getSeconds();
  var m = d.getMinutes();
  var h = d.getHours();
  span.textContent = 
    ("0" + h).substr(-2) + ":" + ("0" + m).substr(-2) + ":" + ("0" + s).substr(-2);
}
time()
setInterval(time, 1000);
</script>
