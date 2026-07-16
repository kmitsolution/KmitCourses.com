<?php
// Logging function
function logEvent($message) {
    $logFile = 'logs/website.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $logEntry = "[$timestamp] IP: $ip | User-Agent: $userAgent | $message\n";
    error_log($logEntry, 3, $logFile);
}

// Log the 404 error
$requestedUrl = $_SERVER['REQUEST_URI'] ?? 'unknown';
logEvent("404 Not Found: $requestedUrl");

// Display a simple 404 page
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Not Found</title>
</head>
<body>
    <h1>404 - Page Not Found</h1>
    <p>The page you are looking for does not exist.</p>
    <a href="default.php">Go to Home</a>
</body>
</html>