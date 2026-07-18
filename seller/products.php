<?php
require_once '../includes/functions.php';
require_login(true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $category_id = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $image_url = trim($_POST['image_url'] ?? '');
        if ($name === '' || $desc === '' || $category_id <= 0) {
            flash_set('danger', 'Please complete the product form.');
        } elseif ($id > 0) {
            $stmt = $mysqli->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image_url=?, updated_at=NOW() WHERE id=?');
            $stmt->bind_param('issdisi', $category_id, $name, $desc, $price, $stock, $image_url, $id);
            $stmt->execute();
            $stmt->close();
            audit_log('update_product', 'Updated product ID ' . $id);
            flash_set('success', 'Product updated.');
        } else {
            $stmt = $mysqli->prepare('INSERT INTO products (category_id, name, description, price, stock, image_url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->bind_param('issdis', $category_id, $name, $desc, $price, $stock, $image_url);
            $stmt->execute();
            $stmt->close();
            audit_log('create_product', 'Added product ' . $name);
            flash_set('success', 'Product added.');
        }
        header('Location: /seller/products.php');
        exit;
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    audit_log('delete_product', 'Deleted product ID ' . $id);
    flash_set('success', 'Product deleted.');
    header('Location: /seller/products.php');
    exit;
}
$categories = get_categories();
$products = get_products();
$edit = null;
if (isset($_GET['edit'])) {
    $edit = get_product((int)$_GET['edit']);
}
include '../includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card card-shadow"><div class="card-body p-4">
      <h4><?php echo $edit ? 'Edit Stock' : 'Add Stock'; ?></h4>
      <form method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo h($edit['id'] ?? 0); ?>">
        <div class="mb-2"><label class="form-label">Category</label>
          <select class="form-select" name="category_id" required>
            <option value="">Select</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo (int)$cat['id']; ?>" <?php echo (($edit['category_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>><?php echo h($cat['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2"><label class="form-label">Name</label><input class="form-control" name="name" value="<?php echo h($edit['name'] ?? ''); ?>" required></div>
        <div class="mb-2"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3" required><?php echo h($edit['description'] ?? ''); ?></textarea></div>
        <div class="row">
          <div class="col-md-6 mb-2"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" name="price" value="<?php echo h($edit['price'] ?? ''); ?>" required></div>
          <div class="col-md-6 mb-2"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" value="<?php echo h($edit['stock'] ?? ''); ?>" required></div>
        </div>
        <div class="mb-3"><label class="form-label">Image URL</label><input class="form-control" name="image_url" value="<?php echo h($edit['image_url'] ?? ''); ?>"></div>
        <button class="btn btn-primary">Save</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-7">
    <div class="card card-shadow"><div class="card-body p-4">
      <h4>Stocks</h4>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?php echo h($p['name']); ?></td>
              <td><?php echo h($p['category_name']); ?></td>
              <td><?php echo money($p['price']); ?></td>
              <td><?php echo (int)$p['stock']; ?></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo (int)$p['id']; ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo (int)$p['id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div></div>
  </div>
</div>
