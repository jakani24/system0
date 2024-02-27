<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
include "queue.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"]!=="admin"){
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
	if($role=="user")
	{
		echo "<script type='text/javascript' >load_user()</script>";
	}
	if($role=="admin")
	{
		echo "<script type='text/javascript' >load_admin()</script>";
	}

?>
<?php $color=$_SESSION["color"]; ?>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<div id="content"></div>

<head>
  <title>Alle Augaben</title>
  
</head>
<body>
	<div class="container mt-5" style="height: 95vh;">
		<div class="row justify-content-center">
	  	<div style="width: 100hh">
	      <h1>Druckerfreigabe erzwingen (falls beim freigeben Fehlermeldungen angezeigt werden)</h1>
				<?php
					if(isset($_POST['free']))
					{
						$printer_id=htmlspecialchars($_GET['free']);
						$sql="select used_by_userid from printer where id=$printer_id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $cnt);
						mysqli_stmt_fetch($stmt);	
						$sql="update printer set free=1,printing=0,cancel=0 ,used_by_userid=0 where id=$printer_id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
					}
					
					$cnt=0;
					$url="";
					$apikey="";
					$sql="select count(*) from printer where free=0";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cnt);
					mysqli_stmt_fetch($stmt);	
					//echo($cnt);
					echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Druckerid</th><th>Freigeben</th></tr></thead><tbody>");
					$last_id=0;					
					while($cnt!=0)
					{
						$userid=0;
						$sql="select id,printer_url,apikey,cancel,used_by_userid from printer where free=0 and id>$last_id ORDER BY id";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $printer_id,$url,$apikey,$cancel,$userid);
						mysqli_stmt_fetch($stmt);
	
						
						$last_id=$printer_id;
						
						$used_by_user="";
						$sql="select username from users where id=$userid";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $used_by_user);
						mysqli_stmt_fetch($stmt);


						echo("<tr><td>$printer_id</td><td><form method='POST' action='?free=$printer_id'><button type='submit' value='free'  name='free' class='btn btn-dark'>Free</button></form></tr>");
						
						$cnt--;
					}
					echo("</tbody></table></div></div></div></div>");
				?>
				<br><br>
				<?php
					test_queue($link);
				?>
				</div>
	    </div>
	  </div>
	</div>
	<div id="footer"></div>
</body>

</html>
