<?php
function logmein($link)
{
	//require_once "php/config.php";
	require_once "/var/www/html/system0/html/php/login/v3/log/log.php";
		 $cookie = isset($_COOKIE['keepmeloggedin']) ? $_COOKIE['keepmeloggedin'] : '';
		if ($cookie) {
			$data=explode(':', $cookie);
			$username=$data[0];
			$token=$data[1];
			$mac=$data[2];
			if (!hash_equals(hash('sha256', $username . ':' . $token), $mac)) {
				log_("Logged $username not in via autologin","LOGIN:AUTOLOGIN:FAILURE"); 
				return "error1";
			}
			//echo($username);
			$role="";
			$usertoken="";
			$id=0;
			$color="";
			$banned=0;
			$sql = "SELECT keepmeloggedin, role, id, color,banned FROM users WHERE username = ?";
			$username=htmlspecialchars($username);
			$stmt = mysqli_prepare($link, $sql);
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $usertoken,$role,$id,$color,$banned);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
			if ($usertoken!==$token) {
				log_("Logged $username not in via autologin","LOGIN:AUTOLOGIN:FAILURE"); 
				return "error2";
			}
			else
			{
				if($banned!=1)
				{
					session_start();
					$_SESSION["loggedin"] = true;
					$_SESSION["id"] = $id;
					$_SESSION["username"] = $username;       
					$_SESSION["role"] = $role; 
					$_SESSION["token"]=bin2hex(random_bytes(32));   
					$_SESSION["color"]=$color;  
					log_("Logged $username in via autologin","LOGIN:AUTOLOGIN:SUCCESS"); 
				}
				else
				{
					log_("Logged $username not in via autologin","LOGIN:AUTOLOGIN:FAILURE"); 
					return "error3";
				}
			}
		}		 
		 return $username;
}

?>
