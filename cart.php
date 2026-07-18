<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $pid => $qty) {
            $qty = max(0, (int)$qty);
            if ($qty === 0) {
                unset($_SESSION['cart'][$pid]);
            } else {
                $_SESSION['cart'][$pid] = $qty;
            }
        }
        flash_set('success', 'Cart updated.');
        header('Location: /cart.php');
        exit;
    }
    if (isset($_POST['clear_cart'])) {
        clear_cart();
        flash_set('success', 'Cart cleared.');
        header('Location: /cart.php');
        exit;
    }
}
$cart = get_cart();
$items = [];
$total = 0;
if ($cart) {
    $products = get_products_by_ids(array_keys($cart));
    foreach ($products as $row) {
        $row['qty'] = $cart[(int)$row['id']];
        $row['subtotal'] = $row['qty'] * (float)$row['price'];
        $total += $row['subtotal'];
        $items[] = $row;
    }
}
include 'includes/header.php';
?>
<h3>Your Cart</h3>
<?php if (!$items): ?>
  <div class="alert alert-info">Your cart is empty.</div>
<?php else: ?>
<form method="post">
  <div class="card card-shadow mb-3">
    <div class="card-body">
      <?php foreach ($items as $item): ?>
        <div class="row align-items-center border-bottom py-3">
          <div class="col-md-6">
            <strong><?php echo h($item['name']); ?></strong><br>
            <small><?php echo money($item['price']); ?></small>
          </div>
          <div class="col-md-2">
            <input class="form-control" type="number" min="0" name="qty[<?php echo (int)$item['id']; ?>]" value="<?php echo (int)$item['qty']; ?>">
          </div>
          <div class="col-md-2 text-end"><?php echo money($item['subtotal']); ?></div>
          <div class="col-md-2 text-end"><span class="badge text-bg-secondary">Stock: <?php echo (int)$item['stock']; ?></span></div>
        </div>
      <?php endforeach; ?>
      <div class="d-flex justify-content-between mt-3">
        <strong>Total</strong><strong><?php echo money($total); ?></strong>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary" name="update_cart">Update Cart</button>
    <button class="btn btn-outline-danger" name="clear_cart">Clear Cart</button>
    <a class="btn btn-success ms-auto" href="/checkout.php">Proceed to Checkout</a>
  </div>
</form>
<?php endif; ?>
