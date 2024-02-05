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
$username=$_SESSION["username"];
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

	function get_base64_preview($filename){
		$base64="";
		$file=fopen($filename,"r");
		$start=0;
		while(!feof($file)){
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
?>
<div id="content"></div>

<head>
  <title>Alle Dateien</title>
  
</head>
<body>
	<div class="container mt-10" style="height: 100vh;overflow-y:auto">
		<div class="row justify-content-center">
			<!--<div style="width: 90vh">-->
			      <h1>Deine Dateien</h1>
				<div class="container">
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
					      echo '<tr>';
					      echo '<td><img  style="display:block; width:100px;height:100px;" id="base64image" src="data:image;base64,' . get_base64_preview($file) . '"/></td>';
					      echo '<td>' . basename($file) . '</td>';
					      echo '<td><a href="print.php?cloudprint='.basename($file).'">Drucken</a></td>';
					      echo "<td><a href='cloud.php?delete=".basename($file)."' >" . "Löschen" . '</a></td>';
					      echo "<td><a href='/system0/html/user_files/$username/".basename($file)."' download>" . "Herunterladen" . '</a></td>';
					      echo "<td><a href='cloud.php?public=".basename($file)."'>Öffentlich verfügbar machen</a></td>";
					      echo '</tr>';
					  }
				      } else {
					  echo '<tr><td colspan="2">Directory not found</td></tr>';
				      }
				      ?>
				    </tbody>
				  </table>
				</div>	
			    <!--</div>-->
		</div>
	</div>
	<div id="footer"></div>
</body>

</html>
