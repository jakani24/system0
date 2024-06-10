<!DOCTYPE html>
<html>
<?php

include "../php/login/v3/php/config.php";

?>


<script src="/system0/html/php/login/v3/js/load_page.js"></script>
<script>
function load_user()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/user_page.php");
	});
}
</script>
<?php
	
	echo "<script type='text/javascript' >load_user()</script>";


?>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
	if(isset($_POST["printer"])){
		$color=htmlspecialchars($_GET["color"]);
		$id=htmlspecialchars($_POST["printer"]);
		$sql="update printer set color='$color' where id=$id;";
		//echo($sql);
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_execute($stmt);
	
	}
?>
<div id="content"></div>

<head>
  <title>Filamentfarbe Aktualisieren</title>
  
</head>
<body>
	<div class="container mt-5" style="min-height: 95vh;">
		<div class="row justify-content-center">
	  	<div style="width: 100hh">
	      <h1>Filamentfarbe Aktualisieren</h1>
	      <form class="mt-5" enctype="multipart/form-data" method="POST" action="">
	      <input type="text" value="<?php echo($_GET["color"]); ?>" name="color" disabled><br><br>
	      <select class="form-control selector" name="printer" required>
		<?php
			//get number of printers
			$num_of_printers=0;
			$sql="select count(*) from printer;";
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
				$sql="Select id from printer where id>$last_id order by id";
				//echo $sql;
				$color="";
				$stmt = mysqli_prepare($link, $sql);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $id);
				mysqli_stmt_fetch($stmt);
				if($id!=0 && $id!=$last_id)
				{
					echo("<option printer='$id' value='$id'>Printer $id</option>");
				}
				$last_id=$id;
				$num_of_printers--;
			}	
		?>
		</select><br><br>
		<input type="submit" class="btn btn-dark mb-5" value="Farbe aktualisieren" id="button">
		</form>
	    </div>
	  </div>
	</div>
	<div id="footer"></div>
</body>

</html>
