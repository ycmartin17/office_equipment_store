<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'address' => $user['address'],
            'contact_numbers' => $user['contact_numbers']
        ];
        audit_log('login', 'Logged in successfully');
        flash_set('success', 'Welcome back, ' . $user['full_name'] . '!');
        if (in_array($user['role'], ['admin', 'superadmin'], true)) {
            header('Location: /seller/index.php');
        } else {
            header('Location: /store.php');
        }
        exit;
    } elseif ($user && (int)$user['email_verified'] === 0) {
        flash_set('danger', 'Please confirm your email first.');
    } else {
        flash_set('danger', 'Invalid login details.');
    }
}
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card card-shadow"><div class="card-body p-4">
      <h3 class="mb-3">Login</h3>
      <form method="post">
        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
        <button class="btn btn-primary">Login</button>
      </form>
    </div></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>