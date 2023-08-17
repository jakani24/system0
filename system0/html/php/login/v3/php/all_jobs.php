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
   	$('#content').load("/system0/html/php/login/v3/html/admin_page.html");
	});
}
function load_user()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/user_page.html");
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
  <title>All jobs</title>
  
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
	  	<div class="col-md-6">
	      <h1>All running Jobs</h1>
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
						$sql="update printer set free=1,printing=0,cancel=0 ,used_by_userid=0 where id=1";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
					}
					if(isset($_POST['cancel']))
					{
						$apikey="";
						$printer_url="";
						$printer_id=htmlspecialchars($_GET['cancel']);
						$sql="select used_by_userid,apikey,printer_url from printer where id=$printer_id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $cnt,$apikey,$printer_url);
						mysqli_stmt_fetch($stmt);	
			
						exec("curl -k -H \"X-Api-Key: $apikey\" -H \"Content-Type: application/json\" --data '{\"command\":\"cancel\"}' \"$printer_url/api/job\" > /var/www/html/system0/html/user_files/$username/json.json");
						$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
						$json=json_decode($fg,true);
						if($json["error"]!="")
						{
							echo("<div class='alert alert-danger' role='alert'>There was an error canceling the print job !<br>The error is on our machine or printer, so please wait and trie again in some time!</div>");
						}
						else
						{
							$sql="update printer set cancel=1 where id=$printer_id";
							$stmt = mysqli_prepare($link, $sql);					
							mysqli_stmt_execute($stmt);
						}
						
					}
					if(isset($_POST["remove"]))
					{
						$quserid=0;
						$userid=$_SESSION["id"];
						$queueid=htmlspecialchars($_GET["remove"]);
						$sql="select from_userid from queue where id=$queueid";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $quserid);
						mysqli_stmt_fetch($stmt);
						
						$sql="delete from queue where id=$queueid";
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
					echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Printer</th><th>file</th><th>completion</th><th>free</th><th>cancel print</th><th>detailes</th></tr></thead><tbody>");
					while($cnt!=0)
					{
						$sql="select id,printer_url,apikey,cancel from printer where free=0";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id,$url,$apikey,$cancel);
						mysqli_stmt_fetch($stmt);
						//echo("curl $url/api/job?apikey=$apikey > /var/www/html/system0/html/user_files/$username/json.json");
						exec("curl --max-time 10 $url/api/job?apikey=$apikey > /var/www/html/system0/html/user_files/$username/json.json");
						$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
						$json=json_decode($fg,true);
						//var_dump($json);
						//echo($fg);
						
						$progress=(int) $json['progress']['completion'];
						if($progress<0)
							$progress=-$progress;
						$file=$json['job']['file']['name'];
						if($progress==100)
							echo("<tr><td>$id</td><td>$file</td><td>$progress%</td><td><form method='POST' action='?free=$id'><input type='submit' value='free'  name='free'> </form></td><td>Job already finished</td><td><form method='POST' action='job_info_all.php'><input type='submit' value='detailes'> </form></td></tr>");
						else if($cancel==1)
							echo("<tr><td>$id</td><td>$file</td><td>cancelled</td><td><form method='POST' action='?free=$id'><input type='submit' value='free'  name='free'> </form></td><td>Job cancelled</td><td><form method='POST' action='job_info_all.php'><input type='submit' value='detailes'> </form></td></tr>");
						else
							echo("<tr><td>$id</td><td>$file</td><td>$progress%</td><td>Job still running</td><td><form method='POST' action='?cancel=$id'><input type='submit' value='cancel'  name='cancel'> </form></td><td><form method='POST' action='job_info_all.php'><input type='submit' value='detailes'> </form></td></tr>");
			 			
						$cnt--;
					}
					echo("</tbody></table></div></div></div></div>");
					echo("free your printer after you've taken out your print!");
				?>
				<h1>All jobs in queue</h1>
				<?php
					$userid=$_SESSION["id"];
					$cnt=0;
					$filepath="";
					$sql="select count(*) from queue";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cnt);
					mysqli_stmt_fetch($stmt);	
					//echo($cnt);
					echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>file</th><th>remove from queue</th></tr></thead><tbody>");
					while($cnt!=0)
					{
						$sql="select id,filepath from queue";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);	
						echo mysqli_error($link);				
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id,$filepath);
						mysqli_stmt_fetch($stmt);
						$filepath=basename($filepath);
						echo("<tr><td>$filepath</td><td><form method='POST' action='?remove=$id'><input type='submit' value='remove'  name='remove'> </form></td></tr>");
			 			
						$cnt--;
					}
					echo("</tbody></table></div></div></div></div>");	
				
				?>
				<?php
					test_queue($link);
				?>
				</div>
	    </div>
	  </div>
	</div>
</body>

</html>
