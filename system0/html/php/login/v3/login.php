<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf_no_anti_xss.php";
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["role"]==="user")
    {
         header("location: php/new_main.php");
    }
    if($_SESSION["role"]==="admin")
    {
         header("location: php/new_main.php");
    }
    exit;
}
require_once "php/config.php";
require_once "log/log.php";
require_once "waf/salt.php";
require_once "keepmeloggedin.php";
include "components.php";
$error=logmein($link);
//echo($error);
//die();
if($error!=="error1" && $error!=="error2")
{
    if($_SESSION["role"]==="admin")
    {
        header("LOCATION: php/new_main.php");
    }
    else if($_SESSION["role"]==="user")
    {
        header("LOCATION: php/new_main.php");
    }
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
$color="";
$banned=0;
$banned_reason="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, role, color,banned,banned_reason  FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = htmlspecialchars($username);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role,$color,$banned,$banned_reason);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
		                if($banned!=1)
		                {
		                    // Password is correct, so start a new session
		                    mysqli_stmt_close($stmt);
		                    if(isset($_POST["keepmeloggedin"]))
		                    {
		                        //echo("hi");
		                        //exit;
		                        $token=getSalt();
		                        $sql="UPDATE users SET keepmeloggedin=? WHERE username=?";
		                        if($stmt = mysqli_prepare($link, $sql)){
		                            $ptoken="";
		                            $pusername="";
		                            mysqli_stmt_bind_param($stmt, "ss", $ptoken,$pusername);
		                            $ptoken=$token;
		                            $pusername=$username;
		                            mysqli_stmt_execute($stmt);
		                            mysqli_stmt_close($stmt);
		                        }
		                        else
		                            echo("Error while setting 'keepmeloggedin'");

		                        $cookie=$username.':'.$token;
		                        $mac=hash("sha256",$cookie);
		                        $cookie.=':'.$mac;
		                        setcookie('keepmeloggedin',$cookie,time()+(3600*24*31));
		                        log_("Added keepmeloggedin token for $username","LOGIN:AUTOLOGIN");
		                    }
		                    session_start();

		                    // Store data in session variables
		                    $_SESSION["loggedin"] = true;
		                    $_SESSION["id"] = $id;
		                    $_SESSION["username"] = $username;
		                    $_SESSION["role"] = $role;
		                    $_SESSION["token"]=bin2hex(random_bytes(32));
		                    $_SESSION["color"]=$color;
		                    // Redirect user to welcome page
		                    if($role=="admin")
		                    {
		                        log_("$username logged in as admin","LOGIN:SUCCESS");
		                        header("location:php/new_main.php");
		                    }
		                    else
		                    {
		                        log_("$username logged in as user","LOGIN:SUCCESS");
		                        header("location:php/new_main.php");
		                    }
		                }
		                else
		                {
		                	$login_err = "Sorry your account got banned. Reason: $banned_reason";
		                }
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                            log_("$username tried to log in with wrong Password","LOGIN:FAILURE");
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                    log_("$username tried to log in with non existant username","LOGIN:FAILURE");
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
                log_("$username tried to log. Undefind failure","LOGIN:FAILURE");
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<script>
    function load_footer() {
      $(document).ready(function(){
        $('#footer').load("/system0/html/php/login/v3/html/footer.html");
      });
    }
    load_footer();
</script>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
		<?php include "/var/www/html/system0/html/php/login/v3/components.php";?>
    <title>Login</title>
</head>
<body>
	<div class="d-flex align-items-center justify-content-center vh-100">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-md-6">
						<h3 class="text-center">Login</h3>
						<form action="" method="post">
							<div class="mb-3">
								<label for="username" class="form-label">Username:</label>
								<input type="text" class="form-control" id="username" name="username" required>
							</div>
							<div class="mb-3">
								<label for="pwd" class="form-label">Password:</label>
								<input type="password" class="form-control" id="pwd" name="password" required>
							</div>
							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" id="keepmeloggedin" name="keepmeloggedin" value="keepmeloggedin">
								<label class="form-check-label" for="keepmeloggedin">Keep me logged in</label>
							</div>
							<button type="submit" name="submit" class="btn btn-dark">Login</button>
						</form>
						<div class="text-center mt-3">
							<button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#noaccount">Noch kein Account? Erstelle einen!</button>
						</div>
						<?php 
							if(!empty($login_err)){
							echo '<div class="alert alert-danger">' . $login_err . '</div>';
							}        
						?>
					</div>
				</div>
			</div>
		</div>



		<div class="modal fade" id="noaccount" tabindex="-1" role="dialog" aria-labelledby="Account" aria-hidden="true">
		      <div class="modal-dialog" role="document">
		        <div class="modal-content">
		          <div class="modal-header">
		            <h5 class="modal-title" id="exampleModalLabel">Account Erstellen</h5>
		            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		
		          </div>
				<div class="modal-body">
				  
		            <!-- Account things-->
					<form action="php/create_user.php" method="post">
						<div class="form-group mb-3">
							<label for="username" class="form-label">Email:</label>
							<input type="text" class="form-control" id="username" name="username" required>
					  	</div>
						<div class="form-group mb-3">
							<label for="pwd" class="form-label">Passwort:</label>
							<input type="password" class="form-control" id="pwd" name="password" required>
					  	</div>
						<div class="form-group mb-3">
							<label for="confirmPwd" class="form-label">Passwort bestätigen:</label>
							<input type="password" class="form-control" id="confirmPwd" name="confirm_password" required>
					  	</div>
					
					<?php 
					    if(!empty($err)){
						echo '<div class="alert alert-danger">' . $err . '</div>';
					    }        
					?>
				</div>
				<div class="modal-footer">
					<div class="form-check mx-auto">
						<!--<input type="checkbox" class="form-check-input" id="keepmeloggedin" name="keepmeloggedin" value="keepmeloggedin">-->
						<!--<label class="form-check-label" for="keepmeloggedin">Login speichern</label>-->
					</div>
        			<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="submit" name="submit" class="btn btn-dark">Account erstellen</button>
					<div class="text-center mt-3">
						<p class="mt-3">Durch erstellen des Accounts stimmst du unseren <a href="/system0/html/php/login/v3/php/privacy-policy.php">Datenschutzrichtlinien</a> zu</p>
					</div>
				</div>
				  </div>
				</form>
			</div>
		</div>
	


	<div id="footer"></div>
        
</body>
</html>
