<?php
// Database configuration
// Update these values for your hosting account.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'office_equipment_store');

define('SITE_NAME', 'Apex Office Supply');
define('GROUP_NAME', 'Group Apex');
define('BASE_URL', '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>