<?php
// config.php — Database connection settings for KMIT Courses
$host = 'localhost';
$db   = 'kmit_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define('BASE_URL', '/mysite/'); // Adjust to your local folder
} else {
    define('BASE_URL', '/');
}
function getPDO() {
    global $dsn, $user, $pass, $options;
    return new PDO($dsn, $user, $pass, $options);
}
