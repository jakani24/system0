<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
include "queue.php";
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
  <title>Alle Drucker</title>
  
</head>
<body>
	<div class="container mt-5">
		<div class="row justify-content-center">
	  	<div style="width: 100hh">
	    <!--  <h1>Alle Drucker</h1> -->
				<?php
					if(isset($_GET['free']))
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
					if(isset($_GET['cancel']))
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
					$is_free=0;
					//echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Drucker</th><th>Benutzer</th><th>Datei</th><th>Fortschritt</th><th>Freigeben</th><th>Druck abbrechen</th></tr></thead><tbody>");
					echo("<div class='container'><div class='row'>");					
					$last_id=0;					
					while($cnt!=0)
					{	
						echo("<div class='col-4' style='padding:5px'>");
						$userid=0;
						$sql="select free,id,printer_url,apikey,cancel,used_by_userid from printer where id>$last_id ORDER BY id";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $is_free,$printer_id,$url,$apikey,$cancel,$userid);
						mysqli_stmt_fetch($stmt);
						$last_id=$printer_id;

						if($is_free==0){
							//printer is printing
							exec("curl --max-time 10 $url/api/job?apikey=$apikey > /var/www/html/system0/html/user_files/$username/json.json");
							$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
							$json=json_decode($fg,true);
							
							
							$used_by_user="";
							$sql="select username from users where id=$userid";
							$stmt = mysqli_prepare($link, $sql);					
							mysqli_stmt_execute($stmt);
							mysqli_stmt_store_result($stmt);
							mysqli_stmt_bind_result($stmt, $used_by_user);
							mysqli_stmt_fetch($stmt);
							$username2=explode("@",$used_by_user);

							$progress=(int) $json['progress']['completion'];
							if($progress<0)
								$progress=-$progress;
							$file=$json['job']['file']['name'];
							if($progress==100){
									echo("<div class='card'>");
									echo("<div class='card-body'>");
									echo("<h5 class='card-title'>Drucker $printer_id</h5>");
									echo("</div>");
									echo("<div class='card-body'>");
									echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id'></iframe>");
									echo("<div class='progress'>");
									  echo("<div class='progress-bar' role='progressbar' style='width: $progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'>$progress%</div>");
									echo("</div>");
									echo("<table class='table table-borderless'>");
									echo("<thead>");
									echo("<tr><td>Status</td><td style='color:green'>Fertig</td></tr>");
									echo("<tr><td>Genutzt von</td><td>".$username2[0]."</td></tr>");
									echo("<tr><td>Erwartete Druckzeit</td><td>".round((round(intval($json["job"]["estimatedPrintTime"],0))/60),0)." Minuten</td></tr>");
									echo("<tr><td>Verbleibende Druckzeit</td><td>".round((intval($json["progress"]["printTimeLeft"])/60),0)." Minuten</td></tr>");
									echo("<tr><td>Vergangene Druckzeit</td><td>".round((intval($json["progress"]["printTime"])/60),0)." Minuten</td></tr>");
									echo("<tr><td>Datei</td><td>".substr($json["job"]["file"]["name"],0,20)."...</td></tr>");
									if($userid==$_SESSION["id"] or $_SESSION["role"]==="admin"){
										echo("<tr><td><a class='btn btn-success' href='overview.php?free=$printer_id'>Freigeben</a></td></tr>");
									}
									echo("</thead>");
									echo("</table>");
									echo("</div>");
									echo("</div>");
							}
							else if($cancel==1){
									echo("<div class='card'>");
									echo("<div class='card-body'>");
									echo("<h5 class='card-title'>Drucker $printer_id</h5>");
									echo("</div>");
									echo("<div class='card-body'>");
									echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id'></iframe>");
									echo("<div class='progress'>");
									  echo("<div class='progress-bar' role='progressbar' style='width: $progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'>$progress%</div>");
									echo("</div>");
									echo("<table class='table table-borderless'>");
									echo("<thead>");
									echo("<tr><td>Status</td><td style='color:red'>Druck Abgebrochen</td></tr>");
									echo("<tr><td>Genutzt von</td><td>".$username2[0]."</td></tr>");
									echo("<tr><td>Erwartete Druckzeit</td><td>".round((round(intval($json["job"]["estimatedPrintTime"],0))/60),0)." Minuten</td></tr>");
									echo("<tr><td>Verbleibende Druckzeit</td><td>".round((intval($json["progress"]["printTimeLeft"])/60),0)." Minuten</td></tr>");
									echo("<tr><td>Vergangene Druckzeit</td><td>".round((intval($json["progress"]["printTime"])/60),0)." Minuten</td></tr>");
									echo("<tr><td>Datei</td><td>".substr($json["job"]["file"]["name"],0,20)."...</td></tr>");
									if($useuserid==$_SESSION["id"] or $_SESSION["role"]==="admin"){
										echo("<tr><td><a class='btn btn-success' href='overview.php?free=$printer_id'>Freigeben</a></td></tr>");
									}
									echo("</thead>");
									echo("</table>");
									echo("</div>");
									echo("</div>");
							}								
							else{
									echo("<div class='card'>");
									echo("<div class='card-body'>");
									echo("<h5 class='card-title'>Drucker $printer_id</h5>");
									echo("</div>");
									echo("<div class='card-body'>");
									echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id'></iframe>");
									echo("<div class='progress'>");
									  echo("<div class='progress-bar' role='progressbar' style='width: $progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'>$progress%</div>");
									echo("</div>");
									echo("<table class='table table-borderless'>");
									echo("<thead>");
									echo("<tr><td>Status</td><td style='color:orange'>Drucken</td></tr>");
									echo("<tr><td>Genutzt von</td><td>".$username2[0]."</td></tr>");
									echo("<tr><td>Erwartete Druckzeit</td><td>".round((round(intval($json["job"]["estimatedPrintTime"],0))/60),0)." Minuten</td></tr>");
									echo("<tr><td>Verbleibende Druckzeit</td><td>".round((intval($json["progress"]["printTimeLeft"])/60),0)." Minuten</td></tr>");
									echo("<tr><td>Vergangene Druckzeit</td><td>".round((intval($json["progress"]["printTime"])/60),0)." Minuten</td></tr>");
									echo("<tr><td>Datei</td><td>".substr($json["job"]["file"]["name"],0,20)."...</td></tr>");
									if($userid==$_SESSION["id"] or $_SESSION["role"]==="admin"){								
										echo("<tr><td><a class='btn btn-danger' href='overview.php?cancel=$printer_id'>Abbrechen</a></td></tr>");
									}
									echo("</thead>");
									echo("</table>");
									echo("</div>");
									echo("</div>");
							}
				 		}else{
							//printer is free
							echo("<div class='card'>");
							echo("<div class='card-body'>");
							echo("<h5 class='card-title'>Drucker $printer_id</h5>");
							echo("</div>");
							echo("<div class='card-body'>");
							echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id'></iframe>");
							echo("<table class='table table-borderless'>");
							echo("<thead>");
							echo("<tr><td>Status</td><td style='color:green'>Bereit</td></tr>");
							echo("<tr><td><a class='btn btn-dark' href='print.php?preselect=$printer_id'>Drucken</a></td></tr>");
							echo("</thead>");
							echo("</table>");
							echo("</div>");
							echo("</div>");
						}
						$cnt--;
						echo("</div>");
					}
					echo("</div></div>");
					
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
