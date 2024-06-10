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
  <tr><td>notification_telegram</td><td>int</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>notification_mail</td><td>int</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>telegram_id</td><td>text</td><td>YES</td><td></td><td>NULL</td><td></td></tr>
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
  <tr><td>system_status</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>mail_sent</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>rotation</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>color</td><td>TEXT</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
</table>

The 'queue' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>from_userid</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
   <tr><td>filepath</td><td>varchar(255)</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>print_on</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
</table>

The 'api' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
  <tr><td>apikey</td><td>varchar(255)</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
 </table>

 The 'print_key' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
  <tr><td>print_key</td><td>varchar(50)</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
 </table>

  The 'reservations' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
  <tr><td>set_by_userid</td><td>INT</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>time_from</td><td>TEXT</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>time_to</td><td>TEXT</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>day</td><td>TEXT</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
 </table>

   The 'filament' table:<br>
<table>
  <tr><th>Field</th><th>Type</th><th>NULL</th><th>Key</th><th>Default</th><th>Extra</th>  </tr>
  <tr><td>id</td><td>int</td><td>NO</td><td>PRI</td><td>NULL</td><td>auto_increment</td></tr>
  <tr><td>name</td><td>text</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
  <tr><td>internal_id</td><td>int</td><td>NO</td><td></td><td>NULL</td><td></td></tr>
 </table>

installation onto a raspberry pi:
2) Install apache2 webserver. `sudo apt install apache2`<br>
2) Install mysql. `sudo apt install mariadb-server`<br>
3) Install libapache2-mod-php. `sudo apt install libapache2-mod-php`<br>
4) Git clone the project. `git clone https://github.com/jakani24/system0`<br>
5) Copy the system0 folder to the webroot<br>
6) Run `chown www-data:www-data system0/html/php/login/v3/log`
7) Create the sql tables and an sql user all permissions on your tables and add the credentials of this user in "system0/html/php/login/v3/php/config.php".<br>
8) Add the apikeys to "system0/html/php/login/v3/php/config.php"


# Installation to your Octoprint machine<br>
1) If you're planning to access the machine from outside of your local network do the following:<br>
  a) run this command to install ngrok: `curl -s https://ngrok-agent.s3.amazonaws.com/ngrok.asc | sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null && echo "deb https://ngrok-agent.s3.amazonaws.com buster main" | sudo tee /etc/apt/sources.list.d/ngrok.list && sudo apt update && sudo apt install ngrok`<br><br>
  b) add your ngrok auth token: `ngrok config add-authtoken TOKEN`<br><br>
  c) start ngrok with the following command: `ngrok http 80`<br><br>
  d) Run the following command to register the printer to system0: `url=$(curl --silent --show-error http://127.0.0.1:4040/api/tunnels | sed -nE 's/.*public_url":"https:..([^"]*).*/\1/p' )` <br>
   `echo $url`<br>
   `curl --silent "https://SYSTEM0_WEBSERVER_URL/system0/html/api/update_url.php?url=$url&id=ENTER_THE_ID_FOR_THIS_MACHINE_HERE&apikey=YOUR_SYSTEM0_APIKEY&octoapikey=OCTOPRINT_ADMIN_APIKEY"`
  <br><br>
  e) and replace the ENTER_THE_ID_FOR_THIS_MACHINE_HERE with a positive integer. This integer will be used to identifie the printer later on. <br><br>
  f) if you run the command with an id that is already taken it will overwrite/update the old entry!<br>So you can for example make a script that updates the octoprint url once a day or after every reboot, because ngrok will give you a new url after every restart<br><br>
  The machine should now be registered at system0 and show up in the print selection as "Printer ID_YOU_GAVE_IT".

<br><br>
2) If you will only use system0 on a local network:<br> 
    just skip the ngrok part and execute the follwoing command with the ip of your device.<br>
  `curl --silent "https://SYSTEM0_WEBSERVER_URL/system0/html/api/update_url.php?url=IP_OF_YOUR_OCTOPRINT_MACHINE&id=ENTER_THE_ID_FOR_THIS_MACHINE_HERE&apikey=YOUR_SYSTEM0_APIKEY&octoapikey=OCTOPRINT_ADMIN_APIKEY"`
