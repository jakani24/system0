# Installation to your server
1) On your Webserver create the following sql structure:<br>
The 'users' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
   <tr><td>username</td><td>varchar(50)</td><td>YES</td><td>UNI</td><td>NULL</td><td></td></tr>
   <tr><td>password</td><td>varchar(255)</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
   <tr><td>role</td><td>varchar(50)</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>created_at</td><td>datetime</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>keepmeloggedin</td><td>varchar(255)</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>color</td><td>varchar(50)</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>banned</td><td>int</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>banned_reason</td><td>varchar(255)</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
</table>


The 'printer' table:<br>
<table>
 <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
  <tr><td>printing</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
   <tr><td>free</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
   <tr><td>used_by_userid</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
   <tr><td>printer_url</td><td>varchar(255)</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>apikey</td><td>varchar(255)</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>cancel</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
</table>

The 'queue' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>from_userid</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
   <tr><td>filepath</td><td>varchar(255)</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
</table>
2) I don't know how the product will look like at the end, so I don't know step 2 yet!


# Installation to your Octoprint machine<br>
1) If you're planning to access the machine from outside of your local network do the following:<br>
  run this command to install ngrok: `curl -s https://ngrok-agent.s3.amazonaws.com/ngrok.asc | sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null && echo "deb https://ngrok-agent.s3.amazonaws.com buster main" | sudo tee /etc/apt/sources.list.d/ngrok.list && sudo apt update && sudo apt install ngrok`<br>
  add your ngrok auth token: `ngrok config add-authtoken TOKEN`<br>
  start ngrok with the following command: `ngrok http 80`<br>
  paste the following code into a script and run it: `url=$(curl --silent --show-error http://127.0.0.1:4040/api/tunnels | sed -nE 's/.*public_url":"https:..([^"]*).*/\1/p' ) \ echo $url \ curl --silent https://jakach.duckdns.org/system0/html/api/update_url?url=$url&id=ENTER_THE_ID_FOR_THIS_MACHINE_HERE`<br>
  and replace the ENTER_THE_ID_FOR_THIS_MACHINE_HERE with a positive integer. This integer will be used to identifie the printer later on. <br>
  if you run the command with an id that is already taken it will overwrite the old entry!<br>
  The machine should now be registret at system0.

<br><br>
2) If you will only use system0 on a local network:<br> 
    just skip the ngrok part and execute the commands with the ip of your device.<br>
