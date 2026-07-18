<?php
require_once 'includes/functions.php';
require_login(false);
$cart = get_cart();
if (!$cart) {
    flash_set('danger', 'Your cart is empty.');
    header('Location: /store.php');
    exit;
}
$total = cart_total();
$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_method'] ?? 'Cash on Delivery';
    $address = $_POST['delivery_address'] ?? ($user['address'] ?? '');

    $mysqli->begin_transaction();
    try {
        $orderNumber = 'ORD-' . date('YmdHis') . '-' . random_int(1000, 9999);
        $status = 'Pending';
        $stmt = $mysqli->prepare('INSERT INTO orders (user_id, order_number, total_amount, status, payment_method, delivery_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $uid = current_user_id();
        $stmt->bind_param('isdsss', $uid, $orderNumber, $total, $status, $method, $address);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        foreach ($cart as $pid => $qty) {
            $product = get_product((int)$pid);
            if (!$product || (int)$product['stock'] < (int)$qty) {
                throw new Exception('Insufficient stock for ' . ($product['name'] ?? 'a product'));
            }
            $stmt = $mysqli->prepare('INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)');
            $price = (float)$product['price'];
            $stmt->bind_param('iiid', $order_id, $pid, $qty, $price);
            $stmt->execute();
            $stmt->close();

            $newStock = (int)$product['stock'] - (int)$qty;
            $stmt = $mysqli->prepare('UPDATE products SET stock = ?, updated_at = NOW() WHERE id = ?');
            $stmt->bind_param('ii', $newStock, $pid);
            $stmt->execute();
            $stmt->close();
        }

        audit_log('order_created', 'Order ' . $orderNumber . ' total ' . $total);
        $mysqli->commit();
        clear_cart();
        flash_set('success', 'Payment recorded and order placed successfully.');
        header('Location: /store.php');
        exit;
    } catch (Throwable $e) {
        $mysqli->rollback();
        flash_set('danger', 'Could not place order: ' . $e->getMessage());
    }
}
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-shadow"><div class="card-body p-4">
      <h3>Payment</h3>
      <p>Order total: <strong><?php echo money($total); ?></strong></p>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Payment method</label>
          <select class="form-select" name="payment_method">
            <option>Cash on Delivery</option>
            <option>Bank Transfer</option>
            <option>Over the Counter</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Delivery address</label>
          <textarea class="form-control" name="delivery_address" rows="3"><?php echo h($user['address'] ?? ''); ?></textarea>
        </div>
        <div class="alert alert-warning">No payment API is used yet. This page only records the selected payment method for the project requirement.</div>
        <button class="btn btn-primary">Place Order</button>
      </form>
    </div></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>