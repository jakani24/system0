<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="system0/html/php/login/v3/components.php" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script>
    function load_footer() {
      $(document).ready(function(){
        $('#footer').load("/system0/html/php/login/v3/html/footer.html");
      });
    }
    load_footer();
	function update_telegram_id(){
		var a=document.getElementById("telegram_id");
		var tel_id=a.value;
		fetch("update_settings.php?telegram_id="+tel_id);
	}
	function update_notification(div_id){
		var a=document.getElementById(div_id);
		var tel_id=a.checked;
		fetch("update_settings.php?"+div_id+"="+tel_id);
	}	
  </script>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <a class="navbar-brand" href="/system0/html/index.php">
                <img src="/system0/html/php/login/v3/css/MicrosoftTeams-image (16).png" width="auto" height="30" alt="Logo">
            </a>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bars" style="color: #2E6A2F;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
			<?php
				session_start();
                        	echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/bugreport.php">Fehler melden</a></li>');
				if($_SESSION["role"][5]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/create_admin.php">Neue Admin erstellen</a></li>');
				if($_SESSION["role"][4]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/remove_user.php">User ändern</a></li>');
				if($_SESSION["role"][0]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/print.php">Datei drucken</a></li>');
				if($_SESSION["role"][6]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/view_log.php">View system0 Log</a></li>');
				if($_SESSION["role"][7]==="1")                        	
					echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/view_apikey.php">View the system0 API Key</a></li>');
				if($_SESSION["role"][8]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/create_key.php">Druckschlüssel erstellen</a></li>');
				if($_SESSION["role"][1]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/cloud.php">Deine Dateien</a></li>');
                        	if($_SESSION["role"][2]==="1")
					echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/public_cloud.php">Öffentliche Dateien</a></li>');
				if($_SESSION["role"][9]==="1")
                        		echo('<li><a class="dropdown-item" href="/system0/html/php/login/v3/php/debug.php">Debug</a></li>');

			?>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="d-flex">
            <a class="btn" role="button" data-bs-toggle="modal" data-bs-target="#account"><i class="fa-solid fa-gear" style="color: #2E6A2F;"></i></a>
            <a href="/system0/html/php/login/v3/logout.php" class="btn" role="button"><i class="fa-solid fa-right-from-bracket" style="color: #2E6A2F;"></i></a>
        </div>
    </div>
</nav>




<div class="modal fade" id="account" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Account Einstellungen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
		<div class="modal-body">
			<div class="container mt-2 ml-2">
				<div class="row justify-content-center">
					<div class="col-md-6 p-4">
						<a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/reset-password.php" role="button">Passwort zurücksetzen</a><br>
						<a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/delete-account.php" role="button">Account und alle dazugehörigen Daten löschen</a>
						<br><a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/php/privacy-policy.php" role="button">Unsere Privacy Policy</a>
						<br>
						<a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/php/disclaimer.php" role="button">Dislcaimer</a>
						<input id="telegram_id" type="text" class="form-control" placeholder="Telegram Chat Id" value="<?php echo($_SESSION["telegram_id"]); ?>" oninput="update_telegram_id();">
						<div class="form-check form-switch">
							<?php if($_SESSION["notification_telegram"]==1)
								echo('<input class="form-check-input" type="checkbox" id="notification_telegram" checked="true" onclick="update_notification(\'notification_telegram\');">');
  							else
								echo('<input class="form-check-input" type="checkbox" id="notification_telegram"  onclick="update_notification(\'notification_telegram\');">');
							?>
							<label class="form-check-label" for="notification_telegram">Benachrichtigung via Telegram</label>
							<br>
							<?php if($_SESSION["notification_mail"]==1)
								echo('<input class="form-check-input" type="checkbox" id="notification_mail" checked="true" onclick="update_notification(\'notification_mail\');">');
  							else
								echo('<input class="form-check-input" type="checkbox" id="notification_mail"  onclick="update_notification(\'notification_mail\');">');
							?>
							<label class="form-check-label" for="notification_mail">Benachrichtigung via Mail</label>
						</div>						
						<br><br>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>
