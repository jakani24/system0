<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
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
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Log viewer</title>
  
</head>
<body>
<div class="container m-3">
  <div class="row justify-content-center">
    <div class="col-md-auto">
      <h1>Alle Einträge</h1>
			<div class="overflow-auto">
			<table class="table">
  			<thead>
					<tr>
						<th>Datum & Zeit</th>
						<th>IP Adresse</th>
						<th>Typ</th>
						<th>Benutzername</th>
						<th>Info</th>
					</tr>
					<form method="GET" action="?search=true">
					<tr>
						<td>---</td>
						<td>---</td>
						<td>
							<select class="form-select" name="type_">
								<option type_="All_types">All_types</option>
								<option type_="PRINT::UPLOAD::PRINTER">PRINT::UPLOAD::PRINTER</option>
								<option type_="PRINT:JOB:START:FAILED">PRINT:JOB:START:FAILED</option>
								<option type_="PRINT::UPLOAD::QUEUE">PRINT::UPLOAD::QUEUE</option>
								<option type_="PRINT::UPLOAD::FILE::FAILED">PRINT::UPLOAD::FILE::FAILED</option>
								<option type_="JOB_INFO::PRINTERCTRL::FREE">JOB::PRINTERCTRL::FREE</option>
								<option type_="JOB_INFO::QUEUECTRL::REMOVE">JOB::QUEUECTRL::REMOVE</option>
								<option type_="JOB::PRINTERCTRL::CANCEL::FAILED">JOB::PRINTERCTRL::CANCEL::FAILED</option>
								<option type_="JOB::PRINTERCTRL::CANCEL">JOB::PRINTERCTRL::CANCEL</option>
							</select>
						</td>
					<td>
			<?php  //insert all the usernames
			
			
			$cnt=0;
			$sql="SELECT COUNT(*) FROM users";
		        $stmt = mysqli_prepare($link, $sql);
		        mysqli_stmt_execute($stmt);
		 
		        mysqli_stmt_store_result($stmt);
		        mysqli_stmt_bind_result($stmt, $cnt);
		        mysqli_stmt_fetch($stmt);
		
		        mysqli_stmt_close($stmt);		
		
			echo('<select class="form-select" id="username" name="username">');      
			echo('<option username="All_usernames">All_usernames</option>');
		        //now get those users
		        $cnt2=1;
		        $id=0;
		        $last_id=0;
		        while($cnt2!==$cnt+1)
		        {
				$sql = "SELECT id, username FROM users WHERE id > $last_id ORDER BY id;";
				$stmt = mysqli_prepare($link, $sql);
		
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $id,$username);
				mysqli_stmt_fetch($stmt);
				$last_id=$id;
				echo('<option username="'.$username.'">'.$username.'</option>');
				mysqli_stmt_close($stmt);
				$cnt2++;
			}
			
			
			
			
			?>
			</td><!-- username -->
			<td><button type="submit" class="btn btn-primary">Apply filter</button>
			</tr></form>
			<?php
				 $fp=fopen("/var/www/html/system0/html/php/login/v3/log/sys0.log","r");
				 while(!feof($fp))
				 {
				 	$content=fgets($fp);
				 	$data=explode(";",$content);
				 	if(!feof($fp))
				 	{
				 		if($data[2]==$_GET["type_"] or $_GET["type_"]=="All_types" or !isset($_GET["type_"]))
				 		{
				 			if($data[3]==$_GET["username"] or $_GET["username"]=="All_usernames" or !isset($_GET["username"]))
				 				echo("<tr><td>".$data[0]."</td><td>".$data[1]."</td><td>".$data[2]."</td><td>".$data[3]."</td><td>".$data[4]."</td></tr>");
				 		}
				 	}
				 
				 }
				 fclose($fp);
			?>
				</tbody>
        </table>
			</div>
    </div>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
