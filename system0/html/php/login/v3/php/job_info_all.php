<!DOCTYPE html>
<html>
<?php //this file is only viewable by the admin and shows all jobs of all printers
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
 include "/var/www/html/system0/html/php/login/v3/components.php";
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
	<h1>All running Jobs</h1>
	<br>
	<a href="job_info_all.php">Reload</a>
	<br>
	<br>
	<?php
		$cnt=0;
		$url="";
		$apikey="";
		$sql="select count(*) from printer";
		$stmt = mysqli_prepare($link, $sql);					
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $cnt);
		mysqli_stmt_fetch($stmt);	
		//echo($cnt);
		echo '<div style="overflow-x: auto;">';
		while($cnt!=0)
		{
			echo("<table><tr><th>Keyword</th><th>value</th></tr>");
			$sql="select id,printer_url,apikey from printer";
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
			//echo($file);
			echo("<tr><td>average print time</td><td>".$json["job"]["averagePrintTime"]." Seconds</td></tr>");
			echo("<tr><td>estimated print time</td><td>".round($json["job"]["estimatedPrintTime"],0)." Seconds</td></tr>");
			echo("<tr><td>filament</td><td>".$json["job"]["filament"]."</td></tr>");
			echo("<tr><td>file</td><td>".$json["job"]["file"]["name"]."</td></tr>");
			echo("<tr><td>file size</td><td>".$json["job"]["file"]["size"]." Bytes</td></tr>");
			echo("<tr><td>progress</td><td>".round($json["progress"]["completion"],2)."%</td></tr>");
			echo("<tr><td>print time</td><td>".$json["progress"]["printTime"]." Seconds</td></tr>");
			echo("<tr><td>print time left</td><td>".$json["progress"]["printTimeLeft"]." Seconds</td></tr>");

			$cnt--;
			echo("</table>");
			echo("<iframe height='135' width='240' src='$url/webcam/?action=stream'></iframe>");
			echo("<br><br>");
		}
		//echo("free your printer after you've taken out your print!</div>");
		echo("<a href='all_jobs.php'>back to job control</a>");
	?>
</center>
<br><br><br>
</body>
</html>
