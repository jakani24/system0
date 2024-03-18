<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Server Stats</title>
</head>
<body>
<div id="stats"></div>
<script>
function refreshStats() {
    location.reload();
}

// Refresh stats every 1 second
setInterval(refreshStats, 1000);

</script>
<?php

// Get server load
$load = sys_getloadavg();

// Get CPU usage
$cpu_usage = shell_exec("top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\\([0-9.]*\\)%* id.*/\\1/' | awk '{print 100 - $1\"%\"}'");

// Get RAM usage
$ram_usage = shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0\"%\"}'");

// Display results
echo "Server Load: " . implode(", ", $load) . "<br>";
echo "CPU Usage: " . $cpu_usage . "<br>";
echo "RAM Usage: " . $ram_usage . "<br>";

?>

</body>
</html>
