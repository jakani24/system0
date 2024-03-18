<?php
	function test_queue($link)//function to check if any printer is free and if there are jobs in queue. If yes push jo to printer
	{
		
		$sql="select id, from_userid,filepath,print_on from queue order by id";
		$qid=0;
		$quserid=0;
		$qfilepath="";
		$print_on=0;
		$stmt = mysqli_prepare($link, $sql);
		//echo "test".mysqli_error($link);					
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $qid,$quserid,$qfilepath,$print_on);
		mysqli_stmt_fetch($stmt);
		//echo(":".$qid.":".$quserid.":".$qfilepath);
		
		
		//get number of printers
		$num_of_printers=0;
		if($print_on==-1)
			$sql="select count(*) from printer";
		else
			$sql="select count(*) from printer where id=$print_on";
		$stmt = mysqli_prepare($link, $sql);					
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $num_of_printers);
		mysqli_stmt_fetch($stmt);
		$last_id=0;
		$printers_av=0;
		if($num_of_printers!=0)
		{
			$id=0;
			$papikey="";
			$userid=$_SESSION["id"];
			$username=$_SESSION["username"];
			$purl="";
			if($print_on==-1)
				$sql="Select id,apikey,printer_url from printer where id>$last_id and free=1 order by id";
			else
				$sql="Select id,apikey,printer_url from printer where id=$print_on and free=1";
			//echo $sql;
			$stmt = mysqli_prepare($link, $sql);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $id,$papikey,$purl);
			mysqli_stmt_fetch($stmt);
			if($id!=0)
			{
				exec('curl -k -H "X-Api-Key: '.$papikey.'" -F "select=true" -F "print=true" -F "file=@'.$qfilepath.'" "'.$purl.'/api/files/local" > /var/www/html/system0/html/user_files/'.$username.'/json.json');
				$fg=file_get_contents("/var/www/html/system0/html/user_files/$username/json.json");
				$json=json_decode($fg,true);
				if($json['effectivePrint']==true and $json["effectiveSelect"]==true)
				{
					$sql="update printer set free=0, printing=1,mail_sent=0, used_by_userid=$quserid where id=$id";
					$stmt = mysqli_prepare($link, $sql);				
					mysqli_stmt_execute($stmt);
					
					$sql="delete from queue where id=$qid";
					$stmt = mysqli_prepare($link, $sql);				
					mysqli_stmt_execute($stmt);
				}
				else
				{
					//error in upload
					//echo("err1");
				}
				
				//upload to a free printer;
			}
		}
		if($num_of_printers==0)
		{
			//no printer free
			//echo("err2");
		}
		

	}

?>
