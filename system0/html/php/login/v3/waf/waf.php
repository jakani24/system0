<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$block=false;
if($_SESSION["username"]!="janis" &&$block===true)
{
    echo("<p>We are sorry but jakach is currently down for maintenance</p>");
    die();
}
/*
 *
 *Anti XSS
 *
 */
// Define a list of characters that are not allowed in input
$disallowed_chars = array("'", "\"", ";", "<", ">", "{", "}", "[", "]", "$", "&", "|", "*", "+", "=", "%", "`");
$value="";
// Loop through each GET or POST parameter and check for disallowed characters
foreach ($_GET as $key => $value) {
    if (contains_disallowed_chars($value)) {
        echo("error-xss_get<br>");
        log_security_risk($value,$key);
        header('Location:/system0/html/php/login/v3/waf/forbidden.php');
        exit;
        die();
    }
}

foreach ($_POST as $key => $value) {
    if (contains_disallowed_chars($value)) {
        echo("error-xss_post<br>");
        log_security_risk($value,$key);
        header('Location:/system0/html/php/login/v3/waf/forbidden.php');
        exit;
        die();
    }
}
if(isset($_SESSION["token"])&&(isset($_POST['token']) or isset($_GET['token'])))
{
        if(! (hash_equals($_SESSION['token'], $_POST['token']) )and ! (hash_equals($_SESSION['token'], $_GET['token'])))
        {
            echo("error-csrf<br>");
            header('Location:/system0/html/php/login/v3/waf/forbidden.php');
            exit;
        }
}

/*
 *
 * Anti SQLi
 *
 */
    // Check for SQL injection
    foreach ($_POST as $key => $value) {
        if (preg_match('/(union|select|insert|update|delete|drop|create|rename|alter|\s+where\s+|\s+or\s+|\s+and\s+)/i', $value)) {
			echo("error-sql_post<br>");
            log_security_risk($value,$key);
            header('Location:/system0/html/php/login/v3/waf/forbidden.php');
            exit;
            die();
        }
    }

       foreach ($_GET as $key => $value) {
        if (preg_match('/(union|select|insert|update|delete|drop|create|rename|alter|\s+where\s+|\s+or\s+|\s+and\s+)/i', $value)) {
			echo("error-sql_get<br>");
            log_security_risk($value,$key);
            header('Location:/system0/html/php/login/v3/waf/forbidden.php');
            exit;
            die();
        }
    }
/*
 * 
 * Anti session hijacking
 * 
 */
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > 300) {
    session_regenerate_id(true);
    $_SESSION['last_activity'] = time();
}

// Set the user agent in the session
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

// Check if the user agent has changed
if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_destroy();
    header('Location: /system0/html/php/login/v3/logout.php');
    exit;
    die();
}
/*
 * 
 * 
 * Anti Dos and Ddos system
 * 
 * 
 */
 /*
$ip = $_SERVER['REMOTE_ADDR'];
$limit = 100; // Maximum number of requests allowed per minute
$cache_file = "/var/www/html/php/login/v3/log/ddos/ddos_$ip";
$expiry = time() + 300; // Expiry time in seconds (5 minutes)
$count = 0;
$cache_expires = 60; // Cache expiration time in seconds

if (file_exists($cache_file)) {

    // Check how many requests have been made in the last minute
    $cache_time = time() - filemtime($cache_file);
    if ($cache_time < $cache_expires) {
           $data = json_decode(file_get_contents($cache_file), true);
    if ($data['expiry'] > time()) {
        $count = $data['count'];
    } else {
        unlink($cache_file);
    }
    } else {
        // Cache has expired, reset the counter
        unlink($cache_file);
    } 


}

if ($count >= $limit) {
    // Too many requests - return 429 (Too Many Requests) response code
    echo("Your IP got blocked because of to many requests! (DDos protection)<br>Your IP is banned for 5 Minutes!");
    //log_security_risk("DDos preventation","DDos preventation");
    http_response_code(429);
    exit;
} else {
    // Increment the request count and save to cache file
    $count++;
    $data = array(
        'count' => $count,
        'expiry' => $expiry
    );
    file_put_contents($cache_file, json_encode($data));
}
*/
/*
 * 
 * Security funcitons 
 * 
 */
// Function to check if a string contains any disallowed characters
function contains_disallowed_chars($string) {
    global $disallowed_chars;
    foreach ($disallowed_chars as $char) {
        if (strpos($string, $char) !== false) {
            return true;
        }
    }
    return false;
}
function log_security_risk($dangerous_value,$dangerous_key)
{
   if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   }
   elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
         $ip = $_SERVER['HTTP_CLIENT_IP'];
   }
   else {
         $ip = $_SERVER['REMOTE_ADDR'];
   }
    $username=$_SESSION["username"];
    $file=$_SERVER["PHP_SELF"];
    $fp=fopen("/var/www/html/system0/html/php/login/v3/log/waf.log","a");
    if($fp!=NULL)
    {
        fwrite($fp,date(DATE_RFC2822));
        fwrite($fp,"     ");
        fwrite($fp,$ip);
        fwrite($fp,"     ");
        fwrite($fp,$username);
        fwrite($fp,"     ");
        fwrite($fp,$dangerous_key);
        fwrite($fp,"     ");
        fwrite($fp,$dangerous_value);
        fwrite($fp,"     ");
        fwrite($fp,$file);
        fwrite($fp,"\n");
        fclose($fp);
    }

}

?>
