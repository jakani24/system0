<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
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
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Your jobs</title>
  
</head>
<style>
		table, td, th
		{
				border:1px solid;
				border-collapse: collapse;
				height:22px;
		}
		tr:nth-child(odd){background-color:#DCDCDC}
		tr:hover{background-color:#aaa}
		th{background-color:gray;text-align: left;}
</style>
<body>
<center>
	<h1>Your running Jobs</h1>
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
			if($cnt!=$_SESSION['id'])
			{
				echo("Wrong userid!");
			}
			else
			{
				$sql="update printer set free=1,printing=0 ,used_by_userid=0 where id=1";
				$stmt = mysqli_prepare($link, $sql);					
				mysqli_stmt_execute($stmt);
			}
		}
		$cnt=0;
		$url="";
		$apikey="";
		$sql="select count(*) from printer where used_by_userid=$id";
		$stmt = mysqli_prepare($link, $sql);					
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $cnt);
		mysqli_stmt_fetch($stmt);	
		//echo($cnt);
		echo '<div style="overflow-x: auto;">';
		echo("<table><tr><th>Printer</th><th>file</th><th>completion</th><th>free</th></tr>");
		while($cnt!=0)
		{
			$sql="select id,printer_url,apikey from printer where used_by_userid=$id";
			$stmt = mysqli_prepare($link, $sql);					
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $id,$url,$apikey);
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
				echo("<tr><td>$id</td><td>$file</td><td>$progress%</td><td><form method='POST' action='?free=$id'><input type='submit' value='free'  name='free'> </form></td></tr>");
			else
				echo("<tr><td>$id</td><td>$file</td><td>$progress%</td><td><form method='POST'>Job still running</td></tr>");
 			
			$cnt--;
		}
		echo("</table>");
		echo("free your printer after you've taken out your print!</div>fire");
	?>
</center>
<br><br><br>
</body>

</html>
