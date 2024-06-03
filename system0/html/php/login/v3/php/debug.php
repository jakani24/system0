<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
//include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
include "queue.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"][9]!=="1"){
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

function update_input(input,action,id){
	var selector=document.getElementById(input);
	var selector_value=selector.value;
	fetch("/system0/html/api/printer_settings.php?action="+action+"&value="+selector.value+"&id="+id);

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
?>
<div id="content"></div>

<head>
  <title>Alle Augaben</title>
  
</head>
<body>
	<div class="container mt-5" style="min-height: 95vh;">
		<div class="row justify-content-center">
	  	<div style="width: 100hh">
	      <h1>Druckerfreigabe erzwingen (falls beim freigeben Fehlermeldungen angezeigt werden)</h1>
				<?php
					if(isset($_POST['free']))
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
					if($_GET["action"]=="add_filament"){
						$name=$_POST["filament_name"];
						$id=$_POST["filament_id"];
						$sql="INSERT INTO filament (internal_id,name) VALUES ($id,'$name')";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
					}
					
					$cnt=0;
					$url="";
					$apikey="";
					$sql="select count(*) from printer where free=0";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cnt);
					mysqli_stmt_fetch($stmt);	
					//echo($cnt);
					echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Druckerid</th><th>Freigeben</th></tr></thead><tbody>");
					$last_id=0;					
					while($cnt!=0)
					{
						$userid=0;
						$sql="select id,printer_url,apikey,cancel,used_by_userid from printer where free=0 and id>$last_id ORDER BY id";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $printer_id,$url,$apikey,$cancel,$userid);
						mysqli_stmt_fetch($stmt);
	
						
						$last_id=$printer_id;
						
						$used_by_user="";
						$sql="select username from users where id=$userid";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $used_by_user);
						mysqli_stmt_fetch($stmt);


						echo("<tr><td>$printer_id</td><td><form method='POST' action='?free=$printer_id'><button type='submit' value='free'  name='free' class='btn btn-dark'>Free</button></form></tr>");
						
						$cnt--;
					}
					echo("</tbody></table></div></div></div></div>");
				?>
				<br><br>


			<!-- Rotation der Druckerkameras: -->
			<h1>Rotation der Druckerkameras</h1>
			<?php
				//list printers => form => action=rot&rot=180
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
				echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Druckerid</th><th>Rotation</th></tr></thead><tbody>");
				$last_id=0;	
				$rotation=0;
				while($cnt!=0)
				{
					$userid=0;
					$sql="select rotation,id from printer where id>$last_id ORDER BY id";
					$cancel=0;
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $rotation,$printer_id);
					mysqli_stmt_fetch($stmt);

					
					$last_id=$printer_id;
					
					$used_by_user="";

					echo("<tr><td>$printer_id</td><td><form method='POST' action='?id=$printer_id'><input type='number' value='$rotation' id='rotation$printer_id' name='rotation' placeholder='rotation (deg)' oninput='update_input(\"rotation$printer_id\",\"update_rotation\",\"$printer_id\");'></input></td></form></tr>");
					
					$cnt--;
				}
				echo("</tbody></table></div></div></div>");
			?>
				<br><br>
				<h1>Filamentfarbe</h1>
				<?php
					//list printers => form => color
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
					echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Druckerid</th><th>Rotation</th></tr></thead><tbody>");
					$last_id=0;	
					$color="";
					while($cnt!=0)
					{
						$userid=0;
						$sql="select color,id from printer where id>$last_id ORDER BY id";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $color,$printer_id);
						mysqli_stmt_fetch($stmt);

						
						$last_id=$printer_id;
						
						$used_by_user="";

						echo("<tr><td>$printer_id</td><td><form method='POST' action='?id=$printer_id'><input type='text' id='color$printer_id' value='$color' name='color' placeholder='Filamentfarbe' oninput='update_input(\"color$printer_id\",\"update_color\",\"$printer_id\");'></input></td></form></tr>");
						
						$cnt--;
					}
					echo("</tbody></table></div></div></div>");
					echo("</div>");
				
				?>
				<h1>Filamente</h1>
				<?php
					//list printers => form => color
					$cnt=0;
					$url="";
					$apikey="";
					$sql="select count(*) from filament";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cnt);
					mysqli_stmt_fetch($stmt);	
					//echo($cnt);
					echo("<div class='container'><div class='row'><div class='col'><div class='overflow-auto'><table class='table'><thead><tr><th>Filamente</th><th>Farbe</th><th>Hinzufügen/Löschen</th></tr></thead><tbody>");
					
					//form to add a color
					echo("<form action='debug.php?action=add_filament' method='post'>");
						echo("<td><input type='number' placeholder='Filament id' name='filament_id'></input></td>");
						echo("<td><input type='text' placeholder='filament  Farbe' name='filament_name'></input></td>");
						echo("<td><button type='submit' value='add' class='btn btn-primary'>Hinzufügen</button></td>");
					echo("</form>");
					
					$last_id=0;	
					$color="";
					$id=0;
					while($cnt!=0)
					{
						$userid=0;
						$sql="select id,name,internal_id from filament where id>$last_id ORDER BY id";
						$cancel=0;
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt,$id, $color,$printer_id);
						mysqli_stmt_fetch($stmt);

						
						$last_id=$id;
						
						$used_by_user="";

						echo("<tr><td>$printer_id</td><td><form method='POST' action='?id=$printer_id'><input type='text' id='filament$printer_id' value='$color' name='color' placeholder='Filamentfarbe' oninput='update_input(\"filament$printer_id\",\"update_filament\",\"$printer_id\");'></input></td></form><td><button class='btn btn-danger' onclick='update_input(\"filament$printer_id\",\"delete_filament\",\"$printer_id\");'>Löschen</button></td></tr>");
						
						$cnt--;
					}
					echo("</tbody></table></div></div></div>");
					echo("</div>");
				
				?>
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
