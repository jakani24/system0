<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
include "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
$id=$_SESSION["id"];
?>



<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php echo(" <body style='background-color:$color'> ");?>

<head>
  <title>Your jobs</title>
  
</head>
<body>
	<div class="d-flex justify-content-center align-items-center">
		<div class="center">
		      	

			<!-- <button type="button" class="btn btn-dark btn-lg" onclick="location.href = 'job_info_for_new_system.php';">Reload</button>-->

			<br>
			<div class="container">
				<?php
					$cnt=0;
					$url="";
					$apikey="";
					$printer_id=0;
					$sql="select count(*) from printer where used_by_userid=$id";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cnt);
					mysqli_stmt_fetch($stmt);	
					//echo($cnt);
					echo '<div style="overflow-x: auto;">';
					while($cnt!=0)
					{
						echo("<table class='table'><thead><tr><th>Keyword</th><th>value</th></tr></thead><tbody>");
						$sql="select id,printer_url,apikey from printer where used_by_userid=$id AND id>$printer_id order by id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $printer_id,$url,$apikey);
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
						echo("<tr><td>Drucker</td><td>$printer_id</td></tr>");
						echo("<tr><td>Erwartete Druckzeit</td><td>".round($json["job"]["estimatedPrintTime"],0)." Sekunden</td></tr>");
						echo("<tr><td>Druckzeit</td><td>".$json["progress"]["printTime"]." Seconds</td></tr>");
						echo("<tr><td>Verbleibende Druckzeit</td><td>".$json["progress"]["printTimeLeft"]." Sekunden</td></tr>");
						echo("<tr><td>Datei</td><td>".$json["job"]["file"]["name"]."</td></tr>");
						echo("<tr><td>Dateigrösse</td><td>".$json["job"]["file"]["size"]." Bytes</td></tr>");
						echo("<tr><td>Fortschritt</td><td>".round($json["progress"]["completion"],2)."%</td></tr>");

						$cnt--;
						echo("</tbody></table>");
						echo("<iframe height='auto' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id'></iframe>");
						echo("<br><br>");
					}
					//echo("free your printer after you've taken out your print!</div>");
					//echo("<a href='job.php'>back to job control</a>");
				?>
			</div>
		</div>
	</div>
<br><br><br>
</body>
<!-- curl --silent --show-error http://127.0.0.1:4040/api/tunnels | sed -nE 's/.*public_url":"https:..([^"]*).*/\1/p' -->
</html>
