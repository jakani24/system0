<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'fisch');
define('DB_PASSWORD', 'tinte');
define('DB_NAME', 'system0');
$api="";
$SENDGRID_API_KEY="";
$sendgrid_email="";
$chat_id="";
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
