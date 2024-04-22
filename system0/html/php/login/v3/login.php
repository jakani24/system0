<?php
// Initialize the session
session_start();
//include "/var/www/html/system0/html/php/login/v3/waf/waf_no_anti_xss.php";
$username = $password = $confirm_password = "";
$role="user";
$username_err = $password_err = $confirm_password_err = "";
$err="";
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
	header("location: https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/overview.php");
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


<?php
$validation_mail = <<<EOD
<!DOCTYPE html>
<html lang='en' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:v='urn:schemas-microsoft-com:vml'>
<head>
<title></title>
<meta content='text/html; charset=UTF-8' http-equiv='Content-Type'/>
<meta content='width=device-width, initial-scale=1.0' name='viewport'/>
<style>
    /* Ihre CSS-Stile hier */
</style>
</head>
<body style='background-color: #000000; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;'>
<table border='0' cellpadding='0' cellspacing='0' class='nl-container' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #000000;' width='100%'>
<tbody>
<tr>
<td>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'>
<tbody>
<tr>
<td>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px; margin: 0 auto;' width='600'>
<tbody>
<tr>
<td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'>
<table border='0' cellpadding='0' cellspacing='0' class='image_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'>
<tr>
<td class='pad' style='width:100%;padding-right:0px;padding-left:0px;'>
<div align='left' class='alignment' style='line-height:10px'>
<div style='max-width: 120px;'><img src='system0/html/php/login/v3/css/MicrosoftTeams-image (16).png' style='display: block; height: auto; border: 0; width: 100%;' width='120'/></div>
</div>
</td>
</tr>
</table>
<div class='spacer_block block-2' style='height:20px;line-height:20px;font-size:1px;'> </div>
<table border='0' cellpadding='10' cellspacing='0' class='heading_block block-3' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'>
<tr>
<td class='pad'>
<h1 style='margin: 0; color: #ffffff; direction: ltr; font-family: Arial, Helvetica, sans-serif; font-size: 38px; font-weight: 700; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0; mso-line-height-alt: 45.6px;'><span class='tinyMce-placeholder'>Emailadresse bestätigen</span></h1>
</td>
</tr>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-2' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'>
<tbody>
<tr>
<td>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px; margin: 0 auto;' width='600'>
<tbody>
<tr>
<td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'>
<table border='0' cellpadding='0' cellspacing='0' class='image_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'>
<tr>
<td class='pad' style='width:100%;'>
<div align='center' class='alignment' style='line-height:10px'>
<div style='max-width: 125px;'><img alt='I'm an image' src='images/1.png' style='display: block; height: auto; border: 0; width: 100%;' title='I'm an image' width='125'/></div>
</div>
</td>
</tr>
</table>

<table border='0' cellpadding='10' cellspacing='0' class='text_block block-3' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;' width='100%'>
<tr>
<td class='pad'>
<div style='font-family: sans-serif'>
<div class='' style='font-size: 12px; font-family: Arial, Helvetica, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;'>
<p style='margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 16.8px;'>Hallo $username<br>Hier ist dein System0 Account verifikations Link. Bitte klicke drauf. Sollte dies nicht funktionieren, kopiere bitte den Link und öffne Ihn in deinem Browser.<br>Achtung: der Link funktioniert nur in dem gleichen Browser und Gerät, auf dem du deinen Account erstellt hast.
</div>
</div>
</td>
</tr>
</table>
<table border='0' cellpadding='10' cellspacing='0' class='button_block block-4' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'>
<tr>
<td class='pad'>
<div align='center' class='alignment'>
<div style='text-decoration:none;display:inline-block;color:#ffffff;background-color:#0d7e00;border-radius:4px;width:auto;border-top:0px solid transparent;font-weight:undefined;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:Arial, Helvetica, sans-serif;font-size:16px;text-align:center;mso-border-alt:none;word-break:keep-all;'><span href='https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/verify_account.php?token=$token' style='padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;'><span style='margin: 0; word-break: break-word; line-height: 32px;'>Email bestätigen</span></span></div>
</div>
</td>
</tr>
</table>
    
<table border='0' cellpadding='10' cellspacing='0' class='text_block block-3' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;' width='100%'>
<tr>
<td class='pad'>
<div style='font-family: sans-serif'>
<div class='' style='font-size: 12px; font-family: Arial, Helvetica, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;'>
<p style='margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 16.8px;'>Vielen dank für dein Vertrauen in uns!<br>Code Camp 2024<br></p>
</div>
</div>
</td>
</tr>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</table>
</tbody>
</tr>
</td>
</body>
</html>


EOD;
?>

if($error==="success")
{
        header("LOCATION: https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/overview.php");

}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
$color="";
$banned=0;
$banned_reason="";
$telegram_id="";
$notification_telegram=0;
$notification_mail=0;
//resend account verify mail
if(isset($_GET["resend_acc_verify"])){
			//we need to resend the accont verification lin
			$_SESSION["creation_token"]= urlencode(bin2hex(random_bytes(24/2)));
			$token=$_SESSION["creation_token"];
			if(isset($_SESSION["verify"])){
				$username=$_SESSION["verify"];
				//send the mail:
				$mail=<<<EOF

curl --request POST \
  --url https://api.sendgrid.com/v3/mail/send \
  --header "Authorization: Bearer $SENDGRID_API_KEY" \
  --header 'Content-Type: application/json' \
  --data '{"personalizations": [{"to": [{"email": "$username"}]}],"from": {"email": "$sendgrid_email"},"subject": "System0 Account Validation","content": [{"type": "text/html", "value": $validation_mail}]}'

EOF;
				exec($mail);
				header("location: /system0/html/php/login/v3/login.php?mail_sent1");	
			}
			else{
				header("location: /system0/html/php/login/v3/login.php?mail_sent3");
			}
		}
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST" and $_GET["action"]=="login"){
 
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
        $sql = "SELECT id, username, password, role, color,banned,banned_reason ,telegram_id,notification_telegram,notification_mail FROM users WHERE username = ?";
        
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
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role,$color,$banned,$banned_reason,$telegram_id,$notification_telegram,$notification_mail);
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
				    $_SESSION["creation_token"]= urlencode(bin2hex(random_bytes(24/2)));
					$_SESSION["telegram_id"]=$telegram_id;
					$_SESSION["notification_telegram"]=$notification_telegram;
					$_SESSION["notification_mail"]=$notification_mail;
		                    // Redirect user to welcome page
		                        log_("$username logged in","LOGIN:SUCCESS");
		                        header("location:https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/overview.php");
		                }
		                else
		                {
		                	$login_err = "Dein Account wurde noch nicht aktiviert: $banned_reason";
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
// Processing form data when form is submitted and user wants to create new user
if($_SERVER["REQUEST_METHOD"] == "POST" and $_GET["action"]=="create_user"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_@.]+$/', trim($_POST["username"]))){
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
    // Validate kantimail
    if(strpos($_POST["username"],"@kantiwattwil.ch")===false){
        $err = "Only members of KSW can access this site. (prename.name@kantiwattwil.ch).";     
    } 
    // Check input errors before inserting in database
    if(empty($err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, role,banned,banned_reason,notification_telegram,notification_mail) VALUES (?, ?, ?,?,?,?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            $banned=1;
	    $banned_reason="Account muss zuerst verifiziert werden (Link in Mail)";
		$tel=0;
		$mail=1;
            mysqli_stmt_bind_param($stmt, "sssisii", $param_username, $param_password, $role,$banned,$banned_reason,$tel,$mail);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $role="11100000000";
            $banned=1;
		$tel=0;
		$mail=1;
	    $banned_reason="Account muss zuerst verifiziert werden (Link in Mail)";
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
		if(!is_dir("/var/www/html/system0/html/user_files/$username"))
                	mkdir("/var/www/html/system0/html/user_files/$username");
	    //create session token, which has account creation token inisde it.
	    $_SESSION["creation_token"]= urlencode(bin2hex(random_bytes(24/2)));
	    $token=$_SESSION["creation_token"];
	    $_SESSION["verify"]=$username;
	    $_SESSION["email"]=$username;
	    //send the mail:
	    $mail=<<<EOF

curl --request POST \
  --url https://api.sendgrid.com/v3/mail/send \
  --header "Authorization: Bearer $SENDGRID_API_KEY" \
  --header 'Content-Type: application/json' \
  --data '{"personalizations": [{"to": [{"email": "$username"}]}],"from": {"email": "$sendgrid_email"},"subject": "System0 Account Validation","content": [{"type": "text/html", "value": "Hallo $username<br>Hier ist dein System0 Account verifikations Link. Bitte klicke drauf. Sollte dies nicht funktionieren, kopiere bitte den Link und öffne Ihn in deinem Browser.<br><a href='https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/verify_account.php?token=$token'>https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/verify_account.php?token=$token</a><br>Achtung: der Link funktioniert nur in dem gleichen Browser und Gerät, auf dem du deinen Account erstellt hast.<br><br>Vielen dank für dein Vertrauen in uns!<br>Code Camp 2024<br>"}]}'

EOF;

	    exec($mail);

                header("location: /system0/html/php/login/v3/login.php?mail_sent1");
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
if($_SERVER["REQUEST_METHOD"] == "POST" and $_GET["action"]=="reset_pw"){
	$email=htmlspecialchars($_POST["username"]);
	$_SESSION["email"]=$email;
	$_SESSION["pw_reset_token"]= urlencode(bin2hex(random_bytes(24 / 2)));
	$token=$_SESSION["pw_reset_token"];
	$_SESSION["verify"]=$email;
	$mail=<<<EOF
curl --request POST \
  --url https://api.sendgrid.com/v3/mail/send \
  --header "Authorization: Bearer $SENDGRID_API_KEY" \
  --header 'Content-Type: application/json' \
  --data '{"personalizations": [{"to": [{"email": "$email"}]}],"from": {"email": "$sendgrid_email"},"subject": "System0 Password reset","content": [{"type": "text/html", "value": "Hallo $email<br>Hier ist dein System0 Passwort Zurücksetzungs Link. Bitte klicke drauf. Sollte dies nicht funktionieren, kopiere bitte den Link und öffne Ihn in deinem Browser.<br><a href='https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/reset_pw.php?token=$token'>https://3dprint.ksw-informatik.ch/system0/html/php/login/v3/php/reset_pw.php?token=$token</a><br>Achtung: der Link funktioniert nur in dem gleichen Browser und Gerät, auf dem du deinen Account erstellt hast.<br><br>Vielen dank für dein Vertrauen in uns!<br>Code Camp 2024<br>"}]}'

EOF;

	    exec($mail);
		header("location: /system0/html/php/login/v3/login.php?mail_sent2");
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
	<!-- Modal Fehler -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Info</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body m-3" style="height: auto;">
        <!-- Bitte vergrössern Sie das Browserfenster für den optimalen Gebrauch der Website! -->
		Das System hat Ihr Gerät als Mobile erkannt. Das Interface wurde zu "mobile_view" geändert.<br>
		Achtung "mobile_view" ist noch in Entwicklung.
      </div>
    </div>
  </div>
</div>
	
<!--<p id="window"></p>-->

<script>
	var galleryModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
	  keyboard: false
	});

	// Funktion zur Überprüfung der Fenstergröße
	function checkWindowSize() {
	  var windowWidth = window.innerWidth;
	
	  if (windowWidth < 1000) {
	    fetch("/system0/html/php/login/v3/php/init_mobile_view.php")
	    galleryModal.show();  
	  } else {
	    galleryModal.hide();
	  }
	}

	// Initialer Aufruf beim Laden der Seite
	checkWindowSize();

	// Ereignislistener für das Ändern der Fenstergröße
	//window.addEventListener('resize', checkWindowSize);
</script>
	
	<div class="d-flex align-items-center justify-content-left bg-dark" style="height:8vh;">
		<img src="/system0/html/php/login/v3/css/MicrosoftTeams-image (16).png" alt="Logo" class="img-fluid p-2" style="height:40px; width:auto;">
	</div>
	<div class="d-flex align-items-center justify-content-center" style="height:92vh;">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-md-6">
						<h3 class="text-center">Login</h3>
						<form action="login.php?action=login" method="post">
							<div class="mb-3">
								<label for="username" class="form-label">Benutzername:</label>
								<input type="text" class="form-control" id="username" name="username" value="<?php echo($username); ?>" required>
							</div>
							<div class="mb-3">
								<label for="pwd" class="form-label">Passwort:</label>
								<input type="password" class="form-control" id="pwd" name="password" required>
							</div>
							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" id="keepmeloggedin" name="keepmeloggedin" value="keepmeloggedin">
								<label class="form-check-label" for="keepmeloggedin">Angemeldet bleiben</label>
							</div>
							<button type="submit" name="submit" class="btn btn-dark">Login</button>
						</form>
						<div class="text-center mt-3">
							<button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#noaccount" id="lnk_1">Noch kein Account? Erstelle einen!</button>
							<button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#reset_pw" id="lnk_2">Passwort vergessen?</button>
						</div>
						<?php 
							if(!empty($login_err)){
							echo '<div class="alert alert-danger">' . $login_err . '</div>';
							}   
							if(isset($_GET["mail_sent1"]))
							echo '<div class="alert alert-success">Eine Mail mit einem Aktivierungslink wurde an deine Mailadresse gesendet.</div>';
							if(isset($_GET["mail_sent2"]))
							echo '<div class="alert alert-success">Eine Mail mit einem Passwort zurücksetzungslink wurde an deine Mailadresse gesendet.</div>';
							if(isset($_GET["acc_verify_ok"]))
							echo '<div class="alert alert-success">Email erfolgreich Verifiziert.</div>';
							if(isset($_GET["mail_sent3"]))
							echo '<div class="alert alert-danger">Eine Mail mit einem Passwort zurücksetzungslink konnte nich gesendet werden. Bitte melde dich beim Support <a href="mailto:info.jakach@gmail.com">hier.</a></div>';
						       
				    
						?>
					</div>
				</div>
			</div>
		</div>


		<div class="modal fade" id="noaccount" tabindex="1" role="dialog" aria-labelledby="Account" aria-hidden="false">
		      <div class="modal-dialog" role="document">
		        <div class="modal-content">
		          <div class="modal-header">
		            <h5 class="modal-title" id="exampleModalLabel">Account Erstellen</h5>
		            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		
		          </div>
				<div class="modal-body">
				  
		            <!-- Account things-->
					<form action="login.php?action=create_user" method="post">
						<div class="form-group mb-3">
							<label for="username" class="form-label">Email:</label>
							<input type="text" class="form-control" id="username" name="username" value="<?php echo($username) ?>" required>
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
	<div class="modal fade" id="reset_pw" tabindex="1" role="dialog" aria-labelledby="Account" aria-hidden="false">
		      <div class="modal-dialog" role="document">
		        <div class="modal-content">
		          <div class="modal-header">
		            <h5 class="modal-title" id="exampleModalLabel">Passwort Zurücksetzen</h5>
		            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		
		          </div>
				<div class="modal-body">
					<form action="login.php?action=reset_pw" method="post">
						<div class="form-group mb-3">
							<label for="username" class="form-label">Deine Account Email:</label>
							<input type="text" class="form-control" id="username" name="username" value='<?php echo($_SESSION["email"]); ?>' required>
					  	</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="submit" class="btn btn-dark">Passwort zurücksetzlink senden</button>
				</div>
				  </div>
				</form>
			</div>
		</div>
<?php
		if(!empty($err)){
			echo("<script>");
				echo('const a=document.getElementById("lnk_1");');
				echo('a.click();');
			echo("</script>");
		}
		if(isset($_GET["resend_pw_reset"])){
			echo("<script>");
				echo('const a=document.getElementById("lnk_2");');
				echo('a.click();');
			echo("</script>");
		}

		?>

	<div id="footer"></div>
        
</body>
</html>

