<!DOCTYPE html>
<html>
<head>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
include "queue.php";
$role=$_SESSION["role"];
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
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
	echo "<script type='text/javascript' >load_user()</script>";
?>
<?php $color=$_SESSION["color"]; ?>
<?php 
	function seconds_to_time($seconds) {
	    // Convert seconds to hours
	    $hours = floor($seconds / 3600);

	    // Convert remaining seconds to minutes
	    $minutes = floor(($seconds % 3600) / 60);

	    // Return the result as an associative array
	    //return array(
	//	"hours" => $hours,
	//	"minutes" => $minutes
	 //   );
		if($hours!=0){
			if($hours==1)
				return sprintf("%d Stunde %d Minuten", $hours, $minutes);
			else
				return sprintf("%d Stunden %d Minuten", $hours, $minutes);
		}
		else
			return sprintf("%d Minuten", $minutes);
	}
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
	if(!isset($_SESSION["rid"]))
		$_SESSION["rid"]=0;
	$_SESSION["rid"]++;
?>

  <title>Alle Drucker</title>
  
</head>
<body>
	<div id="content"></div>
	<div class="container mt-5">
		<div class="row justify-content-center">
		<div style="width: 100hh;min-height:95vh">
	    <!--  <h1>Alle Drucker</h1> -->
				<?php
					if(isset($_GET['free'])&&$_GET["rid"]==($_SESSION["rid"]-1))
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
					if(isset($_GET['remove_queue'])&&$_GET["rid"]==($_SESSION["rid"]-1))
					{
						$id=htmlspecialchars($_GET['remove_queue']);
						$sql="delete from queue where id=$id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
					}
					if(isset($_GET['cancel'])&&$_GET["rid"]==($_SESSION["rid"]-1))
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
							//$sql="update printer set system_status=1 where id=$printer_id";
							//$stmt = mysqli_prepare($link, $sql);					
							//mysqli_stmt_execute($stmt);
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
					if(isset($_GET["private"]))
						$sql="select count(*) from printer where used_by_userid=".$_SESSION["id"];
					else
						$sql="select count(*) from printer";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cnt);
					mysqli_stmt_fetch($stmt);	
					//echo($cnt);
					$is_free=0;
					echo("<div class='container'><div class='row'>");
					echo("<div style='padding:5px'>");
						if(isset($_GET["private"]))
							echo("<a class='btn btn-dark' href='overview.php'>Alle Drucker anzeigen</a>");
						else
							echo("<a class='btn btn-dark' href='overview.php?private'>Nur eigene Aufträge anzeigen</a>");
					echo("</div>");					
					$last_id=0;		
					$system_status=0;	
					$rotation=0;
					while($cnt!=0)
					{	
						if(isset($_SESSION["mobile_view"]))
							echo("<div class='col-12' style='padding:5px'>");
						else
							echo("<div class='col-4' style='padding:5px'>");
						$userid=0;
						if(isset($_GET["private"]))
							$sql="select rotation,free,id,printer_url,apikey,cancel,used_by_userid,system_status from printer where id>$last_id and used_by_userid=".$_SESSION["id"]." ORDER BY id";
						else
							$sql="select rotation,free,id,printer_url,apikey,cancel,used_by_userid,system_status from printer where id>$last_id ORDER BY id";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $rotation,$is_free,$printer_id,$url,$apikey,$cancel,$userid,$system_status);
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
									$print_time=seconds_to_time(intval($json["progress"]["printTime"]));
									$print_time_left=seconds_to_time(intval($json["progress"]["printTimeLeft"]));
									$print_time_total=seconds_to_time(intval($json["job"]["estimatedPrintTime"]));

									echo("div class='d-flex flex-wrap justify-content-center'>");
									echo("<div class='card'>");
									echo("<div class='card-body'>");
									echo("<h5 class='card-title'>Drucker $printer_id</h5>");
									echo("</div>");
									echo("<div class='card-body'>");
									echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id&username=".$_SESSION["username"]."&url=$url&rotation=$rotation'></iframe>");
									echo("<div class='progress'>");
									echo("<div class='progress-bar' role='progressbar' style='width: $progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'>$progress%</div>");
									echo("</div>");
									echo("<table class='table table-borderless'>");
									echo("<thead>");
									echo("<tr><td>Status</td><td style='color:green'>Fertig</td></tr>");
									echo("<tr><td>Genutzt von</td><td>".$username2[0]."</td></tr>");
									echo("<tr><td>Erwartete Druckzeit</td><td>$print_time_total</td></tr>");
									echo("<tr><td>Verbleibende Druckzeit</td><td>$print_time_left</td></tr>");
									echo("<tr><td>Vergangene Druckzeit</td><td>$print_time</td></tr>");
									echo("<tr><td>Datei</td><td>".substr($json["job"]["file"]["name"],0,20)."...</td></tr>");
									if($userid==$_SESSION["id"] or $role[3]==="1"){
										echo("<tr><td><a class='btn btn-success' href='overview.php?free=$printer_id&rid=".$_SESSION["rid"]."'>Freigeben</a></td></tr>");
									}
									echo("</thead>");
									echo("</table>");
									echo("</div>");
									echo("</div>");
							}
							else if($cancel==1){
									$print_time=seconds_to_time(intval($json["progress"]["printTime"]));
									$print_time_left=seconds_to_time(intval($json["progress"]["printTimeLeft"]));
									$print_time_total=seconds_to_time(intval($json["job"]["estimatedPrintTime"]));
									echo("<div class='card'>");
									echo("<div class='card-body'>");
									echo("<h5 class='card-title'>Drucker $printer_id</h5>");
									echo("</div>");
									echo("<div class='card-body'>");
									echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id&username=".$_SESSION["username"]."&url=$url&rotation=$rotation'></iframe>");
									echo("<div class='progress'>");
									  echo("<div class='progress-bar' role='progressbar' style='width: $progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'>$progress%</div>");
									echo("</div>");
									echo("<table class='table table-borderless'>");
									echo("<thead>");
									//if($system_status==0)
										echo("<tr><td>Status</td><td style='color:red'>Druck Abgebrochen</td></tr>");
									//else
									//	echo("<tr><td>Status</td><td style='color:red'>Fehler</td></tr>");
									echo("<tr><td>Genutzt von</td><td>".$username2[0]."</td></tr>");
									echo("<tr><td>Erwartete Druckzeit</td><td>$print_time_total</td></tr>");
									echo("<tr><td>Verbleibende Druckzeit</td><td>$print_time_left</td></tr>");
									echo("<tr><td>Vergangene Druckzeit</td><td>$print_time</td></tr>");
									echo("<tr><td>Datei</td><td>".substr($json["job"]["file"]["name"],0,20)."...</td></tr>");
									if($useuserid==$_SESSION["id"] or $role[3]=="1"){
										echo("<tr><td><a class='btn btn-success' href='overview.php?free=$printer_id&rid=".$_SESSION["rid"]."'>Freigeben</a></td></tr>");
									}
									echo("</thead>");
									echo("</table>");
									echo("</div>");
									echo("</div>");
							}								
							else{
									$print_time=seconds_to_time(intval($json["progress"]["printTime"]));
									$print_time_left=seconds_to_time(intval($json["progress"]["printTimeLeft"]));
									$print_time_total=seconds_to_time(intval($json["job"]["estimatedPrintTime"]));
									//echo("aaaaaaaaaaaa".$json["progress"]["estimatedPrintTime"]);
									echo("<div class='card'>");
									echo("<div class='card-body'>");
									echo("<h5 class='card-title'>Drucker $printer_id</h5>");
									echo("</div>");
									echo("<div class='card-body'>");
									echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id&username=".$_SESSION["username"]."&url=$url&rotation=$rotation'></iframe>");
									echo("<div class='progress'>");
									echo("<div class='progress-bar' role='progressbar' style='width: $progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'>$progress%</div>");
									echo("</div>");
									echo("<table class='table table-borderless'>");
									echo("<thead>");
									echo("<tr><td>Status</td><td style='color:orange'>Drucken</td></tr>");
									echo("<tr><td>Genutzt von</td><td>".$username2[0]."</td></tr>");
									echo("<tr><td>Erwartete Druckzeit</td><td>$print_time_total</td></tr>");
									echo("<tr><td>Verbleibende Druckzeit</td><td>$print_time_left</td></tr>");
									echo("<tr><td>Vergangene Druckzeit</td><td>$print_time</td></tr>");
									echo("<tr><td>Datei</td><td>".substr($json["job"]["file"]["name"],0,20)."...</td></tr>");
									if($userid==$_SESSION["id"] or $role[3]==="1"){								
										echo("<tr><td><a class='btn btn-danger' href='overview.php?cancel=$printer_id&rid=".$_SESSION["rid"]."'>Abbrechen</a></td></tr>");
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
							echo("<iframe height='230px' scrolling='no' width='100%' src='/system0/html/php/login/v3/php/webcam.php?printer_id=$printer_id&username=".$_SESSION["username"]."&url=$url&rotation=$rotation'></iframe>");
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
	<!-- We currently do not show the queue -->
	<div style="width: 100hh">
	<center><h3>Warteschlange</h3></center>
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
		echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Datei</th><th>Drucken auf Drucker</th><th>aus der Warteschlange entfernen</th></tr></thead><tbody>");
		$last_id=0;
		$form_userid=0;
		$print_on=0;
		while($cnt!=0)
		{
			$sql="select id,filepath,from_userid,print_on from queue where id>$last_id order by id";
			$cancel=0;
			$stmt = mysqli_prepare($link, $sql);	
			echo mysqli_error($link);				
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $queue_id,$filepath,$from_userid,$print_on);
			mysqli_stmt_fetch($stmt);
			$filepath=basename($filepath);
			$last_id=$queue_id;
			echo("<tr><td>$filepath</td>");
			if($print_on==-1)
				echo("<td>Erster verfügbarer Drucker</td>");
			else
				echo("<td>$print_on</td>");
			if($_SESSION["role"][3]==="1" or $_SESSION["id"]==$from_userid)
				echo("<td><form method='POST' action='?remove_queue=$queue_id&rid=".$_SESSION["rid"]."'><button type='submit' value='remove'  name='remove' class='btn btn-danger'>Löschen</button></form></td></tr>");
 			
			$cnt--;
		}
		echo("</tbody></table></div></div></div></div>");
	?>
	<br><br>
	</div>
	<div id="footer"></div>
</body>

</html>
