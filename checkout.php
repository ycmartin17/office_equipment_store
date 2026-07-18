<?php
require_once 'includes/functions.php';
require_login(false);
$cart = get_cart();
if (!$cart) {
    flash_set('danger', 'Your cart is empty.');
    header('Location: /store.php');
    exit;
}
$user = current_user();
$total = cart_total();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['checkout_notes'] = trim($_POST['notes'] ?? '');
    audit_log('checkout_started', 'Checkout page opened');
    header('Location: /payment.php');
    exit;
}
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-shadow"><div class="card-body p-4">
      <h3>Checkout</h3>
      <p><strong>Name:</strong> <?php echo h($user['full_name']); ?><br>
      <strong>Email:</strong> <?php echo h($user['email']); ?><br>
      <strong>Address:</strong> <?php echo h($user['address'] ?? ''); ?></p>
      <div class="alert alert-secondary">Order total: <strong><?php echo money($total); ?></strong></div>
      <form method="post">
        <div class="mb-3"><label class="form-label">Order notes</label><textarea class="form-control" name="notes" rows="3" placeholder="Optional delivery instructions"></textarea></div>
        <button class="btn btn-success">Continue to Payment</button>
      </form>
    </div></div>
  </div>
</div>
