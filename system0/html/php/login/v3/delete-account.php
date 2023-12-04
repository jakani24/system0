<?php
require_once "php/config.php"; 
// Initialize the session
session_start();
 include "/var/www/html/system0/html/php/login/v3/waf/wafno_anti_xss.php";
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

        <div class="container mt-5">
         <div class="row justify-content-center">
             <div class="col-md-8 text-center">
                 <div id="content">
                     <p class="mt-4">When you delete your account, the following will happen:</p>
                     <ul class="list-unstyled">
                         <li>- We will delete all your data from our systems.</li>
                         <li>- We will delete your cloud as well as your voctr files.</li>
                         <li>- Your username will be freed. This means anyone can re-register with your username.</li>
                     </ul>
                 </div>
                 <form action="" method="post" class="mt-4">
                     <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                     <div class="mb-3">
                         <label for="username" class="form-label">To continue, please type in your username:</label>
                         <input type="text" id="username" name="username" class="form-control" required>
                     </div>
                     <button type="submit" class="btn btn-primary">Submit</button>
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
                         header("LOCATION:/php/login/v3/logout.php");
                     } else {
                         echo '<div class="alert alert-danger mt-4">Usernames did not match!</div>';
                     }
                 }
                 ?>
             </div>
         </div>
     </div>
    </body>
</html>
