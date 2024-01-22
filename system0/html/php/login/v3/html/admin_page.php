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
  </script>
</head>
<body>
  <div id="page-container">
    <nav class="navbar navbar-fixed-top navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="/system0/html/index.php">
          <img src="/system0/html/php/login/v3/css/MicrosoftTeams-image (16).png" width="auto" height="30" alt="Logo">
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bars" style="color: #2E6A2F;"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-dark">
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/bugreport.php">Fehler melden</a>
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/create_admin.php">Neue Admin erstellen</a>
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/remove_user.php">User enfernen</a>
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/print.php">Datei drucken</a>
		<!--<a class="dropdown-item" href="/system0/html/php/login/v3/php/job.php">Meine Druckvorgänge</a>-->
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/all_jobs.php">Alle Druckvorgänge</a>
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/view_log.php">View system0 Log</a>
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/view_apikey.php">View the system0 API Key</a>
		 <a class="dropdown-item" href="/system0/html/php/login/v3/php/create_key.php">Druckschlüssel erstellen</a>
		<a class="dropdown-item" href="/system0/html/php/login/v3/php/cloud.php">Deine Dateien</a>
              </ul>
            </li>
          </ul>
          <li class="d-flex">
            <a class="btn" role="button" data-bs-toggle="modal" data-bs-target="#account"><i class="fa-solid fa-gear" style="color: #2E6A2F;"></i></a>
            <a href="/system0/html/php/login/v3/logout.php" class="btn" role="button"><i class="fa-solid fa-right-from-bracket" style="color: #2E6A2F;"></i></a>
          </li>
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
            <?php 
              $color=$_SESSION["color"]; 
              
            ?>


            <?php
              if(isset($_POST["color"])) {
                $color=$_POST["color"];
                if(strlen($color)>45) {
                  die("invalid length");
                } else {
                  $sql = "UPDATE users SET color = ? WHERE username = ?";
                  $stmt = mysqli_prepare($link, $sql);
                  mysqli_stmt_bind_param($stmt, "ss", $color, htmlspecialchars($_SESSION["username"]));
                  mysqli_stmt_execute($stmt);
                  mysqli_stmt_close($stmt);
                  mysqli_close($link);
                  $_SESSION["color"]=$color;
                  header("location: #");
                }
              } else {
                $color=$_SESSION["color"];
              }
            ?>

            <div id="content"></div>

            <!-- Account things -->
            <div class="container mt-2 ml-2">
              <div class="row justify-content-center">
                <div class="col-md-6 p-4">
                  <a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/reset-password.php" role="button">Passwort zurücksetzen</a><br>
                  <a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/delete-account.php" role="button">Account und alle dazugehörigen Daten löschen</a>
                  <br><a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/php/privacy-policy.php" role="button">Unsere Privacy Policy</a>
                  <br>
                  <a class="btn btn-dark btn-block m-2" href="/system0/html/php/login/v3/php/disclaimer.php" role="button">Dislcaimer</a>
                  <br><br>

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
