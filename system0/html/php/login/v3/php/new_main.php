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
			* {
		  box-sizing: border-box;
		}	
	</style>
	<?php $color=$_SESSION["color"]; ?>
	<?php echo(" <body style='background-color:$color'> ");?>
	<!-- title and so on -->
		
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <a class="navbar-brand" href="#">Navbar</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>
		
		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    <ul class="navbar-nav">
		      <li class="nav-item dropdown mr-auto">
		        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          Dropdown
		        </a>
		        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		          <a class="dropdown-item" href="#">Action</a>
		          <a class="dropdown-item" href="#">Another action</a>
		          <div class="dropdown-divider"></div>
		          <a class="dropdown-item" href="#">Something else here</a>
		        </div>
		      </li>
		      <li class="nav-item" ml-auto>
						<a href="#Settings" class="btn" role="button"><i class="fa-solid fa-gear"></i></a>
            <a href="#logout" class="btn" role="button"><i class="fa-solid fa-right-from-bracket"></i></a>	
					</li>
		    </ul>
		  </div>
		</nav>

		<!-- buttons for ctrl -->

		<div class="d-flex flex-row">
  		<button type="button" href="print.php" class="btn btn-primary p-2">Print a file</button>
			<button type="button" href="print.php" class="btn btn-primary p-2">Print a file</button>
		</div>
		
		<!-- your jobs -->
		<div class="job_ctrl" style="width:100%">
		
			<div class="main_job_ctrl" style="overflow-x: auto;width:50%;float:left">
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
							echo("<p>Wrong userid!</p>");
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
							echo("<p>Wrong userid!</p>");
						}
						else
						{
							exec("curl -k -H \"X-Api-Key: $apikey\" -H \"Content-Type: application/json\" --data '{\"command\":\"cancel\"}' \"$printer_url/api/job\" > /var/www/html/system0/html/user_files/$username/json.json");
							$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
							$json=json_decode($fg,true);
							if($json["error"]!="")
							{
								echo("<center><br><br><p style='color:red'>There was an error canceling the print job !<br>The error is on our machine or printer, so please wait and trie again in some time!<br></p></center>");
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
					//echo '<div style="overflow-x: auto;">';
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
				 			</tbody>");
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
					echo("<p>free your printer after you've taken out your print!</p>");
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
					echo("<table class="table"><thead><tr><th>file</th><th>remove from queue</th></tr></thead><tbody>");
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
					echo("<p>It might take some time for your job in queue to start after a printer is free.<br>(After every print the printer has to cool down)</p>");
				?>
				<?php
					test_queue($link); //test for a free printer. If any printe ris free and there are jobs in queue, push job to printer
				?>				
			</div>
			<!-- job detailes -->
			<div class="main_job_info" style="overflow-x: auto;width:50%;float:left;">
				<iframe src="job_info_for_new_system.php" style="width:100%;100%:auto;border:0px;"></iframe>
			</div>
		</div>
	</body>


</html>
