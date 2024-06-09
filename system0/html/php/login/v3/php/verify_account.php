<?php
	echo("<head>");
	include "/var/www/html/system0/html/php/login/v3/components.php";
	?>
	<script>
		function redirectToAnotherWebsite(newDomain) {
		    // Get the current URL
		    const currentUrl = window.location.href;

		    // Create a URL object from the current URL
		    const url = new URL(currentUrl);

		    // Get the search (query) parameters from the current URL
		    const searchParams = url.search;

		    // Extract the current domain
		    const currentDomain = url.origin;

		    // Check if the current domain is different from the new domain
		    if (currentDomain !== newDomain) {
			// Construct the new URL with the new domain and the preserved query parameters
			const newUrl = newDomain + url.pathname + searchParams;

			// Redirect to the new URL
			window.location.href = newUrl;
		    }
		}
		redirectToAnotherWebsite('https://app.ksw3d.ch');
	</script>
	
	<?php
	echo("</head>");
	echo("<br>");
	session_start();
	include "config.php";
	if(isset($_GET["token"])){
		if($_GET["token"]==$_SESSION["creation_token"]){
			$username=$_SESSION["verify"];
			$sql="update users set banned=0 where username='$username'";
			$stmt = mysqli_prepare($link, $sql);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			header("LOCATION: /system0/html/php/login/v3/login.php?acc_verify_ok");
		}else{
			$login_err = "Dein Link ist entweder abgelaufen oder ungültig. Erzeuge einen neuen, in dem du auf <a href='/system0/html/php/login/v3/login.php?resend_acc_verify'>diesen Link</a> klickst.";
			echo '<center><div style="width:50%" class="alert alert-danger">' . $login_err . '</div></center>';
		}
	}else{
		$login_err = "Dein Link ist entweder abgelaufen oder ungültig. Erzeuge einen neuen, in dem du auf <a href='/system0/html/php/login/v3/login.php?resend_acc_verify'>diesen Link</a> klickst.";
		echo '<center><div style="width:50%" class="alert alert-danger">' . $login_err . '</div></center>';	
	}
?>
