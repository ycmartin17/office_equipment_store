<?php
require_once 'includes/functions.php';
audit_log('logout', 'Logged out');
session_destroy();
header('Location: /index.php');
exit;