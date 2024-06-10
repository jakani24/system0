<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"][9]!=="1"){
    header("location: login.php");
    exit;
}

include "../php/login/v3/php/config.php";

if($_GET['action']=="update_rotation")
{
	$printer_id=htmlspecialchars($_GET['id']);
	$rotation=htmlspecialchars($_GET["value"]);
	$sql="update printer set rotation=$rotation where id=$printer_id";
	$stmt = mysqli_prepare($link, $sql);					
	mysqli_stmt_execute($stmt);
}

if($_GET["action"]=="update_color")
{
	$printer_id=htmlspecialchars($_GET['id']);
	$color=htmlspecialchars($_GET["value"]);
	$sql="update printer set color='$color' where id=$printer_id";
	$stmt = mysqli_prepare($link, $sql);					
	mysqli_stmt_execute($stmt);
}

if($_GET["action"]=="update_filament")
{
	$id=htmlspecialchars($_GET['id']);
	$color=htmlspecialchars($_GET["value"]);
	$sql="update filament set name='$color' where internal_id=$id";
	$stmt = mysqli_prepare($link, $sql);					
	mysqli_stmt_execute($stmt);
}
if($_GET["action"]=="delete_filament")
{
	$id=htmlspecialchars($_GET['id']);
	$color=htmlspecialchars($_GET["value"]);
	$sql="delete from filament where internal_id=$id";
	$stmt = mysqli_prepare($link, $sql);					
	mysqli_stmt_execute($stmt);
}
?>
