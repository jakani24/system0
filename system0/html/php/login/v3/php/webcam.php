<!DOCTYPE html>
<html>
	<?php
	// Initialize the session
	session_start();
	include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
	include "config.php";
	require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
	include "queue.php";
	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
	    header("location: login.php");
	    exit;
	}
	$username=htmlspecialchars($_SESSION["username"]);
	?>

	<?php 
		$color=$_SESSION["color"]; 
		//include "/var/www/html/system0/html/php/login/v3/components.php";
	?>
	<script src="/system0/html/php/login/v3/js/load_page.js"></script>
	<script>
		function load_admin()
		{
			$(document).ready(function(){
		   	$('#content').load("/system0/html/php/login/v3/html/admin_page.php");
			});
			$(document).ready(function(){
   		$('#footer').load("/system0/html/php/login/v3/html/footer.html");
		});
		}
		function load_user()
		{
			$(document).ready(function(){
		   	$('#content').load("/system0/html/php/login/v3/html/user_page.php");
			});
			$(document).ready(function(){
   		$('#footer').load("/system0/html/php/login/v3/html/footer.html");
		});
		}
	</script>
	<?php
		$role=$_SESSION["role"];
		
		test_queue($link);
	?>

	<?php $userid=$_SESSION["id"]; ?>
	<?php echo(" <body style='background-color:$color'> ");?>
	<div id="content"></div>

	<head>
	  <title>Webcam</title>
	
	</head>

	<body>
		<?php
			$status=0;
			$free=0;
			$url="";
			$apikey="";
			$printer_url="";
			$printer_id=htmlspecialchars($_GET["printer_id"]);
			$sql="select printer_url, free, system_status,apikey,printer_url from printer where id=$printer_id";
			//echo $sql;
			$stmt = mysqli_prepare($link, $sql);					
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $url,$free,$status,$apikey,$printer_url);
			mysqli_stmt_fetch($stmt);
			mysqli_close($link);
			//echo $printer_url;
			//download the image
			//authentication is not necesarry
			//url=http://octopiX.local/webcam/?action=snapshot
			$path = "/var/www/html/system0/html/user_files/$username/image.jpeg";
			exec("rm $path");
			exec("wget \"http://$printer_url/webcam/?action=snapshot\" -O $path");
			echo("<img style='transform: rotate(180deg);' height='135' width='240' src='/system0/html/user_files/$username/image.jpeg'>");
		?>
		<script>
			setInterval(function() {
   			 location.reload();
			}, 5000);
		</script>
		
		
	
	</body>

</html>
