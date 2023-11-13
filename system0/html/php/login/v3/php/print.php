<!DOCTYPE html>
<html>
	<?php
	// Initialize the session
	session_start();
	include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
	include "config.php";
	require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
	include "queue.php";
	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
	    header("location: login.php");
	    exit;
	}
	$username=htmlspecialchars($_SESSION["username"]);
	?>

	<?php 
		$color=$_SESSION["color"]; 
		include "/var/www/html/system0/html/php/login/v3/components.php";
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
		test_queue($link);
	?>

	<?php $userid=$_SESSION["id"]; ?>
	<?php echo(" <body style='background-color:$color'> ");?>
	<div id="content"></div>

	<head>
	  <title>Datei drucken</title>
	
	</head>

	<body>
		<?php
		if(isset($_POST["printer"]))
		{
			//echo($_POST["printer"]);
			$status=0;
			$free=0;
			$url="";
			$apikey="";
			$printer_url="";
			$printer_id=htmlspecialchars($_POST["printer"]);
			if($printer_id=="queue")
			{
			 //send file to queue because no printer is ready!
			 	$sql="INSERT INTO queue (from_userid,filepath) VALUES (?,?)";
				//echo $sql;
		
				if(!empty($_FILES['file_upload']))
				{
					$ok_ft=array("gcode","");
					$unwanted_chr=[' ','(',')','/','\\','<','>',':',';','?','*','"','|','%'];
					$filetype = strtolower(pathinfo($_FILES['file_upload']['name'],PATHINFO_EXTENSION));
					$path = "/var/www/html/system0/html/user_files/$username/";
					$filename=basename( $_FILES['file_upload']['name']);
					$filename=str_replace($unwanted_chr,"_",$filename);
					$path = $path . $filename;

					//if(in_array($filetype,$unwanted_ft))
					if(!in_array($filetype,$ok_ft))
					{
						echo "Sorry, this file extensions is not allowed!";
						sys0_log("Could not upload file for ".$_SESSION["username"]." because of unknown file extension",$_SESSION["username"],"PRINT::UPLOAD::FILE::FAILED");//notes,username,type
					}
					else
					{
						if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $path)) {
							echo "<center>Success! The file ".  basename( $_FILES['file_upload']['name']). " has been uploaded</center>";
							echo("<center>Sending file to queue...</center>");
							
							$stmt = mysqli_prepare($link, $sql);	
							//echo ("test".mysqli_error($link));
							mysqli_stmt_bind_param($stmt, "is", $userid,$path);				
							mysqli_stmt_execute($stmt);

							echo("<center>File sent to queue.<br>system0 uploader done. Thank you!</center>");
							sys0_log("user ".$_SESSION["username"]." uploaded ".basename($path)." to the queue",$_SESSION["username"],"PRINT::UPLOAD::QUEUE");//notes,username,type
						}   
						else
						{
							echo "There was an error uploading the file, please try again! path:".$path;
						}
					}
					unset($_FILES['file']);
				}				 	
			}
			else
			{
				$sql="select printer_url, free, system_status,apikey,printer_url from printer where id=$printer_id";
				//echo $sql;
				$stmt = mysqli_prepare($link, $sql);					
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $url,$free,$status,$apikey,$printer_url);
				mysqli_stmt_fetch($stmt);	
				if($free!=1 or $status!=0)
				{
					echo("<center>Fehler! Der Drucker ist zur Zeit nicht verfügbar. Warte einen Moment oder versuche es mit einem anderen Drucker erneut. </center>");
					sys0_log("Could not start job for ".$_SESSION["username"]." with file ".basename($path)."",$_SESSION["username"],"PRINT::JOB::START::FAILED");//notes,username,type
					exit;
				}	
				if(!empty($_FILES['file_upload']))
				{
					$ok_ft=array("gcode","");
					$unwanted_chr=[' ','(',')','/','\\','<','>',':',';','?','*','"','|','%'];
					$filetype = strtolower(pathinfo($_FILES['file_upload']['name'],PATHINFO_EXTENSION));
					$path = "/var/www/html/system0/html/user_files/$username/";
					$filename=basename( $_FILES['file_upload']['name']);
					$filename=str_replace($unwanted_chr,"_",$filename);
					$path = $path . $filename;

					//if(in_array($filetype,$unwanted_ft))
					if(!in_array($filetype,$ok_ft))
					{
						echo "Das Dateiformat wird nicht unterstüzt. ";
						sys0_log("Could not upload file for ".$_SESSION["username"]." because of unknown file extension",$_SESSION["username"],"PRINT::UPLOAD::FILE::FAILED");//notes,username,type
					}
					else
					{
						if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $path)) {
							echo("<div class='alert alert-success' role='alert'>Erfolg! Die Datei ".  basename( $_FILES['file_upload']['name']). " wurde hochgeladen.</div>");
							echo("<div class='alert alert-success' role='alert'>Datei wird an den 3D-Drucker gesendet...</div>");
							exec('curl -k -H "X-Api-Key: '.$apikey.'" -F "select=true" -F "print=true" -F "file=@'.$path.'" "'.$printer_url.'/api/files/local" > /var/www/html/system0/html/user_files/'.$username.'/json.json');
							//file is on printer and ready to be printed
							$userid=$_SESSION["id"];
							echo("<div class='alert alert-success' role='alert'>Datei gesendet und Auftrag wurde gestartet.</div>");
							sys0_log("user ".$_SESSION["username"]." uploaded ".basename($path)." to printer ".$_POST["printer"]."",$_SESSION["username"],"PRINT::UPLOAD::PRINTER");//notes,username,type
							$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
							$json=json_decode($fg,true);
							if($json['effectivePrint']==false or $json["effectiveSelect"]==false)
							{
								echo("<div class='alert alert-danger' role='alert'>Ein Fehler ist aufgetreten und der Vorgagn konnte nicht gestartet werden. Warte einen Moment und versuche es dann erneut.</div>");
								sys0_log("Could not start job for ".$_SESSION["username"]."with file ".basename($path)."",$_SESSION["username"],"PRINT::JOB::START::FAILED");//notes,username,type
							}
							else
							{
								$sql="update printer set free=0, printing=1, used_by_userid=$userid where id=$printer_id";
								$stmt = mysqli_prepare($link, $sql);					
								mysqli_stmt_execute($stmt);
							}
						}
						else
						{
							echo("<div class='alert alert-danger' role='alert'>Ein Fehler beim Uploaden der Datei ist aufgetreten! Versuche es erneut! </div>");
						}
					}
					unset($_FILES['file']);
				}	
			}
		}
	
	?>
	
		<div class="container d-flex align-items-center justify-content-center">
			<div class="text-center mt-5">
				
				<h1>datei drucken</h1>
				<form class="mt-5" enctype="multipart/form-data" method="POST" action="">
					<div class="form-group">
						<div class="custom-file">

							<label for="file_upload" class="form-label">Zu druckende Datei</label>
							<input type="file" class="form-control" type="file" name="file_upload" required>  
						</div>
					</div>
					<br><br>
					<div class="form-group">
						<label class="my-3" for="printer">Printer to print</label>
						<select class="form-control selector" name="printer" required>
							<!-- PHP to retrieve printers -->
							<?php
							//get number of printers
							$num_of_printers=0;
							$sql="select count(*) from printer";
							$stmt = mysqli_prepare($link, $sql);
							mysqli_stmt_execute($stmt);
							mysqli_stmt_store_result($stmt);
							mysqli_stmt_bind_result($stmt, $num_of_printers);
							mysqli_stmt_fetch($stmt);
							//echo("test1:".$num_of_printers);
							$last_id=0;
							$printers_av=0;
							while($num_of_printers!=0)
							{
								$id=0;
								$sql="Select id from printer where id>$last_id and free=1 order by id";
								//echo $sql;
								$stmt = mysqli_prepare($link, $sql);
								mysqli_stmt_execute($stmt);
								mysqli_stmt_store_result($stmt);
								mysqli_stmt_bind_result($stmt, $id);
								mysqli_stmt_fetch($stmt);
								if($id!=0 && $id!=$last_id)
								{
									echo("<option printer='$id' value='$id'>Printer $id</option>");
									$printers_av++;
								}
								$last_id=$id;
								$num_of_printers--;
							}
							if($printers_av==0)
								echo("<option printer='queue' value='queue'>No printer available (send to queue)</option>");
							?>
						</select>
					</div>
					<br><br>
					<input type="submit" class="btn btn-dark mb-5" value="Print file">
				</form>
			</div>
		</div>
	<div id="footer"></div>

</body>

</html>
