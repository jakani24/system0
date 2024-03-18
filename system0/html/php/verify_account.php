<?php
	echo("<head>");
	include "/var/www/html/system0/html/php/login/v3/components.php";
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