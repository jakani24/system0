<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
?>


<script src="/system0/html/php/login/v3/js/load_page.js"></script>
<script>
function load_user()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/user_page.html");
	});
}
function load_admin()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/admin_page.html");
	});
}
</script>
<?php
	$role=$_SESSION["role"];
	if($role=="user")
	{
		echo "<script type='text/javascript' >load_user()</script>";
	}
	if($role=="admin")
	{
		echo "<script type='text/javascript' >load_admin()</script>";
	}
?>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Bug report</title>
</head>
<body>
	<div class="center-container">
			<div class="container">
			  <div class="container mt-5 text-center">
				<h1>Fehler melden</h1>
				<form method="post" action="bugreport.php?sent">
				  <div class="form-group">
					<label class="my-3" for="bugDescription">Beschreibung des Fehlers:</label>
					<textarea class="form-control mx-auto" id="bugDescription" name="bug" rows="5" style="width:50%;" required></textarea>
				  </div>
				  <div class="form-group">
					<label class="my-3" for="email">Deine Email für weitere Nachfragen (optional)</label>
					<input type="text" class="form-control mx-auto" id="email" name="email" style="width:50%;">
				  </div>
				  <button type="submit" class="btn btn-primary my-5">abschicken</button>
				</form>
				<?php
				  if(isset($_GET["sent"]))
				  {
					$email = htmlspecialchars($_POST["email"]);
					$bug = htmlspecialchars($_POST["bug"]);
					$text = urlencode("JWAF INFORMATION:\nuser: $username;\nemail: $email\nbug: $bug\nEND");
					exec("curl 'https://api.callmebot.com/whatsapp.php?phone=41775252026&text=$text&apikey=6002955'");
					echo '<div class="alert alert-success" role="alert">Vielen Dank, deine Fehlermeldung ist bei uns angekommen und wir kümmern uns darum.</div>';
				  }
				?>
			  </div>
			</div>
		</div>
</body>

</html>
