<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
require_once "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Account settings</title>
</head>
<style>
.button2{
	height:auto;
	width:300px;
}
.button2:hover {
	box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
}

</style>
<?php 
	$color=$_SESSION["color"]; 
	include "/var/www/html/system0/html/php/login/v3/components.php";
?>
<?php echo(" <body style='background-color:$color'> ");?>

    <script src="/system0/html/php/login/v3/js/load_page.js"></script>
    <script>
    function load_user()
    {
        $(document).ready(function(){
        $('#content').load("/system0/html/php/login/v3/html/user_page.html");
        });
    }
    function load_admin()
    {
        $(document).ready(function(){
        $('#content').load("/system0/html/php/login/v3/html/admin_page.html");
        });
    }
    </script>
    <?php
        $username=$_SESSION["username"];
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
    <?php
        if(isset($_POST["color"]))
        {
            $color=$_POST["color"];
        
            if(strlen($color)>45)
            {
                    die("invalid length");
            }
            else
            {
                    $sql = "UPDATE users SET color = ? WHERE username = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, "ss", $color, htmlspecialchars($_SESSION["username"]));
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    $_SESSION["color"]=$color;
                    header("location: #");
            }
        }
        else
        {
                $color=$_SESSION["color"];
        }
    ?>
	<div id="content"></div>
    <!--Account things-->
		<div class="container mt-5">
		  <div class="row justify-content-center">
			<div class="col-md-6 border p-4">
				<h1>Account settings</h1>
				<br><br>
				<a class="btn btn-secondary btn-block m-2" href="/system0/html/php/login/v3/reset-password.php" role="button">Reset your password</a>
				<a class="btn btn-secondary btn-block m-2" href="/system0/html/php/login/v3/delete-account.php" role="button">Delete your account and all the data associated with it</a>
				<br><a class="btn btn-secondary btn-block m-2" href="/system0/html/php/login/v3/php/privacy-policy.php" role="button">Our privacy policy</a>
				<br>
				<a class="btn btn-secondary btn-block m-2" href="/system0/html/php/login/v3/php/disclaimer.php" role="button">Dislcaimer</a>
				<br><br>
				<form method="POST" action="#">
					<div class="mb-3">
					  <label for="color" class="form-label">Background Color</label>
					  <input type="color" id="color" name="color" class="form-control selector" value="<?php echo $color; ?>" />
					</div>
					<button type="submit" class="btn btn-secondary">Submit</button>
				  </form>
			
			</div>
		  </div>
		</div>
</html>
