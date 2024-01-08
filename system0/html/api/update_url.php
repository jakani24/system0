<?php
	include "../php/login/v3/php/config.php"
	$apikey=htmlspecialchars($_GET["apikey"]);
	$apikey_fromdb="";
	$octoapikey=htmlspecialchars($_GET["octoapikey"]);
	$sql="select apikey from api where id=1";
	$stmt = mysqli_prepare($link, $sql);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $apikey_fromdb);
	mysqli_stmt_fetch($stmt);
	if($apikey!=$apikey_fromdb)
	{
		echo("wrong apikey");
		exit;
	}	

	else
	{
		$url=htmlspecialchars($_GET["url"]);
		$id=htmlspecialchars($_GET["id"]);
		$sql="insert into printer (id, printer_url,printing,free,used_by_userid,system_status,apikey) values ($id,'$url',0,1,0,0,'$octoapikey') on duplicate key update printer_url='$url', apikey='$octoapikey'";
		$stmt = mysqli_prepare($link, $sql);					
		mysqli_stmt_execute($stmt);
	}

?>
