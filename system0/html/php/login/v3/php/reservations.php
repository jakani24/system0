<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"][9]!=="1"){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
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
	$userid=$_SESSION["id"];
	echo "<script type='text/javascript' >load_user()</script>";
?>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php
//delete reservations that are not valid anymore.
date_default_timezone_set('Europe/Zurich');
$yesterday = new DateTime('yesterday');
$formattedYesterday = $yesterday->format('Y-m-d');

$sql = "DELETE FROM reservations WHERE day <= ?";
$stmt = $link->prepare($sql);
if ($stmt) {
	$stmt->bind_param("s", $formattedYesterday);
	$stmt->execute();
	$stmt->close();
}

if(isset($_POST["res"])){
	$time_from=htmlspecialchars($_POST["time_from"]);
	$time_to=htmlspecialchars($_POST["time_to"]);
	$day=htmlspecialchars($_POST["date"]);
	$sql="INSERT INTO reservations (time_from,time_to,day,set_by_userid) VALUES (?, ?, ?, ?);";
	$stmt = $link->prepare($sql);
	$stmt->bind_param("sssi",$time_from, $time_to, $day,$userid);
        $stmt->execute();
}
if(isset($_GET["del"])){
	$id=htmlspecialchars($_GET["del"]);
	$sql="delete from reservations where id=$id";
	$stmt = $link->prepare($sql);
        $stmt->execute();
}

?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Drucker Reservationen</title>
</head>
<body>
	<div class="center-container" style="min-height: 95vh;">
		<div class="container">
			  <div class="container mt-5 text-center">
				<!-- Add reservation -->
				<h4>Reservation hinzufügen</h4>
				<form action="reservations.php?set_reservation" method="post">
					<input type="text" placeholder="von (z.B. 14:00)" name="time_from">
					<input type="text" placeholder="Bis (z.B. 15:00)" name="time_to">
					<input type="date" name="date">
					<button type="submit" value="res" name ="res" class="btn btn-primary">Reservieren</button>
				</form>
				<br><br>
				<!-- List reservations -->
				<h4>Reservationen (Alte Reservationen werden automatisch gelöscht)</h4>
				<?php
					$sql="select * from reservations order by id desc;";
				        $stmt = $link->prepare($sql);
				        $stmt->execute();
				        $result = $stmt->get_result();
				        echo("<table class='table'>");
				        echo("<tr><th>Zeit von</th><th>Zeit bis</th><th>Datum</th><th>Reservation löschen</th></tr>");
				        while($row = $result->fetch_assoc()) {
				        	echo("<tr><td>".$row["time_from"]."</td><td>".$row["time_to"]."</td><td>".$row["day"]."</td><td><a href='reservations.php?del=".$row["id"]."'>Löschen</a></td><tr>");
				        }
					echo("</table>");
				
				?>
			</div>
		</div>	
	</div>
	<div id="footer"></div>
</body>

</html>
