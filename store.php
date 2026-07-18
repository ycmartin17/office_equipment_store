<?php
require_once 'includes/functions.php';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $pid = (int)$_POST['product_id'];
    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
    audit_log('add_to_cart', 'Added product ID ' . $pid . ' to cart');
    flash_set('success', 'Item added to cart.');
    header('Location: /store.php' . ($category_id ? '?category=' . $category_id : ''));
    exit;
}
$categories = get_categories();
$products = get_products($category_id ?: null);
include 'includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-3">
    <div class="card card-shadow sidebar-card"><div class="card-body">
      <h5>Categories</h5>
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action <?php echo $category_id ? '' : 'active'; ?>" href="/store.php">All Products</a>
        <?php foreach ($categories as $cat): ?>
          <a class="list-group-item list-group-item-action <?php echo $category_id === (int)$cat['id'] ? 'active' : ''; ?>" href="/store.php?category=<?php echo (int)$cat['id']; ?>"><?php echo h($cat['name']); ?></a>
        <?php endforeach; ?>
      </div>
    </div></div>
  </div>
  <div class="col-lg-9">
    <div class="row g-4">
      <?php foreach ($products as $product): ?>
        <div class="col-md-6 col-xl-4">
          <div class="card card-shadow h-100">
            <img class="card-img-top product-img" src="<?php echo h($product['image_url'] ?: 'https://via.placeholder.com/600x400?text=Office+Equipment'); ?>" alt="product">
            <div class="card-body d-flex flex-column">
              <small class="text-muted"><?php echo h($product['category_name']); ?></small>
              <h5 class="card-title"><?php echo h($product['name']); ?></h5>
              <p class="card-text flex-grow-1"><?php echo h($product['description']); ?></p>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong><?php echo money($product['price']); ?></strong>
                <span class="badge text-bg-secondary">Stock: <?php echo (int)$product['stock']; ?></span>
              </div>
              <form method="post">
                <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                <button class="btn btn-primary w-100" name="add_to_cart" <?php echo ((int)$product['stock'] <= 0) ? 'disabled' : ''; ?>>Add to cart</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>