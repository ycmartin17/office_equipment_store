<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $contacts = trim($_POST['contact_numbers'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirm === '' || $address === '' || $contacts === '') {
        flash_set('danger', 'Please complete all fields.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash_set('danger', 'Please enter a valid email address.');
    } elseif ($password !== $confirm) {
        flash_set('danger', 'Passwords do not match.');
    } else {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($exists) {
            flash_set('danger', 'Email already registered.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = generate_token(32);
            $role = 'buyer';
            $stmt = $mysqli->prepare('INSERT INTO users (full_name, email, password_hash, address, contact_numbers, role, email_verified, verify_token, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, ?, NOW())');
            $stmt->bind_param('sssssss', $name, $email, $hash, $address, $contacts, $role, $token);
            $stmt->execute();
            $stmt->close();
            send_confirmation_email($email, $name, $token);
            flash_set('success', 'Registration successful. Please check your email to confirm your account.');
            header('Location: /login.php');
            exit;
        }
    }
}
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card card-shadow">
      <div class="card-body p-4">
        <h3 class="mb-3">Buyer Registration</h3>
        <form method="post">
          <div class="mb-3"><label class="form-label">Complete name</label><input class="form-control" name="full_name" required></div>
          <div class="mb-3"><label class="form-label">E-mail address</label><input type="email" class="form-control" name="email" required></div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Confirm password</label><input type="password" class="form-control" name="confirm_password" required></div>
          </div>
          <div class="mb-3"><label class="form-label">Complete address</label><textarea class="form-control" name="address" rows="3" required></textarea></div>
          <div class="mb-3"><label class="form-label">Contact numbers</label><input class="form-control" name="contact_numbers" placeholder="09xx-xxx-xxxx, 09xx-xxx-xxxx" required></div>
          <button class="btn btn-primary">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>