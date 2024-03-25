<?php
// Initialize the session
session_start();
 include "/var/www/html/system0/html/php/login/v3/waf/waf_no_anti_xss.php";
require_once "config.php";

 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
$old_password="";
$old_passwort_err="";
$username=$_SESSION["verify"];
echo("<div id='content'></div>");
if($_GET["token"]!=$_SESSION["pw_reset_token"]){
	$login_err = "Dein Link ist entweder abgelaufen oder ungültig. Erzeuge einen neuen, in dem du auf <a href='/system0/html/php/login/v3/login.php?resend_pw_reset'>diesen Link</a> klickst.";
	echo '<div class="alert alert-danger">' . $login_err . '</div>';
	
	//die();
}
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $login_err="";
    //first: validate old password
    if(isset($_GET["token"])&&isset($_SESSION["pw_reset_token"])){
	if($_GET["token"]==$_SESSION["pw_reset_token"]){
		$auth=true;
	}
	else{
		$auth=false;
	}
    }
    else{ $auth=false; }
    if($auth===true)
    {
        //end of old_password validation
        // Validate new password
        if(empty(trim($_POST["new_password"]))){
            $login_err = "Please enter the new password.";     
        } elseif(strlen(trim($_POST["new_password"])) < 6){
            $login_err = "Password must have atleast 6 characters.";
        }else if(strlen(trim($_POST["new_password"])) > 96)
        {
            $login_err = "Password cannot have more than 96 characters.";
        } 
        else{
            $new_password = trim($_POST["new_password"]);
        }
        
        // Validate confirm password
        if(empty(trim($_POST["confirm_password"]))){
            $login_err = "Please confirm the password.";
        } else{
            $confirm_password = trim($_POST["confirm_password"]);
            if(empty($new_password_err) && ($new_password != $confirm_password)){
                $login_err = "Password did not match.";
            }
        }
            
        // Check input errors before updating the database
        if(empty($login_err) ){
            // Prepare an update statement
            $sql = "UPDATE users SET password = ? WHERE username = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $param_password, $username);
                
                // Set parameters
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
		$username=$_SESSION["verify"];
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Password updated successfully. Destroy the session, and redirect to login page
			$_SESSION["pw_reset_token"]=urlencode(bin2hex(random_bytes(24)));
                    session_destroy();
                    header("location: login.php");
                    exit();
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }
	else{
		$login_err = "Dein Link ist entweder abgelaufen oder ungültig. Erzeuge einen neuen, in dem du auf <a href='/system0/html/php/login/v3/login.php?resend_pw_reset'>diesen Link</a> klickst.";
	}
        // Close connection
        mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passwort zurücksetzen</title>
   <link rel="stylesheet" href="/system0/html/php/login/css/style.css">
</head>
<?php 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php echo(" <body> ");?>
	
<div class="jumbotron d-flex align-items-center" style="min-height:95vh;">
  <div class="container" style="width:50%;">
    <h3 class="text-center">Passwort zurücksetzen</h3>
	<div class="m-3">
	    <form action="" method="post">
	      <div class="form-group m-2">
		<label for="pwd">Neues Passwort:</label>
		<input type="password" class="form-control" id="pwd" name="new_password" required>
	      </div>
	      <div class="form-group m-2">
		<label for="pwd">Neues Passwort bestätigen:</label>
		<input type="password" class="form-control" id="pwd" name="confirm_password" required>
	      </div>
	      <button type="submit" name="submit" class="btn btn-dark m-2">Bestätigen</button>
	    </form>
	</div>
	<?php
	        if(!empty($login_err)){
	            echo '<div class="alert alert-danger">' . $login_err . '</div>';
	        }        
	?>
  </div>
</div>   
<div id="footer"></div>
</body>
</html>
