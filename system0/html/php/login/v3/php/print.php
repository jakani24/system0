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
		   	$('#content').load("/system0/html/php/login/v3/html/admin_page.php");
			});
			$(document).ready(function(){
   		$('#footer').load("/system0/html/php/login/v3/html/footer.html");
		});
		}
		function load_user()
		{
			$(document).ready(function(){
		   	$('#content').load("/system0/html/php/login/v3/html/user_page.php");
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
				if(isset($_GET["cloudprint"])){
					$path = "/var/www/html/system0/html/user_files/$username/".$_GET["cloudprint"];
					$stmt = mysqli_prepare($link, $sql);	
					mysqli_stmt_bind_param($stmt, "is", $userid,$path);				
					mysqli_stmt_execute($stmt);

					echo("<center>File sent to queue.<br>system0 uploader done. Thank you!</center>");
					sys0_log("user ".$_SESSION["username"]." uploaded ".basename($path)." to the queue",$_SESSION["username"],"PRINT::UPLOAD::QUEUE");

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
						//check if print key is valid:
						$print_key=htmlspecialchars($_POST["print_key"]);
						$sql="SELECT id from print_key where print_key='$print_key'";
						$stmt = mysqli_prepare($link, $sql);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
							
						//if(mysqli_stmt_num_rows($stmt) == 1){ turned off because user does not need to have a printer key
						if(true){
						mysqli_stmt_close($stmt);
			
						
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
									echo("<div class='alert alert-danger' role='alert'>Ein Fehler ist aufgetreten und der Vorgang konnte nicht gestartet werden. Warte einen Moment und versuche es dann erneut.</div>");
									sys0_log("Could not start job for ".$_SESSION["username"]."with file ".basename($path)."",$_SESSION["username"],"PRINT::JOB::START::FAILED");//notes,username,type
								}
								else
								{
									$sql="update printer set free=0, printing=1, used_by_userid=$userid where id=$printer_id";
									$stmt = mysqli_prepare($link, $sql);					
									mysqli_stmt_execute($stmt);
									//delete printer key:
									$sql="DELETE from print_key where print_key='$print_key'";
									$stmt = mysqli_prepare($link, $sql);
									mysqli_stmt_execute($stmt);	
									mysqli_stmt_close($stmt);	
								}
							}
							else
							{
								echo("<div class='alert alert-danger' role='alert'>Ein Fehler beim Uploaden der Datei ist aufgetreten! Versuche es erneut! </div>");
							}
						}
						else{
							echo("<div class='alert alert-danger' role='alert'>Der Druckschlüssel ist nicht gültig. Evtl. wurde er bereits benutzt. Versuche es erneut! </div>");
						}
					}
					unset($_FILES['file']);
				}
				if(isset($_GET["cloudprint"])){
					$path = "/var/www/html/system0/html/user_files/$username/".$_GET["cloudprint"];
					//check if print key is valid:
					$print_key=htmlspecialchars($_POST["print_key"]);
					$sql="SELECT id from print_key where print_key='$print_key'";
					$stmt = mysqli_prepare($link, $sql);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
						
					//if(mysqli_stmt_num_rows($stmt) == 1){ turned off because user does not need to have a printer key
					if(true){
					mysqli_stmt_close($stmt);
	
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
								echo("<div class='alert alert-danger' role='alert'>Ein Fehler ist aufgetreten und der Vorgang konnte nicht gestartet werden. Warte einen Moment und versuche es dann erneut.</div>");
								sys0_log("Could not start job for ".$_SESSION["username"]."with file ".basename($path)."",$_SESSION["username"],"PRINT::JOB::START::FAILED");//notes,username,type
							}
							else
							{
								$sql="update printer set free=0, printing=1, used_by_userid=$userid where id=$printer_id";
								$stmt = mysqli_prepare($link, $sql);					
								mysqli_stmt_execute($stmt);
								//delete printer key:
								$sql="DELETE from print_key where print_key='$print_key'";
								$stmt = mysqli_prepare($link, $sql);
								mysqli_stmt_execute($stmt);	
								mysqli_stmt_close($stmt);	
							}
					}
					else{
						echo("<div class='alert alert-danger' role='alert'>Der Druckschlüssel ist nicht gültig. Evtl. wurde er bereits benutzt. Versuche es erneut! </div>");
					}
				}	
			}
		}
	
	?>
	
		<div class="container d-flex align-items-center justify-content-center" style="height: 95vh;">
			<div class="text-center mt-5">
				
				<h1>Datei drucken</h1>
				<form class="mt-5" enctype="multipart/form-data" method="POST" action="">
					<?php if(!isset($_GET["cloudprint"])){
						echo ('<div class="form-group">');
							echo('<div class="custom-file">');

								echo('<label for="file_upload" class="form-label">Zu druckende Datei</label>');
								echo('<input type="file" class="form-control" type="file" name="file_upload" required accept=".gcode">  ');
							echo('</div>');
						echo('</div>');
					}
					else{
						echo ('<div class="form-group">');
							echo('<div class="custom-file">');

								echo("<p>Cloudfile: ".$_GET["cloudprint"]."</p>");
							echo('</div>');
						echo('</div>');
					}
					?>
					<br><br>
					<div class="form-group">
						<label class="my-3" for="printer">Druckerauswahl</label>
						<select class="form-control selector" name="printer" required>
							<!-- PHP to retrieve printers -->
							<?php
							//get number of printers
							$num_of_printers=0;
							$sql="select count(*) from printer where free=1";
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
					<!--<label class="my-3" for="print_key">Druckschlüssel (Kann im Sekretariat gekauft werden)</label>
					<input type="text" class="form-control text" id="print_key" name="print_key" placeholder="z.B. A3Rg4Hujkief"><br>-->
					<input type="submit" class="btn btn-dark mb-5" value="Datei drucken" onclick="show_loader();" id="button">
					<div class="d-flex align-items-center">
 					 <strong role="status" style="display:none" id="spinner">Hochladen...</strong>
 					 <div class="spinner-border ms-auto" aria-hidden="true" style="display:none" id="spinner2"></div>
					</div>
					
				</form>
			</div>
		</div>
		<br>
	<div id="footer"></div>
<script>
	function show_loader(){
		var spinner=document.getElementById("spinner");
		spinner.style.display="block";
		var spinner=document.getElementById("spinner2");
		spinner.style.display="block";
		var spinner=document.getElementById("button");
		spinner.style.display="none";

	}
</script>
</body>

</html>
