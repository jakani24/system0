# system0
Frontend system for octoprint<br>
This repo contains the code from system0 a frontend user controll system for octoprint<br>
The goal of system0 is to be a system that can controll multiple octoprint 3d printers at the same time.<br>
The system has to be able to register users (students) and must have the functionallity to upload files etc. for printing.
Additionally it should be able to have something like a queue so even if no printer is "free" users can upload theyr files and the system will automaticli send it to a printer as soon as a printer is "free".

<br>
System0 is mainly written for ksw (Kantonsschule Wattwil ST. Gallen CH-switzerland)


# usability
Due to the fact that this project will mainly be used at KSW it won't be to easy to set up.<br>
You will need to have root access on both the octoprint as well as the webserver.<br>
Additionally you will need to be able to install ngrok onto your octoprint server (NOT the plugin but the standalone app)<br>
As well as the mysql databases for user and printermanagement.

# warning
My code often looks horribly and uses outdated methods.<br>
So be prepared for ugly code.

# User management
System0 has its own user management with users and admins.<br>
But due to the modular design of system0 it should not be hard to add new usertypes or permissions.<br>
The user management of octoprint is not used. All the API calls go throu the admin api key of octoprint.<br>

# How does it work
![image](https://github.com/jakani24/system0/assets/89935073/2a0be6d8-3f16-40ec-8317-873ceecc0ec5)

