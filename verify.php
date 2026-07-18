<?php
require_once 'includes/functions.php';
$token = $_GET['token'] ?? '';
if ($token !== '') {
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE verify_token = ? LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($user) {
        $stmt = $mysqli->prepare('UPDATE users SET email_verified = 1, verify_token = NULL WHERE id = ?');
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $stmt->close();
        flash_set('success', 'Email confirmed successfully. You may now log in.');
    } else {
        flash_set('danger', 'Invalid confirmation link.');
    }
}
header('Location: /login.php');
exit;