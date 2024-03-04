<?php
	session_start();
	include "config.php";
	if(isset($_GET["token"])){
		if($_GET["token"]==$_SESSION["creation_token"]){
			$username=$_SESSION["verify"];
			$sql="update users set banned=0 where username='$username'";
			$stmt = mysqli_prepare($link, $sql);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			header("LOCATION: /index.php");
		}else{
			echo("invalid token");
		}
	}else{
		echo("invalid token");
	}
?>
