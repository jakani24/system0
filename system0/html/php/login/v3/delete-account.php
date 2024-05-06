<?php
require_once "php/config.php"; 
// Initialize the session
session_start();
 include "/var/www/html/system0/html/php/login/v3/waf/waf_no_anti_xss.php";
 require_once "keepmeloggedin.php";
$error=logmein($link);
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
       <link rel="stylesheet" href="/system0/html/php/login/css/style.css">
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
       			$(document).ready(function(){
          		$('#footer').load("/system0/html/php/login/v3/html/footer.html");
       		});
       		}
       	</script>
        <?php
            $username=$_SESSION["username"];
            $role=$_SESSION["role"];
          
            echo "<script type='text/javascript' >load_user()</script>";
           
           
        ?>

        <div id="content"></div>
        <div class="container m-5" style="height: 95vh;">
         <div class="row justify-content-center">
             <div class="col-md-8 m-3">
                <h3>Account löschen</h3>
                 <p class="mt-4">Wenn Sie Ihr Konto löschen, geschieht Folgendes:</p>
                 <ul class="list-unstyled">
                     <li>Wir werden alle Ihre Daten aus unseren Systemen löschen.</li>
                     <li>Wir werden sowohl Ihre Cloud als auch Ihre voctr-Dateien löschen.</li>
                     <li>Ihr Benutzername wird freigegeben. Das bedeutet, dass sich jeder mit Ihrem Benutzernamen neu registrieren kann.</li>
                 </ul>
                 <form action="" method="post" class="mt-4">
                     <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                     <div class="mb-3">
                         <label for="username" class="form-label">Um fortzufahren, geben Sie bitte Ihren Benutzernamen ein:</label>
                         <input type="text" id="username" name="username" class="form-control" required>
                     </div>
                     <button type="submit" class="btn btn-primary">Bestätigen</button>
                 </form>
 
                 <?php
                 if (!empty($_POST["username"])) {
                     if ($_POST["username"] === $_SESSION["username"]) {
                         $username_td = $_SESSION["username"];
                         $username_td = htmlspecialchars($username_td);
                         $sql = "DELETE FROM users WHERE username = '$username_td';";
                         //echo($sql);
                         $stmt = mysqli_prepare($link, $sql);
                         mysqli_stmt_execute($stmt);
                         header("LOCATION:/system0/html/php/login/v3/logout.php");
                     } else {
                         echo '<div class="alert alert-danger mt-4">Usernames did not match!</div>';
                     }
                 }
                 ?>
             </div>
         </div>
     </div>
    <div id="footer"></div>
    </body>
</html>
