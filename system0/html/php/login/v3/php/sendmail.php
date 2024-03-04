<?php
	
	$username="sys0_autonomous";
	//no auth, we only check if any printe rhas finished and if yes, if we should send a mail, if yes send the mail.

	include "config.php";
	include "queue.php";
	test_queue($link);

	//iterate over all printers and receive theyr status
	

	$cnt=0;
	$url="";
	$apikey="";
	$sql="select count(*) from printer where printing=1";
	$stmt = mysqli_prepare($link, $sql);					
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $cnt);
	mysqli_stmt_fetch($stmt);	
	//echo($cnt);
	$is_free=0;					
	$last_id=0;	
	$mail_sent=1;	
	$used_by_userid=0;			
	while($cnt!=0)
	{	

		$sql="select free,id,printer_url,apikey,cancel,used_by_userid,mail_sent from printer where id>$last_id and printing=1 ORDER BY id";
		$cancel=0;
		$stmt = mysqli_prepare($link, $sql);					
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $is_free,$printer_id,$url,$apikey,$cancel,$used_by_userid,$mail_sent);
		mysqli_stmt_fetch($stmt);
		$last_id=$printer_id;

		//printer is printing
		exec("curl --max-time 10 $url/api/job?apikey=$apikey > /var/www/html/system0/html/user_files/$username/json.json");
		$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
		$json=json_decode($fg,true);
		
		
		$used_by_user="";
		$sql="select username from users where id=$used_by_userid";
		$stmt = mysqli_prepare($link, $sql);					
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $used_by_user);
		mysqli_stmt_fetch($stmt);
		$username3=explode("@",$used_by_user);
		$username2=$username3[0];
		$progress=(int) $json['progress']['completion'];
		if($progress<0)
			$progress=-$progress;
		$file=$json['job']['file']['name'];
		if($progress==100){
				//print finished
				//check if mail has not been sent:
				if($mail_sent==0)
				{

					echo("sending mail for printer $printer_id to $used_by_user<br>");
					$mail=<<<EOF
curl --request POST \
  --url https://api.sendgrid.com/v3/mail/send \
  --header "Authorization: Bearer $SENDGRID_API_KEY" \
  --header 'Content-Type: application/json' \
  --data '{"personalizations": [{"to": [{"email": "$used_by_user"}]}],"from": {"email": "janis.steiner@kantiwattwil.ch"},"subject": "Dein 3D-Druck ist fertig","content": [{"type": "text/html", "value": "Hallo $username2<br>Dein 3D-Druck auf Drucker $printer_id ist fertig.<br>Bitte hole diesen ab und vergiss nicht den Drucker danach freizugeben!<br><br>Vielen dank für dein Vertrauen in uns!<br>Jakach Software 2024<br>https://jakach.duckdns.org"}]}'
EOF;
					$out="";
					exec($mail,$out);
					$sql="update printer set mail_sent=1 where id=$printer_id";
					$stmt = mysqli_prepare($link, $sql);					
					mysqli_stmt_execute($stmt);
				}
		}
		else if($cancel==1){
				//print cancelled
		}								
		//else: print still running
		$cnt--;
	}


?>
