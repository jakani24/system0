<?php

	function log_($input,$type)
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
	   
	   //echo($ip);
	   $fp=fopen("/var/www/html/system0/html/php/login/v3/log/log.txt","a");
	   fwrite($fp,date(DATE_RFC2822));
	   fwrite($fp,"     ");
	   fwrite($fp,$ip);
	   fwrite($fp,"     ");
	   fwrite($fp,$type."::");
	   fwrite($fp,$input);
	   fwrite($fp,"\n");
	   fclose($fp);
	}
	function sys0_log($notes,$username,$type)
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
	   
	   //echo($ip);
	   $fp=fopen("/var/www/html/system0/html/php/login/v3/log/sys0.log","a");
	   fwrite($fp,date(DATE_RFC2822));
	   fwrite($fp,";");
	   fwrite($fp,$ip);
	   fwrite($fp,";");
	   fwrite($fp,$type.";");
	   fwrite($fp,$username.";");
	   fwrite($fp,$notes);
	   fwrite($fp,"\n");
	   fclose($fp);
	}
?>
