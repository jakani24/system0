<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
include "queue.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true or $_SESSION["role"][1]!="1"){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
$id=$_SESSION["id"];
$username=$_SESSION["username"];
$file_upload_err="nan";
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

	echo "<script type='text/javascript' >load_user()</script>";


?>
<?php $color=$_SESSION["color"]; ?>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";

	function get_base64_preview($filename){
		$base64="";
		$file=fopen($filename,"r");
		$start=-1;
		while(!feof($file)&&$start!=0){
			$buf=fgets($file);
			if(stripos($buf,"thumbnail end")!==false)
				$start=0;
			if($start==1)
				$base64.=$buf;
			if(stripos($buf,"thumbnail begin")!==false)
				$start=1;
		}
		fclose($file);
		$base64=str_replace(";","",$base64);
		$base64=str_replace(" ","",$base64);
		return $base64;
	}
	if(isset($_GET["delete"])){
		$path="/var/www/html/system0/html/user_files/$username/".str_replace("..","",htmlspecialchars($_GET["delete"]));
		unlink($path);
	
	}
	if(isset($_GET["public"])){
		$path="/var/www/html/system0/html/user_files/$username/".str_replace("..","",htmlspecialchars($_GET["public"]));
		$public_path="/var/www/html/system0/html/user_files/public/".str_replace("..","",htmlspecialchars($_GET["public"]));
		copy($path,$public_path);
	}
	if(!empty($_FILES['file']))
	{
		$ok_ft=array("gcode","");
		$unwanted_chr=[' ','(',')','/','\\','<','>',':',';','?','*','"','|','%'];
		$filetype = strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
		$path = "/var/www/html/system0/html/user_files/$username/";
		$filename=basename( $_FILES['file']['name']);
		$filename=str_replace($unwanted_chr,"_",$filename);
		$path = $path . $filename;

		//if(in_array($filetype,$unwanted_ft))
		if(!in_array($filetype,$ok_ft))
		{
			//echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Dieser Dateityp wird nicht unterstüzt.</div></center>");
			$file_upload_err="Dieser Dateityp wird nicht unterstüzt.";
		}
		else
		{
	
			if(move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
				//echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Erfolg! Die Datei ".  basename( $_FILES['file']['name']). " wurde hochgeladen.</div></center>");
				$file_upload_err="ok";
			}
			else
			{
				//echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>Ein Fehler beim Uploaden der Datei ist aufgetreten! Versuche es erneut! </div></center>");
				$file_upload_err="Ein Fehler beim Uploaden der Datei ist aufgetreten! Versuche es erneut!";
			}
		}
		unset($_FILES['file']);
	}
?>
<div id="content"></div>

<head>
  <title>Alle Dateien</title>
  
</head>
<body>
	<div class="container mt-4" style="height: auto;min-height:100vh">
		<div class="row justify-content-center">
			<!--<div style="width: 90vh">-->
				<?php
					if(!empty($file_upload_err)&&$file_upload_err!="nan"&&$file_upload_err!="ok")
						echo("<center><div style='width:50%' class='alert alert-danger' role='alert'>$file_upload_err</div></center>");	
					else if($file_upload_err!="nan")
						echo("<center><div style='width:50%' class='alert alert-success' role='alert'>Datei wurde hochgeladen</div></center>");
			
				?>
			      <h1>Deine Dateien</h1>
				<div class="container">
					<button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#upoload_file" id="lnk_1">Datei Hochladen</button>
					<form action="cloud.php" method="POST">
						<input type="text" name="search" placeholder="Suchbegriff">
						<button type="submit" class="btn btn-dark my-5">Suchen</button>
					</form>
				<div style="overflow-y:auto;overflow-x:auto">
				  <table class="table">
				    <thead>
				      <tr>
					<th>Preview</th>
					<th>File Name</th>
					<th>Print File</th>
					<th>Delete File</th>
					<th>Download File</th>
					<th>Make Public</th>
				      </tr>
				    </thead>
				    <tbody>
				      <?php
				      $directory = "/var/www/html/system0/html/user_files/$username/"; // Replace with the actual path to your directory

				      // Check if the directory exists
				      if (is_dir($directory)) {
					  $files = glob($directory . '/*.gcode');
					  
					  // Iterate through the files and display them in the table
					  $count = 1;
					  foreach ($files as $file) {
						if(isset($_POST["search"])){
							if (stripos(basename($file), $_POST["search"]) !== false) {
							      echo '<tr>';
							      echo '<td><img  style="display:block; width:100px;height:100px;" id="base64image" src="data:image;base64,' . get_base64_preview($file) . '"/></td>';
							      echo '<td>' . basename($file) . '</td>';
							      echo '<td><a href="print.php?cloudprint='.basename($file).'">Drucken</a></td>';
							      echo "<td><a href='cloud.php?delete=".basename($file)."' >" . "Löschen" . '</a></td>';
							      echo "<td><a href='/system0/html/user_files/$username/".basename($file)."' download>" . "Herunterladen" . '</a></td>';
							      echo "<td><a href='cloud.php?public=".basename($file)."'>Öffentlich verfügbar machen</a></td>";
							      echo '</tr>';
							}
						}else{
							echo '<tr>';
							echo '<td><img  style="display:block; width:100px;height:100px;" id="base64image" src="data:image;base64,' . get_base64_preview($file) . '"/></td>';
							echo '<td>' . basename($file) . '</td>';
							echo '<td><a href="print.php?cloudprint='.basename($file).'">Drucken</a></td>';
							echo "<td><a href='cloud.php?delete=".basename($file)."' >" . "Löschen" . '</a></td>';
							echo "<td><a href='/system0/html/user_files/$username/".basename($file)."' download>" . "Herunterladen" . '</a></td>';
							echo "<td><a href='cloud.php?public=".basename($file)."'>Öffentlich verfügbar machen</a></td>";
							echo '</tr>';
						}
					  }
				      } else {
					  echo '<tr><td colspan="2">Directory not found</td></tr>';
				      }
				      ?>
				    </tbody>
				  </table>
				</div>	
			    </div>
		</div>
	</div>
	<div class="modal fade" id="upoload_file" tabindex="1" role="dialog" aria-labelledby="upoload_file" aria-hidden="false">
		      <div class="modal-dialog" role="document">
		        <div class="modal-content">
		          <div class="modal-header">
		            <h5 class="modal-title" id="exampleModalLabel">Datei Hochladen</h5>
		            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		
		          </div>
				<div class="modal-body">
					<form action="cloud.php" method="post" enctype="multipart/form-data">
						<div class="mb-3">
						    <label for="file" class="form-label">Datei wählen:</label>
						    <input type="file" class="form-control" id="file" name="file" required accept=".gcode">
						</div>
						<button type="submit" class="btn btn-dark">Upload</button>	<br>

					</form>
				</div>
				  </div>
				</form>
			</div>
	</div>
	<div id="footer"></div>
</body>

</html>
