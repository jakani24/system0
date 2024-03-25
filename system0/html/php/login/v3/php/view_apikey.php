<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"][7]!=="1"){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
$id=$_SESSION["id"];
?>


<script src="/system0/html/php/login/v3/js/load_page.js"></script>
<script>
function load_admin()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/admin_page.php");
	});
}
function load_user()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/user_page.php");
	});
}
</script>
<?php
	$role=$_SESSION["role"];
	echo "<script type='text/javascript' >load_user()</script>";
?>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Api viewer</title>
  
</head>

<body>
<div class="container m-5" style="height: 95vh;">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h1>Dein system0 APIkey:</h1>
			<?php
				$apikey_fromdb="";
				$sql="select apikey from api where id=1";
				$stmt = mysqli_prepare($link, $sql);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $apikey_fromdb);
				mysqli_stmt_fetch($stmt);	
				echo("<b>".$apikey_fromdb."</b>");
				echo("<br><br>Behandle diesen Schlüssel wie ein Passwort.<br>Es ist wichtig, dass niemand diesen Schlüssel erfährt!");	
			?>
    </div>
  </div>
</div>
<div id="footer"></div>
	

</body>
</html>
