<?php
function extract_param($gcode) {
    // Match the pattern S followed by digits, capturing the digits
    $matches = [];
    $pattern = '/[S|T]([0-9]+)/';

    if (preg_match($pattern, $gcode, $matches)) {
        return (int)$matches[1]; // Return the first capture group as an integer
    } else {
        return false; // No match found
    }
}

function check_file($path){//check file for temperature which are to high
	$file = fopen($path, 'r');
	$cnt=0;
	while (!feof($file)&&$cnt!=2) {
	    $line = fgets($file);
	    
	    // Extract parameter from lines with specific commands
	    if (strpos($line, 'M104') !== false || strpos($line, 'M140') !== false) {
		$cnt++;
	        $parameter = extract_param($line);
		if(strpos($line, 'M104') !== false){ //extruder_temp
			$ex_temp=$parameter;
		}
		if(strpos($line, 'M140') !== false){ //bed temp
			$bed_temp=$parameter;
		}
	    }
	}
	//echo("bed:$bed_temp;ex:$ex_temp");
	if($bed_temp>75 or $ex_temp>225){
		return 0;
	}else{
		return 1;
	}
}

function is_time_between($startTime, $endTime, $checkTime) {
    // Convert times to timestamps
    $startTimestamp = strtotime($startTime);
    $endTimestamp = strtotime($endTime);
    $checkTimestamp = strtotime($checkTime);
    
    // If end time is less than start time, it means the range crosses midnight
    if ($endTimestamp < $startTimestamp) {
        // Check if the time is between start time and midnight or between midnight and end time
        return ($checkTimestamp >= $startTimestamp || $checkTimestamp <= $endTimestamp);
    } else {
        // Normal case: check if the time is between start and end time
        return ($checkTimestamp >= $startTimestamp && $checkTimestamp <= $endTimestamp);
    }
}

?>
<!DOCTYPE html>
<html>
	<?php
	// Initialize the session
	$warning=false;
	session_start();
	include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
	include "config.php";
	require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
	include "queue.php";
	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true or $_SESSION["role"][0]!=="1"){
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
		echo "<script type='text/javascript' >load_user()</script>";
		
		test_queue($link);
	?>

	<?php $userid=$_SESSION["id"]; ?>
	<?php echo(" <body style='background-color:$color'> ");?>
	<div id="content"></div>

	<head>
	  <title>Datei drucken</title>
	
	</head>

	<body>
		<br><br>
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
				//echo $sql;
		
				if(!empty($_FILES['file_upload']))
				{
					$ok_ft=array("gcode","");
					$unwanted_chr=[' ','(',')','/','\\','<','>',':',';','?','*','"','|','%'];
					$filetype = strtolower(pathinfo($_FILES['file_upload']['name'],PATHINFO_EXTENSION));
					$path = "/var/www/html/system0/html/user_files/$username/";
					$print_on=$_POST["queue_printer"];
					$filename=basename( $_FILES['file_upload']['name']);
					$filename=str_replace($unwanted_chr,"_",$filename);
					$path = $path . $filename;
					if(!in_array($filetype,$ok_ft))
					{
						echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Dieser Dateityp wird nicht unterstüzt.</div></center>");
						sys0_log("Could not upload file for ".$_SESSION["username"]." because of unknown file extension",$_SESSION["username"],"PRINT::UPLOAD::FILE::FAILED");//notes,username,type
					}
					else
					{
						if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $path)) {
							$sql="INSERT INTO queue (from_userid,filepath,print_on) VALUES (?,?,?)";		
							$stmt = mysqli_prepare($link, $sql);	
							mysqli_stmt_bind_param($stmt, "isi", $userid,$path,$print_on);				
							mysqli_stmt_execute($stmt);

							echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei ".  basename( $_FILES['file_upload']['name']). " wurde hochgeladen und an die Warteschlange gesendet</div></center>");
							sys0_log("user ".$_SESSION["username"]." uploaded ".basename($path)." to the queue",$_SESSION["username"],"PRINT::UPLOAD::QUEUE");//notes,username,type
						}   
						else
						{
							echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Datei ".  basename( $_FILES['file_upload']['name']). " konnte hochgeladen werden</div></center>");
						}
					}
					unset($_FILES['file']);
				}
				if(isset($_GET["cloudprint"])){
					$print_on=$_POST["queue_printer"];
					if(!isset($_GET["pc"]))
						$path = "/var/www/html/system0/html/user_files/$username/".$_GET["cloudprint"];
					else
						$path = "/var/www/html/system0/html/user_files/public/".$_GET["cloudprint"];
					$sql="INSERT INTO queue (from_userid,filepath,print_on) VALUES (?,?,?)";		
					$stmt = mysqli_prepare($link, $sql);	
					mysqli_stmt_bind_param($stmt, "isi", $userid,$path,$print_on);				
					mysqli_stmt_execute($stmt);


					echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei ".  basename( $_FILES['file_upload']['name']). " wurde hochgeladen und an die Warteschlange gesendet</div></center>");
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

					echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Der Drucker ist zur Zeit nicht verfügbar. Warte einen Moment oder versuche es mit einem anderen Drucker erneut.</div></center>");
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
						echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Dieser Dateityp wird nicht unterstüzt.</div></center>");
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
								echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Erfolg! Die Datei ".  basename( $_FILES['file_upload']['name']). " wurde hochgeladen.</div></center>");
								echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei wird an den 3D-Drucker gesendet...</div></center>");
								if(check_file($path) or isset($_POST["ignore_unsafe"])){
									exec('curl -k -H "X-Api-Key: '.$apikey.'" -F "select=true" -F "print=true" -F "file=@'.$path.'" "'.$printer_url.'/api/files/local" > /var/www/html/system0/html/user_files/'.$username.'/json.json');
									//file is on printer and ready to be printed
									$userid=$_SESSION["id"];
									echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei gesendet und Auftrag wurde gestartet.</div></center>");
									sys0_log("user ".$_SESSION["username"]." uploaded ".basename($path)." to printer ".$_POST["printer"]."",$_SESSION["username"],"PRINT::UPLOAD::PRINTER");//notes,username,type
									$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
									$json=json_decode($fg,true);
									if($json['effectivePrint']==false or $json["effectiveSelect"]==false)
									{
										echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Ein Fehler ist aufgetreten und der Vorgang konnte nicht gestartet werden. Warte einen Moment und versuche es dann erneut.</div></center>");
										sys0_log("Could not start job for ".$_SESSION["username"]."with file ".basename($path)."",$_SESSION["username"],"PRINT::JOB::START::FAILED");//notes,username,type
									}
									else
									{
										$sql="update printer set free=0, printing=1,mail_sent=0, used_by_userid=$userid where id=$printer_id";
										$stmt = mysqli_prepare($link, $sql);					
										mysqli_stmt_execute($stmt);
										//delete printer key:
										$sql="DELETE from print_key where print_key='$print_key'";
										$stmt = mysqli_prepare($link, $sql);
										mysqli_stmt_execute($stmt);	
										mysqli_stmt_close($stmt);	
									}
								}else{
									$warning=true;
									echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Achtung, deine Bett oder Extruder Temperatur ist sehr hoch eingestellt. Dies wird zur zerstörung des Druckes und somit zu Müll führen. Bitte setze diese Temperaturen tiefer in den Einstellungen deines Slicers.</div></center>");
								}
							}
							else
							{
								echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Ein Fehler beim Uploaden der Datei ist aufgetreten! Versuche es erneut! </div></center>");
							}
						}
						else{
							echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Der Druckschlüssel ist nicht gültig. Evtl. wurde er bereits benutzt. Versuche es erneut! </div></center>");
						}
					}
					unset($_FILES['file']);
				}
				if(isset($_GET["cloudprint"])){
					if(!isset($_GET["pc"]))
						$path = "/var/www/html/system0/html/user_files/$username/".$_GET["cloudprint"];
					else
						$path = "/var/www/html/system0/html/user_files/public/".$_GET["cloudprint"];
					//check if print key is valid:
					$print_key=htmlspecialchars($_POST["print_key"]);
					$sql="SELECT id from print_key where print_key='$print_key'";
					$stmt = mysqli_prepare($link, $sql);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
						
					//if(mysqli_stmt_num_rows($stmt) == 1){ turned off because user does not need to have a printer key
					if(true){
					mysqli_stmt_close($stmt);
	
							echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei wird an den 3D-Drucker gesendet...</div></center>");
							if(check_file($path)  or isset($_POST["ignore_unsafe"])){
								exec('curl -k -H "X-Api-Key: '.$apikey.'" -F "select=true" -F "print=true" -F "file=@'.$path.'" "'.$printer_url.'/api/files/local" > /var/www/html/system0/html/user_files/'.$username.'/json.json');
								//file is on printer and ready to be printed
								$userid=$_SESSION["id"];
								echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei gesendet und Auftrag wurde gestartet.</div></center>");
								sys0_log("user ".$_SESSION["username"]." uploaded ".basename($path)." to printer ".$_POST["printer"]."",$_SESSION["username"],"PRINT::UPLOAD::PRINTER");//notes,username,type
								$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
								$json=json_decode($fg,true);
								//echo('curl -k -H "X-Api-Key: '.$apikey.'" -F "select=true" -F "print=true" -F "file=@'.$path.'" "'.$printer_url.'/api/files/local" > /var/www/html/system0/html/user_files/'.$username.'/json.json');
								//echo("<br><br><br>");							
								//var_dump($json);
								if($json['effectivePrint']==false or $json["effectiveSelect"]==false)
								{
									echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Ein Fehler ist aufgetreten und der Vorgang konnte nicht gestartet werden. Warte einen Moment und versuche es dann erneut.</div></center>");
									sys0_log("Could not start job for ".$_SESSION["username"]."with file ".basename($path)."",$_SESSION["username"],"PRINT::JOB::START::FAILED");//notes,username,type
								}
								else
								{
									$sql="update printer set free=0, printing=1,mail_sent=0, used_by_userid=$userid where id=$printer_id";
									$stmt = mysqli_prepare($link, $sql);					
									mysqli_stmt_execute($stmt);
									//delete printer key:
									$sql="DELETE from print_key where print_key='$print_key'";
									$stmt = mysqli_prepare($link, $sql);
									mysqli_stmt_execute($stmt);	
									mysqli_stmt_close($stmt);	
								}
							}else{
								$warning=true;
								echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Achtung, deinen Bett oder Extruder Temperatur ist sehr hoch eingestellt. Dies wird zur zerstörung des Druckes und somit zu Müll führen. Bitte setze diese Temperaturen tiefer in den Einstellungen deines Slicers.</div></center>");
							}
					}
					else{
						echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Der Druckschlüssel ist nicht gültig. Evtl. wurde er bereits benutzt. Versuche es erneut! </div></center>");
					}
				}	
			}
		}
	
	?>
	
			<div class="text-center mt-5" style="min-height: 95vh">
				<h1>Datei drucken</h1>
				<!-- Reservations notice -->
				<?php
					date_default_timezone_set('Europe/Zurich');
					$reservation_conflict=false;
					$today=date("Y-m-d");
					$sql="select time_from, time_to from reservations where day='$today';";
					$stmt = $link->prepare($sql);
        				$stmt->execute();
        				$result = $stmt->get_result();
        				//$row = $result->fetch_assoc();
        				$time_now=date("H:i");
        				while ($row = $result->fetch_assoc()) {
					    if (is_time_between($row["time_from"], $row["time_to"], $time_now)) {
						$reservation_conflict = true;
						break;
					    }
					}

					if ($reservation_conflict) {
					    echo "<center><div style='width:50%' class='alert alert-danger' role='alert'>Die Drucker sind zurzeit reserviert! Bitte drucke nur, wenn du gerade im Informatik Unterricht bist!</div></center>";
					}

				?>
				<div class="container d-flex align-items-center justify-content-center" >
				
				
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
							if(isset($_GET["preselect"])){
								$preselect=$_GET["preselect"];
							}else{
								$preselect=1;							
							}
							if(!isset($_GET["send_to_queue"])){
								while($num_of_printers!=0)
								{
									$id=0;
									$sql="Select id,color from printer where id>$last_id and free=1 order by id";
									//echo $sql;
									$color="";
									$stmt = mysqli_prepare($link, $sql);
									mysqli_stmt_execute($stmt);
									mysqli_stmt_store_result($stmt);
									mysqli_stmt_bind_result($stmt, $id,$color);
									mysqli_stmt_fetch($stmt);
									
									$color=intval($color);
									//get the real color
									$sql="select name from filament where internal_id=$color";
									$stmt = mysqli_prepare($link, $sql);
						                        mysqli_stmt_execute($stmt);
						                        mysqli_stmt_store_result($stmt);
						                        mysqli_stmt_bind_result($stmt,$color);
						                        mysqli_stmt_fetch($stmt);
		                                        
									if($id!=0 && $id!=$last_id)
									{
										if($id==$preselect)
											echo("<option printer='$id' value='$id' selected>Printer $id - $color</option>");
										else
											echo("<option printer='$id' value='$id'>Printer $id - $color</option>");
										$printers_av++;
									}
									$last_id=$id;
									$num_of_printers--;
								}
							}
							if($printers_av==0 or isset($_GET["send_to_queue"])){
								echo("<option printer='queue' value='queue'>an Warteschlange senden</option>");

							}	
							?>
						</select>
					</div>
					<!-- if we send to queue, the user should be able to choose which printer prints it afterwards -->
					<?php
					if($printers_av==0  or isset($_GET["send_to_queue"])){
						echo('<div class="form-group">');
							echo('<label class="my-3" for="printer">Auf diesem Drucker wird deine Datei gedruckt, sobald er frei ist.</label>');
							echo('<select class="form-control selector" name="queue_printer" required>');
								
								
								//get number of printers
								$num_of_printers=0;
								$sql="select count(*) from printer";
								$stmt = mysqli_prepare($link, $sql);
								mysqli_stmt_execute($stmt);
								mysqli_stmt_store_result($stmt);
								mysqli_stmt_bind_result($stmt, $num_of_printers);
								mysqli_stmt_fetch($stmt);
								$last_id=0;
								$printers_av=0;
								if(isset($_GET["preselect"])){
									$preselect=$_GET["preselect"];
								}else{
									$preselect=-1;							
								}
								echo("<option printer='-1' value='-1' selected selected>erster verfügbarer Drucker</option>");
								while($num_of_printers!=0)
								{
									$id=0;
									$sql="Select id,color from printer where id>$last_id order by id";
									//echo $sql;
									$color="";
									$stmt = mysqli_prepare($link, $sql);
									mysqli_stmt_execute($stmt);
									mysqli_stmt_store_result($stmt);
									mysqli_stmt_bind_result($stmt, $id,$color);
									mysqli_stmt_fetch($stmt);
									
									
									$color=intval($color);
									//get the real color
									$sql="select name from filament where internal_id=$color";
									$stmt = mysqli_prepare($link, $sql);
						                        mysqli_stmt_execute($stmt);
						                        mysqli_stmt_store_result($stmt);
						                        mysqli_stmt_bind_result($stmt,$color);
						                        mysqli_stmt_fetch($stmt);
				                                
									if($id!=0 && $id!=$last_id)
									{
										if($id==$preselect)
											echo("<option printer='$id' value='$id' selected>Drucker $id - $color</option>");
										else
											echo("<option printer='$id' value='$id'>Drucker $id - $color</option>");
										$printers_av++;
									}
									$last_id=$id;
									$num_of_printers--;
								}	
							
							echo('</select>');
						echo('</div>');
					}
					?>

				
					<br><br>
					<!--<label class="my-3" for="print_key">Druckschlüssel (Kann im Sekretariat gekauft werden)</label>
					<input type="text" class="form-control text" id="print_key" name="print_key" placeholder="z.B. A3Rg4Hujkief"><br>-->
					<?php
					if($warning==true){
						echo("<input type='checkbox' id='ignore_unsafe' name='ignore_unsafe' value='true'>");
						echo("<label for='ignore_unsafe'>Temperaturbeschränkungen Ignorieren und Drucken</label><br>");
					}
					
					?>
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
