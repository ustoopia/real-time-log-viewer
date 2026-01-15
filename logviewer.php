<?php
//
// CONFIGURATION
//
// Set this to the location of your log files
$log_folder = '/games/wow/azerothcore-wotlk/logs/';

// Refresh every XX seconds. (in milliseconds, 3000 = 3 secs)
$refresh_timer = 3000;

// How many lines it wil show
$tail_lines    = 50;

// Specify what log files to display, add other optionally. This will require setting them in wordlserver.conf first
$logs = [
    'Server'     => $log_folder . 'Server.log',
    'Errors'     => $log_folder . 'Errors.log',
    'Playerbots' => $log_folder . 'Playerbots.log',
    'Auth'       => $log_folder . 'Auth.log',
//    'DB'         => $log_folder . 'DB.log',
//    'GM'         => $log_folder . 'GM.log',
//    'Players'    => $log_folder . 'Players.log',
//    'PvP'        => $log_folder . 'PvP.log',
//    'Chat'       => $log_folder . 'Chat.log'
];

// AJAX request handlings
if (isset($_GET['ajax']) && isset($_GET['file'])) {
    $file_key = $_GET['file'];
    if (array_key_exists($file_key, $logs)) {
        $file_path = $logs[$file_key];
        
        if (file_exists($file_path)) {
            // Use the  $tail_lines variabele set in the config
            $output = shell_exec("tail -n " . (int)$tail_lines . " " . escapeshellarg($file_path));
            $data = htmlspecialchars($output);
            
            // 1. Highlight the dates (Darker gray)
            $data = preg_replace('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', '<span class="log-date">$1</span>', $data);
            
            // 2. Highlight the status
            $search  = ['ERROR', 'CRITICAL', 'FATAL', 'WARN', 'WARNING', 'INFO', 'DEBUG', 'CHAT'];
            $replace = [
                '<span class="log-error">$0</span>',
                '<span class="log-error">$0</span>',
                '<span class="log-error">$0</span>',
                '<span class="log-warn">$0</span>',
                '<span class="log-warn">$0</span>',
                '<span class="log-info">$0</span>',
                '<span class="log-debug">$0</span>',
                '<span class="log-chat">$0</span>'
            ];
            
            echo str_ireplace($search, $replace, $data);
        } else {
            echo "File not found: " . htmlspecialchars($file_key);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <title>DreCraft Logs</title>
  <style>
  body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background: #1a1a1a; 
    color: #d4d4d4; 
    margin: 0; 
    overflow: hidden; 
  }

  /* Navigation Tabs */
  .navbar { 
    display: flex; 
    flex-wrap: wrap;
    justify-content: center;
    background: #2d2d2d; 
    padding: 8px 10px 0 10px; 
    border-bottom: 2px solid #3e3e42; 
  }
  .tab-button {
    padding: 8px 10px; 
    cursor: pointer; 
    background: #3e3e42; 
    border: none; 
    color: #b0b0b0; 
    margin-right: 4px; 
    margin-bottom: 5px; 
    border-radius: 4px 4px 0 0; 
    font-size: 15px; 
    transition: 0.2s;
  }
  .tab-button:hover { 
    background: #4e4e52; 
    color: #ffffff; 
   }
  .tab-button.active { 
    background: #007acc; 
    color: #ffffff; 
    font-weight: bold; 
  }

  /* Log Window */
  #log-container { 
    padding: 15px; 
    height: calc(100vh - 60px); 
    overflow-y: auto; 
    box-sizing: border-box; 
    background: #1e1e1e; 
    display: flex; 
    flex-direction: column;
  }
  #log-output { 
    white-space: pre-wrap; 
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace; 
    font-size: 13px; 
    line-height: 1.5; 
    word-wrap: break-word;
  }

  /* Colors for the highlights */  
  .log-date { color: #555; }
  .log-error { color: #ff5555; font-weight: bold; }
  .log-warn { color: #ffb86c; font-weight: bold; }
  .log-info { color: #50fa7b; }
  .log-debug { color: #8be9fd; }
  .log-chat { color: #bd93f9; }

  ::-webkit-scrollbar { width: 10px; }
  ::-webkit-scrollbar-track { background: #1e1e1e; }
  ::-webkit-scrollbar-thumb { background: #333; border: 1px solid #444; }
  </style>
</head>
<body>

<div class="navbar">
    <?php foreach (array_keys($logs) as $index => $name): ?>
        <button class="tab-button <?= $index === 0 ? 'active' : '' ?>" onclick="switchTab('<?= $name ?>', this)">
            <?= $name ?>
        </button>
    <?php endforeach; ?>
</div>

<div id="log-container">
    <div id="log-output">Loading...</div>
</div>

<script>
    // Gets the PHP variable and converts it to a JS variable.
    const refreshInterval = <?= (int)$refresh_timer ?>;
    let currentLog = 'Server'; // Start the first key of the array
    let autoScroll = true;

    function switchTab(logName, element) {
        currentLog = logName;
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('log-output').innerHTML = "Loading...";
        fetchLogs();
    }

    function fetchLogs() {
        fetch(`?ajax=1&file=${currentLog}`)
            .then(response => response.text())
            .then(data => {
                const output = document.getElementById('log-output');
                const container = document.getElementById('log-container');
                output.innerHTML = data;
                if (autoScroll) {
                    container.scrollTop = container.scrollHeight;
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Use the refresh variable specified in the config.
    setInterval(fetchLogs, refreshInterval);

    const container = document.getElementById('log-container');
    container.addEventListener('scroll', () => {
        const atBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 50;
        autoScroll = atBottom;
    });

    window.onload = fetchLogs;
</script>

</body>
</html>
