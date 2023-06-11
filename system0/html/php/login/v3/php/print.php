<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
$username=htmlspecialchars($_SESSION["username"]);
?>


<script src="/system0/html/php/login/v3/js/load_page.js"></script>
<script>
function load_admin()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/admin_page.html");
	});
}
function load_user()
{
	$(document).ready(function(){
   	$('#content').load("/system0/html/php/login/v3/html/user_page.html");
	});
}
</script>
<?php
	$role=$_SESSION["role"];
	if($role=="user")
	{
		echo "<script type='text/javascript' >load_user()</script>";
	}
	if($role=="admin")
	{
		echo "<script type='text/javascript' >load_admin()</script>";
	}
?>
<?php $color=$_SESSION["color"]; ?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>

<head>
  <title>Print a file</title>
  
</head>

<body>
	
	<center>
	<h1>Print a file</h1>
		<form enctype="multipart/form-data" method="POST" action="">
		<label for="file_upload">Your file to print</label>
			<input type="file" name="file_upload" required/><br><br>
			<label for="printer">Printer to print</label>
			<select name="printer" required>
				<!-- php um printer auszulesen -->
				<?php
				//get number of printers
					$num_of_printers=0;
					$sql="select count(*) from printer";
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
						$sql="Select id from printer where id>$last_id and free=1 order by id";
						echo $sql;
						$stmt = mysqli_prepare($link, $sql);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id);
						mysqli_stmt_fetch($stmt);
						if($id!=0 && $id!=$last_id)
						{
							echo("<option printer='$id' value='$id'>Printer $id</option>");
							$printers_av++;
						}
						$last_id=$id;
						$num_of_printers--;
					}
					if($printers_av==0)
						echo("<option printer='queue' value='queue'>No printer available (send to queue)</option>");
				?>
			</select><br><br>
			<input type="submit" value="Print file">
		</form>
	</center>
	<?php
		if(isset($_POST["printer"]))
		{
			//echo($_POST["printer"]);
			$status=0;
			$free=0;
			$url="";
			$apikey="";
			$printer_url="";
			$printer_id=htmlspecialchars($_POST["printer"]);
			if($printer_id=="queue")
			{
			 //send file to queue because no printer is ready!
			}
			$sql="select printer_url, free, system_status,apikey,printer_url from printer where id=$printer_id";
			//echo $sql;
			$stmt = mysqli_prepare($link, $sql);					
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $url,$free,$status,$apikey,$printer_url);
			mysqli_stmt_fetch($stmt);	
			if($free!=1 or $status!=0)
			{
				echo("Fatale error! The printer is not available. Please trie again later or trie use another printer");
				exit;
			}	
			if(!empty($_FILES['file_upload']))
			{
				$ok_ft=array("gcode","docx","doc","xlsx","xls","pptx","ppt","png","jpg","jpeg","gif","txt","enc","zip","keyx","csv","cbr","7z","gz","cfg","cpp","c","cxx","layout","pdf");
				$unwanted_chr=[' ','(',')','/','\\','<','>',':',';','?','*','"','|','%'];
				$filetype = strtolower(pathinfo($_FILES['file_upload']['name'],PATHINFO_EXTENSION));
				$path = "/var/www/html/system0/html/user_files/$username/";
				$filename=basename( $_FILES['file_upload']['name']);
				$filename=str_replace($unwanted_chr,"_",$filename);
				$path = $path . $filename;

				//if(in_array($filetype,$unwanted_ft))
				if(!in_array($filetype,$ok_ft))
				{
					echo "Sorry, this file extensions is not allowed!";
				}
				else
				{
					if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $path)) {
						echo "<center>Success! The file ".  basename( $_FILES['file_upload']['name']). " has been uploaded</center>";
						echo("<center>Sending file to 3D-printer...</center>");
						exec('curl -k -H "X-Api-Key: '.$apikey.'" -F "select=true" -F "print=true" -F "file=@'.$path.'" "'.$printer_url.'/api/files/local"');
						//file is on printer and ready to be printed
						$userid=$_SESSION["id"];
						$sql="update printer set free=0, printing=1, used_by_userid=$userid where id=$printer_id";
						$stmt = mysqli_prepare($link, $sql);					
						mysqli_stmt_execute($stmt);
						echo("<center>File sent and printer job started.<br>system0 uploader done. Thank you!</center>");
					}
					else
					{
						echo "There was an error uploading the file, please try again! path:".$path;
					}
				}
				unset($_FILES['file']);
			}	
		}
	
	?>
<br><br><br>
</body>

</html>