<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'fisch');
define('DB_PASSWORD', 'tinte');
define('DB_NAME', 'system0');
$api="bot6975511033:AAGGswiwKYwCVbehpGE3hz_tLc9xuSAoBVg";
$SENDGRID_API_KEY="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";
$sendgrid_email="3dprint@ksw-informatik.ch";
$chat_id="6587711215";
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
