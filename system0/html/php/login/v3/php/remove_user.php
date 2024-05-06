<!DOCTYPE html>
<html>
<?php
// Initialize the session
session_start();
include "/var/www/html/system0/html/php/login/v3/waf/waf.php";
require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"][3]!== "1"){
    header("location: login.php");
    exit;
}
$_SESSION["rid"]++;
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
}
</script>
<?php $color=$_SESSION["color"]; ?>
<?php echo(" <body style='background-color:$color'> ");?>
<div id="content"></div>
<?php
	function get_perm_string(){
		$perm_str="";
		if(isset($_POST["print"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["private_cloud"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["public_cloud"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["printer_ctrl_all"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["change_user_perm"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["create_admin"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["view_log"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["view_apikey"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["create_key"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["debug"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		if(isset($_POST["delete_from_public_cloud"]))
			$perm_str.="1";
		else
			$perm_str.="0";
		return $perm_str;
	}
	function deleteDirectory($dir) {
	    if (!is_dir($dir)) {
		return;
	    }

	    // Get list of files and directories inside the directory
	    $files = scandir($dir);

	    foreach ($files as $file) {
		// Skip current and parent directory links
		if ($file == '.' || $file == '..') {
		    continue;
		}

		$path = $dir . '/' . $file;

		if (is_dir($path)) {
		    // Recursively delete sub-directory
		    deleteDirectory($path);
		} else {
		    // Delete file
		    unlink($path);
		}
	    }

	    // Delete the empty directory
	    rmdir($dir);
	}
	echo ("<script type='text/javascript' >load_admin()</script>");
	require_once "config.php"; 
	if(isset($_GET["update_id"]) && $_GET["rid"]==$_SESSION["rid"]-1){
		$tid=$_GET["update_id"];
		$perms=get_perm_string();
		$sql="UPDATE users SET role = '$perms' WHERE id=$tid";
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_execute($stmt);		
	}
	if(isset($_POST['username']))
	{
		$username_td=$_POST['username'];
		$username_td=htmlspecialchars($username_td);
		$sql="DELETE FROM users WHERE username = '$username_td';";
		//echo($sql);
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_execute($stmt);
		deleteDirectory("/var/www/html/system0/html/user_files/$username_td/");
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
                echo "<div class='alert alert-danger' role='alert'>Oops! Something went wrong. Please try again later.</div>";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
	echo('<div>');
	echo('<div class="container mt-5" >
			<div class="d-flex justify-content-center mb-3">
				<h3>Benutzer zum löschen auswählen:</h3>
			</div>
			
			
			<form action="" method="post" style="width: 40%;" class="mx-auto">
				<div class="mb-3">
					<div class="row">
						<div class="col">
					  		<label for="lang">Benutzer:</label>
						</div>
						<div class="col">
					  		<select name="username" id="username" class="form-select">
						');


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
					echo "<div class='alert alert-danger' role='alert'>Oops! Something went wrong. Please try again later.</div>";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
			$cnt2++;
		}
  	echo('</select>
					 </div>
										<div class="col">
									  		<div class="d-flex justify-content-center">
												<button type="submit" class="btn btn-danger" id="ban" ban="ban">Benutzer löschen</button>
									  		</div>
										</div>
									</div>
								</div>
							</form>
						</div>');
	echo('<br><br>
 		<div class="container mt-5">
			<div class="d-flex justify-content-center mb-3">
				<h3>User zum Bannen auswählen:</h3>
			</div>
			
			
			<form action="" method="post" style="width: 40%;" class="mx-auto">
				<div class="mb-3">
					<div class="row">
						<div class="col">
					  		<label for="lang">Benutzername:</label>
						</div>
						<div class="col">
					  		<select name="ban" id="ban" class="form-select">');
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
					echo "<div class='alert alert-danger' role='alert'>Oops! Something went wrong. Please try again later.</div>";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
			$cnt2++;
		}
  	echo('</select>');
  	//echo('<br><input type="text" value="ban reason" id="reason" name="reason" />');
  	echo('<select name="reason" id="reason" class="form-select">');
  	echo('<option reason="Hacking">Hacken</option>');
  	echo('<option reason="Illegal activities">Illegale aktivitäten</option>');
  	echo('<option reason="Misuse of service">Missbrauch der Website</option>');
  	echo('<option reason="Bad behaviour>Schlechtes Verhalten</option>');
  	echo('<option reason="inappropriate behaviour">Unangemessenes Verhalten</option>');
  	echo('<option reason="inappropriate username">Unangemessener Benutzername</option>');
  	echo('<option reason="Illegal files">Illegale Dateien</option>');
  	echo('<option reason="Unspecified">Andere</option>');
  	echo('</select>
   									</div>
										<div class="col">
									  		<div class="d-flex justify-content-center">
												<button type="submit" class="btn btn-danger">Senden</button>
									  		</div>
										</div>
									</div>
								</div>
							</form>
						</div>');
 
	echo("<br><br>");
	echo('<div class="container mt-5">
			<div class="d-flex justify-content-center mb-3">
				<h3>Please select a user to unban:</h3>
			</div>
			
			
			<form action="" method="post" style="width: 40%;" class="mx-auto">
				<div class="mb-3">
					<div class="row">
						<div class="col">
					  		<label for="lang">Benutzer:</label>
						</div>
						<div class="col">
					  		<select name="unban" id="unban" class="form-select">');     
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
					echo "<div class='alert alert-danger' role='alert'>Huch! Ein Fehler ist aufgetreten. Bitte versuchen Sie es später noch einmal.</div>";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
			$cnt2++;
		}
  	echo('</select>
   				</div>
					<div class="col">
						<div class="d-flex justify-content-center">
							<button type="submit" class="btn btn-danger" id="unban" unban="unban">Senden</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
 	</div>');
	?>


	<div class="container" style="min-height:95vh">
		<div class="row">
			<div class="col-mt-12">
				<!-- list users and their permissions -->
				<?php
					echo("<table class='table'>");
						echo("<thead>");
							echo("<tr>");
								echo("<td>Nutzer</td>");
								echo("<td>Drucken</td>");
								echo("<td>Cloud</td>");
								echo("<td>Öffentliche Cloud</td>");
								echo("<td>Alle Drucker abbrechen / freigeben</td>");
								echo("<td>Benutzereinstellungen ändern</td>");
								echo("<td>Administratoren erstellen</td>");
								echo("<td>Log ansehen</td>");
								echo("<td>APIkey ansehen</td>");
								echo("<td>Druckschlüssel erstellen</td>");
								echo("<td>Debug</td>");
								echo("<td>Alle Dateien von Öffentlicher Cloud löschen</td>");
								echo("<td>Aktualisieren</td>");
								echo("<td>Benutzer löschen</td>");
							echo("</tr>");
						echo("</thead>");
						echo("<tbody>");
							echo("<tr>");
								//how many users do we have?
								$cnt=0;
								$sql="SELECT COUNT(*) FROM users";
								$stmt = mysqli_prepare($link, $sql);
								mysqli_stmt_execute($stmt);
								// Store result
								mysqli_stmt_store_result($stmt);
								mysqli_stmt_bind_result($stmt, $cnt);
								mysqli_stmt_fetch($stmt);
								mysqli_stmt_close($stmt);
								//now we know how many users we have.
								$last_id=0;
								while($cnt!=0){
									$tusername="";
									$trole="";
									$tid=0;
									$sql="select id,username,role from users where id>$last_id ORDER BY id";
									$stmt = mysqli_prepare($link, $sql);
									mysqli_stmt_execute($stmt);
									// Store result
									mysqli_stmt_store_result($stmt);
									mysqli_stmt_bind_result($stmt, $tid,$tusername,$trole);
									mysqli_stmt_fetch($stmt);
									mysqli_stmt_close($stmt);
									echo("<tr><form action='remove_user.php?update_id=$tid&rid=".$_SESSION["rid"]."' method='post'>");
									echo("<td>$tusername</td>");
									if($trole[0]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="print" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="print" ></td>');
									if($trole[1]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="private_cloud" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="private_cloud" ></td>');
									if($trole[2]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="public_cloud" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="public_cloud" ></td>');
									if($trole[3]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="printer_ctrl_all" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="printer_ctrl_all" ></td>');
									if($trole[4]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="change_user_perm" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="change_user_perm" ></td>');
									if($trole[5]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="create_admin" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="create_admin" ></td>');
									if($trole[6]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="view_log" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="view_log" ></td>');
									if($trole[7]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="view_apikey" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="view_apikey" ></td>');
									if($trole[8]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="create_key" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="create_key" ></td>');
									if($trole[9]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="debug" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="debug" ></td>');
									if($trole[10]==="1")
										echo('<td><input class="form-check-input" type="checkbox" value="" name="delete_from_public_cloud" checked></td>');
									else
										echo('<td><input class="form-check-input" type="checkbox" value="" name="delete_from_public_cloud" ></td>');
									echo('<td><input type="submit" class="btn btn-dark mb-5" value="Aktualisieren"  id="button"></td>');
									echo('<td><button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#accept">Benutzer löschen</button></td>');
									echo("</form></tr>");
									$last_id=$tid;
									$cnt--;
								}
							echo("</tr>");
						echo("</tbody>");
					echo("</table>");
					mysqli_close($link);
				?>
			</div
		</div>
	</div>
	<div class="modal fade" id="accept" tabindex="-1" aria-labelledby="accept" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Benutzer wirklich löschen?</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <div class="d-flex flex-row bd-highlight m-3">
		  <div class="p-2 bd-highlight">
			 <button type="button" class="btn-success">Bestätigen</button> 
		  </div>
		  <div class="p-2 bd-highlight">
			<button type="button" class="btn-danger" data-bs-dismiss="modal" aria-label="Close">Nein</button> 
		  </div>
		</div>
	      </div>
	    </div>
	  </div>
	</div>
</div>	

<div id="footer"></div>
</body>

</html>
