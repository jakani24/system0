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
</table>

2) I don't know how the product will look like at the end, so I don't know step 2 yet!
