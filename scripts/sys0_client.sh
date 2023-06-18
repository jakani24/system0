#!/bin/bash
ngrok http 80 &
sleep 5
url=$(curl --silent --show-error http://127.0.0.1:4040/api/tunnels | sed -nE 's/.*public_url":"https:..([^"]*).*/\1/p' )
curl --silent https://SYSTEM0_WEBSERVER_URL/system0/html/api/update_url?url=$url&id=ENTER_THE_ID_FOR_THIS_MACHINE_HERE&apikey=YOUR_SYSTEM0_APIKEY&octoapikey=OCTOPRINT_ADMIN_APIKEY
