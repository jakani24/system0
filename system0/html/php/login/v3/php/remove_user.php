<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"]!== "admin"){
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
   	$('#content').load("/system0/html/php/login/v3/html/admin_page.html");
	});
}
</script>
<?php $color=$_SESSION["color"]; ?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>
<?php
	echo ("<script type='text/javascript' >load_admin()</script>");
	require_once "config.php"; 
	if(isset($_POST['username']))
	{
		$username_td=$_POST['username'];
		$username_td=htmlspecialchars($username_td);
		$sql="DELETE FROM users WHERE username = '$username_td';";
		//echo($sql);
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_execute($stmt);
		log_("Deleted $username_td","BAN:DELETION");
	}
	else if(isset($_POST["ban"]))
	{
		$username_td=htmlspecialchars($_POST["ban"]);
		$reason=htmlspecialchars($_POST["reason"]);
		$sql="UPDATE users SET banned = 1, banned_reason='$reason' WHERE username='$username_td'";
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_execute($stmt);
		log_("Banned $username_td","BAN:BAN");
	}
	else if(isset($_POST["unban"]))
	{
		$username_td=htmlspecialchars($_POST["unban"]);
		$sql="UPDATE users SET banned = 0 WHERE username='$username_td'";
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_execute($stmt);
		log_("Unanned $username_td","BAN:UNBAN");
	}
	

		//how many users do we have?
		$cnt=0;
		$sql="SELECT COUNT(*) FROM users";
       if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                    mysqli_stmt_bind_result($stmt, $cnt);
                    if(mysqli_stmt_fetch($stmt)){
			    
                    }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }		
  	echo('<center><h3> Please select a user to delete:</h3><br></center>');
	echo('<center><form action="" method="post">');
	echo('<label for="lang">username:</label>');
	echo('<select name="username" id="username">');      
        //now get those users
        $cnt2=1;
        $id=0;
        $last_id=0;
        while($cnt2!==$cnt+1)
        {
			$sql = "SELECT id, username FROM users WHERE id > $last_id ORDER BY id;";
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Store result
					mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id,$username);
						if(mysqli_stmt_fetch($stmt)){
							//data retrieved
							$last_id=$id;
							echo('<option username="'.$username.'">'.$username.'</option>');
						}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
			$cnt2++;
		}
  	echo('</select>');
	echo('<input type="submit" id="ban" ban="ban" /></center>');
	echo('</form>'); 
	echo("<br><br>");
	echo('<center><h3> Please select a user to ban:</h3><br></center>');
	echo('<center><form action="" method="post">');
	echo('<label for="lang">username:</label>');
	echo('<select name="ban" id="ban">');      
        //now get those users
        $cnt2=1;
        $id=0;
        $last_id=0;
        while($cnt2!==$cnt+1)
        {
			$sql = "SELECT id, username FROM users WHERE id > $last_id AND (banned = 0 or banned IS NULL ) ORDER BY id;";
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Store result
					mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id,$username);
						if(mysqli_stmt_fetch($stmt)){
							//data retrieved
							$last_id=$id;
							echo('<option ban="'.$username.'">'.$username.'</option>');
						}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
			$cnt2++;
		}
  	echo('</select>');
  	//echo('<br><input type="text" value="ban reason" id="reason" name="reason" />');
  	echo('<select name="reason" id="reason">');
  	echo('<option reason="Hacking">Hacking</option>');
  	echo('<option reason="Illegal activities">Illegal activities</option>');
  	echo('<option reason="Misuse of service">Misuse of service</option>');
  	echo('<option reason="Bad behaviour>Bad behaviour</option>');
  	echo('<option reason="inappropriate behaviour">inappropriate behaviour</option>');
  	echo('<option reason="inappropriate username">inappropriate username</option>');
  	echo('<option reason="Illegal files">Illegal files</option>');
  	echo('<option reason="Unspecified">Other</option>');
  	echo('</select>');
	echo('<input type="submit"/></center>');
	echo('</form>'); 
	echo("<br><br>");
	echo('<center><h3> Please select a user to unban:</h3><br></center>');
	echo('<center><form action="" method="post">');
	echo('<label for="lang">username:</label>');
	echo('<select name="unban" id="unban">');      
        //now get those users
        $cnt2=1;
        $id=0;
        $last_id=0;
        while($cnt2!==$cnt+1)
        {
			$sql = "SELECT id, username FROM users WHERE id > $last_id AND banned=1 ORDER BY id;";
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Store result
					mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id,$username);
						if(mysqli_stmt_fetch($stmt)){
							//data retrieved
							$last_id=$id;
							echo('<option unban="'.$username.'">'.$username.'</option>');
						}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
			$cnt2++;
		}
  	echo('</select>');
	echo('<input type="submit" id="unban" unban="unban" /></center>');
	echo('</form>');
    // Close connection
    mysqli_close($link);

?>
</body>

</html>
