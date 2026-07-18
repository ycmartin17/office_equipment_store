<?php
require_once '../includes/functions.php';
require_login(true);
$inventory = $mysqli->query('SELECT p.name, c.name AS category_name, p.stock, p.price FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.name, p.name');
$uid = current_user_id();
$stmt = $mysqli->prepare('SELECT a.action, a.details, a.created_at FROM audit_log a WHERE a.user_id = ? ORDER BY a.created_at DESC');
$stmt->bind_param('i', $uid);
$stmt->execute();
$audit = $stmt->get_result();
$stmt->close();
include '../includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-shadow"><div class="card-body p-4">
      <h4>Inventory Report</h4>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead><tr><th>Item</th><th>Category</th><th>Remaining</th><th>Price</th></tr></thead>
          <tbody>
            <?php while ($row = $inventory->fetch_assoc()): ?>
              <tr>
                <td><?php echo h($row['name']); ?></td>
                <td><?php echo h($row['category_name']); ?></td>
                <td><?php echo (int)$row['stock']; ?></td>
                <td><?php echo money($row['price']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div></div>
  </div>
  <div class="col-lg-6">
    <div class="card card-shadow"><div class="card-body p-4">
      <h4>My Audit Log</h4>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead><tr><th>Date</th><th>Action</th><th>Details</th></tr></thead>
          <tbody>
            <?php while ($row = $audit->fetch_assoc()): ?>
              <tr>
                <td><?php echo h($row['created_at']); ?></td>
                <td><?php echo h($row['action']); ?></td>
                <td><?php echo h($row['details']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div></div>
  </div>
</div>
