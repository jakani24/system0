<!DOCTYPE html>
<html>
	<?php
		$username=htmlspecialchars($_GET["username"]);
		$printer_url=$_GET["url"];
	?>
	<head>
	  <title>Webcam</title>
	
	</head>
	<body>
		<?php
			$path = "/var/www/html/system0/html/user_files/$username/$printer_url.jpeg";
			unlink($path);			
			exec("wget \"http://$printer_url/webcam/?action=snapshot\" -O $path");
			echo("<img style='transform: rotate(180deg);' loading='lazy' width='100%' src='/system0/html/user_files/$username/$printer_url.jpeg'>");
		?>
		<script>
			setInterval(function() {
   			 location.reload();
			}, 5000);
		</script>
	</body>

</html>
