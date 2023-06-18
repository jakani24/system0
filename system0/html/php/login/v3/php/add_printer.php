<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
include "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"]!="admin"){
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
  <title>Add a printer</title>
  
<?php
	// Define variables and initialize with empty values
	$server_url ="";
	$err="";
 
// Processing form data when form is submitted
if(isset($_POST["server_url"])){


    if(empty(trim($_POST["server_url"]))){
        $err = "Please enter a server url.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["server_url"]))){
        $err = "Server url can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM printer WHERE printer_url = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_url);
            
            // Set parameters
            $param_url = trim($_POST["server_url"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $err = "This server url is already taken.";
                } else{
                    $server_url = trim($_POST["server_url"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    

    
    // Check input errors before inserting in database
    if(empty($err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO printer (printer_url,printing,free,used_by_userid,system_status) VALUES (?,0,0,0,0)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
  //echo("test");
 
            mysqli_stmt_bind_param($stmt, "s", $param_url);
            
            // Set parameters
            $server_url = trim($_POST["server_url"]);
            $param_url = $server_url;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
              //echo("test");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        echo mysqli_error($link) ;
    }
    
    // Close connection
    mysqli_close($link);
}
?>
  
</head>
<body>
	<center>
		<form method="POST" action="">
			<input type="text" name="server_url" id="server_url" required>
			<input type="submit" value="Server hinzufügen">
		</form>
	</center>
<br><br><br>
</body>

</html>
