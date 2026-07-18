<?php
require_once '../includes/functions.php';
require_login(true);
include '../includes/header.php';
?>
<div class="row g-4">
  <div class="col-md-4"><div class="card card-shadow h-100"><div class="card-body"><h5>User Administration</h5><p>Add and modify admin users for seller roles.</p><a href="users.php" class="btn btn-dark">Manage Users</a></div></div></div>
  <div class="col-md-4"><div class="card card-shadow h-100"><div class="card-body"><h5>Inventory Management</h5><p>Update products, stock counts, and prices.</p><a href="products.php" class="btn btn-dark">Manage Stocks</a></div></div></div>
  <div class="col-md-4"><div class="card card-shadow h-100"><div class="card-body"><h5>Reports</h5><p>View remaining items and your own audit log entries.</p><a href="reports.php" class="btn btn-dark">View Reports</a></div></div></div>
</div>
