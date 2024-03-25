<?php
// Initialize the session
session_start();
  include "/var/www/html/system0/html/php/login/v3/waf/waf_no_anti_xss.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"][5]!== "1"){
    header("location: login.php");
    exit;
}
?>
 <?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<script src="/system0/html/php/login/v3/js/load_page.js"></script>
     <script>
     	function load_admin()
		{
			$(document).ready(function(){
		   	$('#content').load("/system0/html/php/login/v3/html/user_page.php");
			});
			$(document).ready(function(){
   		$('#footer').load("/system0/html/php/login/v3/html/footer.html");
		});
		}
        load_admin();
     </script>
<?php
// Include config file
require_once "config.php";
include "../log/log.php";
include "permission_functions.php";
echo("<div id='content'></div>");
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$role="admin";
$username_err = $password_err = $confirm_password_err = "";
$err="";
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $err = "Username can only contain letters, numbers, and underscores.";
        $username = htmlspecialchar(trim($_POST["username"]));
        log_("User tried to create new account with illegal characters: $username","ACCOUNT_CREATE:FAILURE");
        
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $err = "This username is already taken.";
                    $username = htmlspecialchars(trim($_POST["username"]));
                    log_("User tried to create new account with allready taken username $username","ACCOUNT_CREATE:FAILURE");
                } else{
                    $username = htmlspecialchars(trim($_POST["username"]));
                }
            } else{
                log_("$username tried to create account. Undefind failure","ACCOUNT_CREATE:FAILURE");
                echo "<div class='alert alert-danger' role='alert'>Oops! Something went wrong. Please try again later.</div>";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $err = "Password must have atleast 6 characters.";
    } else if(strlen(trim($_POST["new_password"])) > 96)
        {
            $login_err = "Password cannot have more than 96 characters.";
        }else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($err) && ($password != $confirm_password)){
            $err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, role,notification_telegram,notification_mail) VALUES (?, ?, ?,?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
		$tel=0;
		$mail=1;	
            mysqli_stmt_bind_param($stmt, "sssii", $param_username, $param_password, $role,$tel,$mail);
            
            // Set parameters
		$tel=0;
		$mail=1;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $role=get_perm_string();
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                mkdir("/var/www/html/system0/html/user_files/$username");
                header("location: /");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<?php echo(" <body style='background-color:$color'> ");?>
	<div class="container">
		<div class="d-flex align-items-center justify-content-center vh-100">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-md-6">
						<h3 class="text-center">Account erstellen</h3>
						<form action="" method="post">
							<div class="form-group mb-3">
								<label for="username">Neuer Benutzername:</label>
								<input type="text" class="form-control" id="username" name="username" required>
						  	</div>
						  	<div class="form-group mb-3">
								<label for="pwd">Neues Passwort:</label>
								<input type="password" class="form-control" id="pwd" name="password" required>
						  	</div>
						  	<div class="form-group mb-3">
								<label for="pwd">Neues Passwort bestätigen:</label>
								<input type="password" class="form-control" id="pwd" name="confirm_password" required>
						  	</div>
							<div class="form-group mb-3">
								<h5>Berechtigungen</h5>
								<table class="table">
									<thead><tr><td>Berechtigung</td><td>Berechtigung erteilen</td></tr></thead>
									<tbody>
										<tr>
											<td>Datei Drucken</td>
											<td><input class="form-check-input" type="checkbox" value="" name="print"></td>
										</tr>
										<tr>
											<td>Private Cloud</td>
											<td><input class="form-check-input" type="checkbox" value="" name="private_cloud"></td>
										</tr>
										<tr>
											<td>Öffentliche Cloud</td>
											<td><input class="form-check-input" type="checkbox" value="" name="public_cloud"></td>
										</tr>
										<tr>
											<td>Alle Drucker abbrechen / freigeben</td>
											<td><input class="form-check-input" type="checkbox" value="" name="printer_ctr_all"></td>
										</tr>
										<tr>
											<td>Benutzereinstellungen ändern</td>
											<td><input class="form-check-input" type="checkbox" value="" name="change_user_perm"></td>
										</tr>
										<tr>
											<td>Administratoren erstellen</td>
											<td><input class="form-check-input" type="checkbox" value="" name="create_admin"></td>
										</tr>
										<tr>
											<td>Log ansehen</td>
											<td><input class="form-check-input" type="checkbox" value="" name="view_log"></td>
										</tr>
										<tr>
											<td>System0 APIkey ansehen</td>
											<td><input class="form-check-input" type="checkbox" value="" name="view_apikey"></td>
										</tr>
										<tr>
											<td>Druckschlüssel erstellen</td>
											<td><input class="form-check-input" type="checkbox" value="" name="create_key"></td>
										</tr>
										<tr>
											<td>Debug</td>
											<td><input class="form-check-input" type="checkbox" value="" name="debug"></td>
										</tr>
										<tr>
											<td>Alle Dateien von Öffentlicher Cloud Löschen</td>
											<td><input class="form-check-input" type="checkbox" value="" name="delete_from_public_cloud"></td>
										</tr>
									</tbody>
								</table>
							</div>
							<button type="submit" name="submit" class="btn btn-dark">Create Account</button><br><br>
						</form>
						<div class="text-center mt-3">
							<p class="mt-3">Beim Erstellen eines Accounts akzeptierst du unsere <a href="/system0/html/php/login/v3/php/privacy-policy.php">Privacy Policy</a></p>
							<p class="mt-3">Account bereits vorhanden? <a href="../login.php">Hier einlogen</a>.</p>
						</div>
						<?php 
						    if(!empty($err)){
							echo '<div class="alert alert-danger">' . $err . '</div>';
						    }        
						?>
					</div>
				</div>
			</div>
		</div>
</div>
		<div id="footer"></div>
</body>
</html>
