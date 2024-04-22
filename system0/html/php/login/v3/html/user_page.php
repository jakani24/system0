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
	
	<style>
		.green {
			color: #2E6A2F;
			text-decoration: none;
		} 
		.green:hover,
		.green:active {
			color: #017D1A;
			text-decoration: none;
		}
	</style>

</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-dark">
		<div class="container-fluid">
			<a class="navbar-brand" href="/system0/html/index.php">
				<img src="/system0/html/php/login/v3/css/MicrosoftTeams-image (16).png" width="auto" height="30" alt="Logo">
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				</ul>
				<ul class="navbar-nav">
					<?php
						session_start();
						echo('
							<li class="nav-item">
								<a class="btn btn-link green" href="/system0/html/php/login/v3/php/bugreport.php">Fehler melden</a>
							</li>
							');
					
						if($_SESSION["role"][0]==="1")
							echo('
								<li class="nav-item">
									<a class="btn btn-link green" href="/system0/html/php/login/v3/php/print.php">Datei drucken</a>
								</li>
								');
						if($_SESSION["role"][8]==="1")
							echo('
								<li class="nav-item">
									<a class="btn btn-link green" href="/system0/html/php/login/v3/php/create_key.php">Druckschlüssel erstellen</a>
								</li>
								');
		
						if($_SESSION["role"][1]==="1")
							echo('
								<li class="nav-item">
									<a class="btn btn-link green" href="/system0/html/php/login/v3/php/cloud.php">Deine Dateien</a>
								</li>
								');
					
						if($_SESSION["role"][2]==="1")
							echo('
								<li class="nav-item">
									<a class="btn btn-link green" href="/system0/html/php/login/v3/php/public_cloud.php">Öffentliche Dateien</a>
								</li>
								');
						?>
					<li class="nav-item">
						<a class="btn green" role="button" data-bs-toggle="modal" data-bs-target="#account"><i class="fa-solid fa-gear" style="color: #2E6A2F;"></i></a>
						<a href="/system0/html/php/login/v3/logout.php" class="btn me-2 green" role="button"><i class="fa-solid fa-right-from-bracket" style="color: #2E6A2F;"></i></a>
					</li>
				</ul>
			</div>
		</div>
	</nav>




<div class="modal fade" id="account" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-sm-down modal-fullscreen-lg-down" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Account Einstellungen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>
			<div class="modal-body">


						
				<div class="container">
					<div class="d-flex flex-wrap justify-content-center">
						<?php
							session_start();
							echo('
								<div class="card m-2" style="width: 14em;" href="#">
									<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
										<i class="fa-solid fa-lock fa-8x justify-content-center"></i>
									</div>

									<div class="card-body">
										<h5 class="card-title">Passwort zurücksetzen</h5>
										<p class="card-text">Setzen Sie Ihr Passwort zurück, wenn Sie es vergessen haben oder wenn Sie es aus Sicherheitsgründen zurücksetzen möchten.</p>
										<a href="/system0/html/php/login/v3/reset-password.php" class="stretched-link">weiter</a>
									</div>

								</div>
								');
						
							if($_SESSION["role"][5]==="1")
								echo('
									<div class="card m-2" style="width: 14em;" href="#">
										<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
											<i class="fa-solid fa-user-plus fa-8x justify-content-center"></i>
										</div>
										<div class="card-body">
											<h5 class="card-title">Neuer Admin erstellen</h5>
											<p class="card-text">Erstellen Sie ein neues Administratorkonto, um Systemeinstellungen und Benutzer zu verwalten.</p>
											<a href="/system0/html/php/login/v3/php/create_admin.php" class="stretched-link">weiter</a>
										</div>

									</div>
									');
						
							if($_SESSION["role"][4]==="1")
								echo('

									<div class="card m-2" style="width: 14em;" href="#">
										<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
											<i class="fa-solid fa-user-minus fa-8x justify-content-center"></i>
										</div>
										<div class="card-body">
											<h5 class="card-title">Benutzer entfernen</h5>
											<p class="card-text">Entfernen Sie ein Benutzerkonto aus dem System.</p>
											<a href="/system0/html/php/login/v3/php/remove_user.php" class="stretched-link">weiter</a>
										</div>

									</div>
									');
							echo('
								<div class="card m-2" style="width: 14em;" href="#">
									<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
										<i class="fa-solid fa-trash fa-8x justify-content-center"></i>
									</div>
									<div class="card-body">
										<h5 class="card-title">Account löschen</h5>
										<p class="card-text">Konto und alle damit verbundenen Daten dauerhaft löschen.</p>
										<a href="/system0/html/php/login/v3/delete-account.php" class="stretched-link">weiter</a>
									</div>

								</div>
								');
						
							echo('
								<div class="card m-2" style="width: 14em;" href="#">
									<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
										<i class="fa-solid fa-shield-halved fa-8x justify-content-center"></i>
									</div>
									<div class="card-body">
										<h5 class="card-title">Datenschutzrichtlinie</h5>
										<p class="card-text">Lesen Sie unsere Datenschutzrichtlinie, um zu erfahren, wie wir mit Ihren Daten umgehen.</p>
										<a href="/system0/html/php/login/v3/php/privacy-policy.php" class="stretched-link">weiter</a>
									</div>

								</div>
								');
						
							echo('
								<div class="card m-2" style="width: 14em;" href="#">
									<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
										<i class="fa-solid fa-circle-info fa-8x justify-content-center"></i>
									</div>
									<div class="card-body">
										<h5 class="card-title">Disclaimer</h5>
										<p class="card-text">Lesen Sie unseren Haftungsausschluss für wichtige Informationen zur Nutzung unserer Dienste.</p>
										<a href="/system0/html/php/login/v3/php/disclaimer.php" class="stretched-link">weiter</a>
									</div>

								</div>
								');
						
							if($_SESSION["role"][6]==="1")

								echo('
									<div class="card m-2" style="width: 14em;" href="#">
										<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
											<i class="fa-solid fa-file fa-8x justify-content-center"></i>
										</div>
										<div class="card-body">
											<h5 class="card-title">View system0 Log</h5>
											<p class="card-text">Zeigen Sie das Protokoll der Systemaktivitäten und -ereignisse an.</p>
											<a href="/system0/html/php/login/v3/php/view_log.php" class="stretched-link">weiter</a>
										</div>

									</div>
									');
						
							if($_SESSION["role"][7]==="1")                        	
								echo('
									<div class="card m-2" style="width: 14em;" href="#">
										<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
											<i class="fa-solid fa-key fa-8x justify-content-center"></i>
										</div>
										<div class="card-body">
											<h5 class="card-title">View the system0 API Key</h5>
											<p class="card-text">Zeigen Sie den API-Schlüssel an, der für den Zugriff auf die Systemfunktionalitäten verwendet wird.</p>
											<a href="/system0/html/php/login/v3/php/view_apikey.php" class="stretched-link">weiter</a>
										</div>

									</div>
								');

							if($_SESSION["role"][9]==="1")
								echo('
									<div class="card m-2" style="width: 14em;" href="#">
										<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
											<i class="fa-solid fa-key fa-8x justify-content-center"></i>
										</div>
										<div class="card-body">
											<h5 class="card-title">Debug</h5>
											<p class="card-text">Hier findest du das Debug-Tool.</p>
											<a href="/system0/html/php/login/v3/php/debug.php" class="stretched-link">weiter</a>
										</div>

									</div>
	 								');

	  				
								?>
							<div class="card m-2" style="width: 14em;" href="#">
								<div class="d-flex justify-content-center align-items-center card-img-top" style="height: 35vh;">
									<i class="fa-solid fa-message fa-8x justify-content-center"></i>
								</div>
								<div class="card-body">
									<input id="telegram_id" type="text" class="form-control mb-2" placeholder="Telegram Chat Id" value="<?php echo($_SESSION["telegram_id"]); ?>" oninput="update_telegram_id();">
									<div class="form-check form-switch">
										<?php if($_SESSION["notification_telegram"]==1)
echo('<input class="form-check-input" type="checkbox" id="notification_telegram" checked="true" onclick="update_notification(\'notification_telegram\');">');
										else
											echo('<input class="form-check-input" type="checkbox" id="notification_telegram"  onclick="update_notification(\'notification_telegram\');">');
										?>
										<label class="form-check-label" for="notification_telegram">Benachrichtigung via Telegram</label>
										<br>
										<?php 
										if($_SESSION["notification_mail"]==1)
											echo('<input class="form-check-input" type="checkbox" id="notification_mail" checked="true" onclick="update_notification(\'notification_mail\');">');
										else
											echo('<input class="form-check-input" type="checkbox" id="notification_mail"  onclick="update_notification(\'notification_mail\');">');
										?>
										<label class="form-check-label" for="notification_mail">Benachrichtigung via Mail</label>
									</div>	
								</div>

							</div>
					
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

</body>
</html>
