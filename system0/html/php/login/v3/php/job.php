<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";		//waf
require_once "/var/www/html/system0/html/php/login/v3/log/log.php";	//logging functions
include "config.php";							//db config & login
include "queue.php";							//job queue system
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
$id=$_SESSION["id"];
?>
<?php
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>

<script src="/system0/html/php/login/v3/js/load_page.js"></script>
<script>
function load_admin()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/admin_page.html");
	});
	$(document).ready(function(){
   	$('#footer').load("/system0/html/php/login/v3/html/footer.html");
	});
}
function load_user()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/user_page.html");
	});
	$(document).ready(function(){
   	$('#footer').load("/system0/html/php/login/v3/html/footer.html");
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

<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Your jobs</title>
  
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
	    		<div class="mb-2"
				<h1>Meine laufenden Aufträge</h1>
			</div>
			<?php
				if(isset($_POST['free']))//free a printer
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
						echo("<div class='alert alert-danger' role='alert'>Falsche userid!</div>");
					}
					else
					{
						$sql="update printer set free=1,printing=0,cancel=0 ,used_by_userid=0 where id=$printer_id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						sys0_log("User ".$_SESSION["username"]." freed printer ".$_GET["free"]."",$_SESSION["username"],"JOB::PRINTERCTRL::FREE");//notes,username,type
					}
				}
				if(isset($_POST['cancel']))//cancel a job
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
					if($cnt!=$_SESSION['id'])
					{
						echo("<div class='alert alert-danger' role='alert'>Falsche userid!</div>");
					}
					else
					{
						exec("curl -k -H \"X-Api-Key: $apikey\" -H \"Content-Type: application/json\" --data '{\"command\":\"cancel\"}' \"$printer_url/api/job\" > /var/www/html/system0/html/user_files/$username/json.json");
						$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
						$json=json_decode($fg,true);
						if($json["error"]!="")
						{
							echo("<br><div class='alert alert-danger' role='alert'>Ein Fehler ist aufgetreten und der Druckvorgang wurde abgebrochen ! <br>Der Fehler befindet sich beim Drucker. Bitte warte einen moment und versuche es dann erneut!</div><br>");
							sys0_log("User ".$_SESSION["username"]." could not cancel job on printer; error: ".$json["error"]."".$_GET["free"]."",$_SESSION["username"],"JOB::PRINTERCTRL::CANCEL::FAILED");//notes,username,type
						}
						else
						{
							$sql="update printer set cancel=1 where id=$printer_id";
							$stmt = mysqli_prepare($link, $sql);					
							mysqli_stmt_execute($stmt);
							sys0_log("User ".$_SESSION["username"]." canceled job on printer ".$_GET["free"]."",$_SESSION["username"],"JOB::PRINTERCTRL::CANCEL");//notes,username,type
						}
					}
				}
				if(isset($_POST["remove"]))//remove a job from queue
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
					if($quserid==$userid){
					
					$sql="delete from queue where id=$queueid";
					$stmt = mysqli_prepare($link, $sql);				
					mysqli_stmt_execute($stmt);
					sys0_log("User ".$_SESSION["username"]." removed file #".$_GET["remove"]." from queue",$_SESSION["username"],"JOB::QUEUECTRL::REMOVE");//notes,username,type
					}
				
				}
				$cnt=0;
				$url="";
				$apikey="";
				$sql="select count(*) from printer where used_by_userid=$id";//how many jobs does the user have? show all running jobs of the user
				$stmt = mysqli_prepare($link, $sql);					
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $cnt);
				mysqli_stmt_fetch($stmt);	
				//echo($cnt);
				echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th scope='col'>Drucker</th><th scope='col'>Datei</th><th scope='col'>Fertigstellung</th><th scope='col'>Abbrechen</th><th scope='col'>Informationen</th></tr></thead><tbody>");
				$last_id=0;
				while($cnt!=0)
				{
					$sql="select id,printer_url,apikey,cancel from printer where used_by_userid=$id and id>$last_id order by id";
					$cancel=0;
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $printer_id,$url,$apikey,$cancel);
					mysqli_stmt_fetch($stmt);
					//echo("curl $url/api/job?apikey=$apikey > /var/www/html/system0/html/user_files/$username/json.json");
					exec("curl --max-time 10 $url/api/job?apikey=$apikey > /var/www/html/system0/html/user_files/$username/json.json");
					$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
					$json=json_decode($fg,true);
					//var_dump($json);
					//echo($fg);
					$last_id=$printer_id;
					$progress=(int) $json['progress']['completion'];
					if($progress<0)
						$progress=-$progress;
					$file=$json['job']['file']['name'];
					if($progress==100)
						echo("<tr><td>$printer_id</td><td>$file</td><td>$progress</td><td><form method='POST' action='?free=$printer_id'><input type='submit' value='free'  name='free'> </form></td><td>Job already finished</td><td><form method='POST' action='job_info.php'><input type='submit' value='detailes'> </form></td></tr>");
					else if($cancel==1)
						echo("<tr><td>$printer_id</td><td>$file</td><td>cancelled</td><td><form method='POST' action='?free=$printer_id'><input type='submit' value='free'  name='free'> </form></td><td>Job cancelled</td><td><form method='POST' action='job_info.php'><input type='submit' value='detailes'> </form></td></tr>");
					else
						echo("<tr><td>$printer_id</td><td>$file</td><td>$progress%</td><td>Job still running</td><td><form method='POST' action='?cancel=$printer_id'><input type='submit' value='cancel'  name='cancel'> </form></td><td><form method='POST' action='job_info.php'><input type='submit' value='detailes'> </form></td></tr>");
		 			
					$cnt--;
				}
				echo("</tbody></table></div></div></div></div>");
				echo("<p class'mt-2'>Bitte entferne deinen Druck aus dem Drucker nach dem Druckvorgang.</p>");
			?>
			<br>
	    		<br>
			<h1 class="mt-5 mb-2">Meine Aufträge in der Warteschlange</h1>
			<?php
				$userid=$_SESSION["id"];	//show users job in queue
				$cnt=0;
				$filepath="";
				$sql="select count(*) from queue where from_userid=$userid";
				$stmt = mysqli_prepare($link, $sql);					
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $cnt);
				mysqli_stmt_fetch($stmt);	
				//echo($cnt);
				echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Datei</th><th>Aus der Warteschlange entfernen</th></tr></thead><tbody>");
				$last_id=0;
				while($cnt!=0)
				{
					$sql="select id,filepath from queue where from_userid=$userid and id>$last_id order by id";
					$cancel=0;
					$stmt = mysqli_prepare($link, $sql);	
					echo mysqli_error($link);				
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $queue_id,$filepath);
					mysqli_stmt_fetch($stmt);
					$filepath=basename($filepath);
					$last_id=$queue_id;
					echo("<tr><td>$filepath</td><td><form method='POST' action='?remove=$queue_id'><input type='submit' value='remove'  name='remove'> </form></td></tr>");
		 			
					$cnt--;
				}
				echo("</tbody></table></div></div></div></div>");
				echo("Es kann einen Moment dauern bis der Druckvorgang startet, da der Drucker nach jedem Druck abkühlen muss. ");
			?>
			<?php
				test_queue($link); //test for a free printer. If any printe ris free and there are jobs in queue, push job to printer
			?>
			</div>
    		</div>
	</div>
	<div id="footer"></div>
	
</body>

</html>
