<?php
	include "config.php";
	session_start();
	if(isset($_GET["telegram_id"])){
		$telegram_id=htmlspecialchars($_GET["telegram_id"]);
		$id=$_SESSION["id"];
		$sql="update users set telegram_id=? where id=?";
		echo("ok1");
		//$sql="INSERT INTO queue (from_userid,filepath,print_on) VALUES (?,?,?)";		
		$stmt = mysqli_prepare($link, $sql);	
		mysqli_stmt_bind_param($stmt, "si", $telegram_id,$id);				
		mysqli_stmt_execute($stmt);
		$_SESSION["telegram_id"]=$telegram_id;
	}
	if(isset($_GET["notification_telegram"])){
		if($_GET["notification_telegram"]=="true"){
			$sql="update users set notification_telegram=1 where id=?";
			$_SESSION["notification_telegram"]=1;
		}	
		else{
			$sql="update users set notification_telegram=0 where id=?";
			$_SESSION["notification_telegram"]=0;
		}
		$id=$_SESSION["id"];
		echo("ok2");
		$stmt = mysqli_prepare($link, $sql);	
		mysqli_stmt_bind_param($stmt, "i" ,$id);				
		mysqli_stmt_execute($stmt);
	}
	if(isset($_GET["notification_mail"])){
		if($_GET["notification_mail"]=="true"){
			$sql="update users set notification_mail=1 where id=?";
			$_SESSION["notification_mail"]=1;
		}	
		else{
			$sql="update users set notification_mail=0 where id=?";
			$_SESSION["notification_mail"]=0;
		}
		$id=$_SESSION["id"];
		echo("ok3");
		$stmt = mysqli_prepare($link, $sql);	
		mysqli_stmt_bind_param($stmt, "i" ,$id);				
		mysqli_stmt_execute($stmt);
	}

?>