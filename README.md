# Real-time-log-viewer
A simple PHP file that shows the logs you specify, in real-time, in a web browser. 

Built initially to show log files for AzerothCore, but it can be used for basically any set of log files that are stored in the same folder.

![Alt text](logviewer.jpg "logviewer screenshot")

# How to use
Copy the logviewer.php file to your web folder somewhere, preferably in a folder that is protected with a username and password.
Open the logviewer.php file in a text editor, and specify these variables at the top of the file.

$log_folder = '/your/folder/location';  // Set this to the location of your log files
$refresh_timer = 3000;  // Refresh every XX seconds. (in milliseconds, 3000 = 3 secs)
$tail_lines    = 50;  // How many lines it wil show

// Specify what log files to display. These must be present in the logs folder.
$logs = [
    'Server'     => $log_folder . 'Server.log',
    'Errors'     => $log_folder . 'Errors.log',
    'Playerbots' => $log_folder . 'Playerbots.log',
    'Auth'       => $log_folder . 'Auth.log'

That's it! Easy as pie!
