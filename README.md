# website-mailer
This project involves implementing a web page that allows a logged-in user to schedule messages to be sent at a specified date and time. 
The user can enter an email address, select a date and time to the nearest half-hour using drop-down menus, and add a message. The message and timestamp are then saved in a server-side database and later sent to the specified email address.

The project consists of two PHP scripts: lab4.php and mailer.php. The former provides the web page for scheduling messages, while the latter is executed by a Cron job on the server every half hour to send any messages whose timestamp is less-than-or-equal-to the current time. Before using the scheduling form, a user must register with a username and password.

To use this project, you will need to configure a CRON job on your server to execute mailer.php at the desired interval.
