<?php require_once __DIR__ . '/functions.php'; $flash = flash_get(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h(SITE_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/index.php">
      <img src="/assets/logo.svg" alt="logo" class="logo">
      <span><?php echo h(SITE_NAME); ?></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/store.php">Store</a></li>
        <li class="nav-item"><a class="nav-link" href="/cart.php">Cart (<?php echo cart_count(); ?>)</a></li>
        <li class="nav-item"><a class="nav-link" href="/about.php">About</a></li>
        <?php if (is_logged_in()): ?>
          <?php if (is_admin()): ?><li class="nav-item"><a class="nav-link" href="/seller/index.php">Seller Area</a></li><?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
<?php if ($flash): ?>
  <div class="alert alert-<?php echo h($flash['type']); ?>"><?php echo h($flash['message']); ?></div>
<?php endif; ?>