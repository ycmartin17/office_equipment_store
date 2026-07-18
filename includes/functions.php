<?php
require_once __DIR__ . '/config.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function current_user_id() {
    return is_logged_in() ? (int)$_SESSION['user']['id'] : 0;
}

function is_admin() {
    return is_logged_in() && in_array($_SESSION['user']['role'], ['admin', 'superadmin'], true);
}

function is_superadmin() {
    return is_logged_in() && $_SESSION['user']['role'] === 'superadmin';
}

function require_login($adminOnly = false) {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
    if ($adminOnly && !is_admin()) {
        http_response_code(403);
        die('Access denied.');
    }
}

function flash_set($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function audit_log($action, $details = '') {
    global $mysqli;
    if (!is_logged_in()) {
        return;
    }
    $stmt = $mysqli->prepare('INSERT INTO audit_log (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())');
    $uid = current_user_id();
    $stmt->bind_param('iss', $uid, $action, $details);
    $stmt->execute();
    $stmt->close();
}

function send_confirmation_email($email, $name, $token) {
    $subject = SITE_NAME . ' Email Confirmation';
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/verify.php?token=' . urlencode($token);
    $message = "Hello $name,

Please confirm your email address by clicking this link:
$link

If you did not create an account, you can ignore this email.";
    $headers = "From: no-reply@" . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
    @mail($email, $subject, $message, $headers);
}

function get_categories() {
    global $mysqli;
    $rows = [];
    $result = $mysqli->query('SELECT * FROM categories ORDER BY name');
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function get_products($category_id = null) {
    global $mysqli;
    $products = [];
    if ($category_id) {
        $stmt = $mysqli->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.name');
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.name, p.name');
    }
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    if (isset($stmt)) {
        $stmt->close();
    }
    return $products;
}

function get_product($id) {
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row;
}

function get_products_by_ids($ids) {
    global $mysqli;
    $ids = array_map('intval', $ids);
    if (!$ids) return [];
    $idList = implode(',', $ids);
    $rows = [];
    $result = $mysqli->query("SELECT * FROM products WHERE id IN ($idList)");
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function get_cart() {
    return $_SESSION['cart'] ?? [];
}

function cart_count() {
    $cart = get_cart();
    $count = 0;
    foreach ($cart as $qty) {
        $count += (int)$qty;
    }
    return $count;
}

function cart_total() {
    $cart = get_cart();
    if (!$cart) return 0;
    $ids = array_keys($cart);
    $products = get_products_by_ids($ids);
    $prices = [];
    foreach ($products as $p) {
        $prices[(int)$p['id']] = (float)$p['price'];
    }
    $total = 0;
    foreach ($cart as $id => $qty) {
        $total += ($prices[(int)$id] ?? 0) * (int)$qty;
    }
    return $total;
}

function clear_cart() {
    unset($_SESSION['cart']);
}

function money($amount) {
    return 'PHP ' . number_format((float)$amount, 2);
}
?>