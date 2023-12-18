<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
require_once "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"]!=="admin"){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Account Einstellungen</title>
</head>

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
        $('#content').load("/system0/html/php/login/v3/html/user_page.php");
        });
    }
    function load_admin()
    {
        $(document).ready(function(){
        $('#content').load("/system0/html/php/login/v3/html/admin_page.php");
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
	require_once "config.php";
	function generate_key($length = 12) {
	    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	    $password = '';

	    for ($i = 0; $i < $length; $i++) {
		$randomIndex = rand(0, strlen($characters) - 1);
		$password .= $characters[$randomIndex];
	    }

	    return $password;
	}
	
	?>
	<div id="content"></div>
    <!--Account things-->
		<div class="container mt-5" style="height: 95vh;">
			<div class="row justify-content-center">
				<div class="col-md-6 p-4">
					<h1 class="mb-2">Druckschlüssel Generieren</h1>
					<br>
					<p>
					Ein Druckschlüssel ist ein Code, welcher ein Benutzer benutzen kann, um einen Druckauftrag zu starten.
					</p>
					    <form action="create_key.php?create=true" method="post">
						<button type="submit" value="create_key" class="btn btn-dark">Neuen Druckschlüssel generieren</button>
					    </form>
				</div>
				<?php

				if (isset($_GET["create"])){
						$key=generate_key();
						$sql = "INSERT INTO print_key (print_key) VALUES (?)";
						$stmt = mysqli_prepare($link, $sql);
						mysqli_stmt_bind_param($stmt, "s", $key);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);
						echo("<center>You key got added to the database, it can now be used to print files.<br>key: $key</center>");
				}
				?>
		  	</div>
		</div>

	<div class="mt-5" id="footer"></div>

</html>
