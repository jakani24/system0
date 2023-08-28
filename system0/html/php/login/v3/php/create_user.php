<?php
// Include config file
require_once "config.php";
 include "/var/www/html/system0/html/php/login/v3/waf/waf_no_anti_xss.php";
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$role="user";
$username_err = $password_err = $confirm_password_err = "";
$err="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $err = "Username can only contain letters, numbers, and underscores.";
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
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
    }
    else if(strlen(trim($_POST["new_password"])) > 96)
        {
            $login_err = "Password cannot have more than 96 characters.";
        } else{
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
        $sql = "INSERT INTO users (username, password, role,banned) VALUES (?, ?, ?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            $banned=0;
            mysqli_stmt_bind_param($stmt, "sssi", $param_username, $param_password, $role,$banned);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $role="user";
            $banned=0;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                mkdir("/var/www/html/system0/html/user_files/$username");
                header("location: /system0/html/php/login/v3/login.php");
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
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<body>
    <div class="container mt-5">
	    <div class="row justify-content-center">
		    <div class="col-md-6">
		        <h3 class="text-center">Create Account</h3>
		        <form action="" method="post">
    		        <div class="mb-3">
    				    <label for="username" class="form-label">New Username:</label>
    					<input type="text" class="form-control" id="username" name="username" required>
    				</div>
    				<div class="mb-3">
    					 <label for="pwd" class="form-label">New Password:</label>
    					 <input type="password" class="form-control" id="pwd" name="password" required>
    				</div>
    				<div class="mb-3">
    					 <label for="confirmPwd" class="form-label">Confirm New Password:</label>
    					 <input type="password" class="form-control" id="confirmPwd" name="confirm_password" required>
    				</div>
    		        <div class="mb-3 form-check">
    		            <input type="checkbox" class="form-check-input" id="keepmeloggedin" name="keepmeloggedin" value="keepmeloggedin">
    		            <label class="form-check-label" for="keepmeloggedin">Keep me logged in</label>
    		        </div>
    		        <button type="submit" name="submit" class="btn btn-primary">Create Account</button>
		        </form>
		        <div class="text-center mt-3">
    		        <p class="mt-3">By creating an account you accept our <a href="/system0/html/php/login/v3/php/privacy-policy.php">Privacy Policy</a></p>
    				<p class="text-center mt-3">Already have an account? <a href="../login.php">Login here</a>.</p>
	            	</div>
			<?php 
			    if(!empty($err)){
				echo '<div class="alert alert-danger">' . $err . '</div>';
			    }        
		    	?>
		    </div>
	    </div>
	</div>
    
</body>
</html>
