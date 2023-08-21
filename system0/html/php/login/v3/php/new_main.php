<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";		//waf
require_once "/var/www/html/system0/html/php/login/v3/log/log.php";	//logging functions
include "config.php";							//db config & login
include "queue.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
$id=$_SESSION["id"];
?>

	<head>
		<title>System0 web page</title>
		<?php
			 include "/var/www/html/system0/html/php/login/v3/components.php";
		?>
	</head>
	<!-- temporary -->
	
	<?php $color=$_SESSION["color"]; ?>
	<?php echo(" <body style='background-color:$color' class='d-flex flex-column'> ");?>
	<!-- title and so on -->
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
		test_queue($link);
	?>
		<div id="content"></div>
		<!-- buttons for ctrl -->
		<div class="flex-grow-1 d-flex justify-content-center align-items-center">
			<div class="d-flex">
				<div class="m-3">
					<button type="button" href="print.php" class="btn btn-primary btn-lg" onclick="location.href = 'print.php';">Print a file</button>
				</div>
				<div class="m-3">
					<button type="button" href="print.php" class="btn btn-primary btn-lg" onclick="location.href = 'print.php';">Print a file</button>
				</div>
			</div>
		</div>
		<!-- your jobs -->

		<div class="d-flex flex-grow-2 h-75">
			<div style="flex-grow: 2;">
				<div class="job p-2 big">
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
								echo("<div class='alert alert-danger' role='alert'>Wrong userid!</div>");
							}
							else
							{
								$sql="update printer set free=1,printing=0,cancel=0 ,used_by_userid=0 where id=1";
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
								echo("<div class='alert alert-danger' role='alert'>Wrong userid!</div>");
							}
							else
							{
								exec("curl -k -H \"X-Api-Key: $apikey\" -H \"Content-Type: application/json\" --data '{\"command\":\"cancel\"}' \"$printer_url/api/job\" > /var/www/html/system0/html/user_files/$username/json.json");
								$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
								$json=json_decode($fg,true);
								if($json["error"]!="")
								{
									echo("<div class='alert alert-danger' role='alert'>There was an error canceling the print job !<br>The error is on our machine or printer, so please wait and trie again in some time!<br></div>");
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
						$cnt_of_job=$cnt;
						//echo($cnt);
						//echo '<div style="overflow-x: auto;">';
						if($count!=0){
							echo(
								"<table class='table'>
					 				<thead>
		    							<tr>
			      							<th scope='col'>Printer</th>
			      							<th scope='col'>File</th>
			      							<th scope='col'>Completion</th>
			      							<th scope='col'>Free</th>
									 	<th scope='col'>Cancel Print</th>
								 		<th scope='col'>Details</th>
		    							</tr>
		  							</thead>
						 			<tbody>");
							while($cnt!=0)
							{
								$sql="select id,printer_url,apikey,cancel from printer where used_by_userid=$id";
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
									echo("<tr><td>$id</td><td>$file</td><td>$progress%</td><td><form method='POST' action='?free=$id'><input type='submit' value='free'  name='free'> </form></td><td>Job already finished</td><td><form method='POST' action='new_main.php'><input type='submit' value='detailes'> </form></td></tr>");
								else if($cancel==1)
									echo("<tr><td>$id</td><td>$file</td><td>cancelled</td><td><form method='POST' action='?free=$id'><input type='submit' value='free'  name='free'> </form></td><td>Job cancelled</td><td><form method='POST' action='new_main.php'><input type='submit' value='detailes'> </form></td></tr>");
								else
									echo("<tr><td>$id</td><td>$file</td><td>$progress%</td><td>Job still running</td><td><form method='POST' action='?cancel=$id'><input type='submit' value='cancel'  name='cancel'> </form></td><td><form method='POST' action='new_main.php'><input type='submit' value='detailes'> </form></td></tr>");
					 			
								$cnt--;
							}
							echo("</tbody></table>");
							//echo("</div>");
							echo("<div class='alert alert-primary' role='alert'>free your printer after you've taken out your print!</div>");
						}
						else
						{
							echo("<div class='alert alert-primary' role='alert'>running jobs and queue items will be displayed here if any are available.</div>");
						}
					?>	
					<!-- list queue -->
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
						//echo '<div style="overflow-x: auto;">';
						if($count!=0){
							echo("<table class='table'><thead><tr><th>file</th><th>remove from queue</th></tr></thead><tbody>");
							while($cnt!=0)
							{
								$sql="select id,filepath from queue where from_userid=$userid";
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
							echo("</tbody></table>");	
							//echo("</div>");
							echo("<div class='alert alert-primary' role='alert'>It might take some time for your job in queue to start after a printer is free.<br>(After every print the printer has to cool down)</div>");
						}
					?>
					<?php
						test_queue($link); //test for a free printer. If any printe ris free and there are jobs in queue, push job to printer
					?>				
				</div>
			</div>
			<!-- job detailes -->
			<div style="flex-grow: 1;">
				<div class="p-2 small">
					<?php
						if($cnt_of_job!=0)
						{
							//echo('<div class="p-2 small">');
							echo('<iframe src="job_info_for_new_system.php" width="100%" height="100%" frameborder="0"></iframe>');
							//echo('</div>');
						}
						else
							echo("<div class='alert alert-primary' role='alert'>Info of your jobs will be displayed here if any are available.</div>");
					?>
			</div>
			</div>
		</div>
	</body>


</html>
