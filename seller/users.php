<?php
require_once '../includes/functions.php';
require_login(true);
if (!is_superadmin()) {
    flash_set('danger', 'Only superadmins can manage admin users.');
    header('Location: /seller/index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'admin';
        $address = trim($_POST['address'] ?? '');
        $contacts = trim($_POST['contact_numbers'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($name === '' || $email === '' || $address === '' || $contacts === '') {
            flash_set('danger', 'Fill in all required fields.');
        } elseif (!in_array($role, ['buyer', 'admin', 'superadmin'], true)) {
            flash_set('danger', 'Invalid role.');
        } else {
            if ($id > 0) {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $mysqli->prepare('UPDATE users SET full_name=?, email=?, password_hash=?, address=?, contact_numbers=?, role=? WHERE id=?');
                    $stmt->bind_param('ssssssi', $name, $email, $hash, $address, $contacts, $role, $id);
                } else {
                    $stmt = $mysqli->prepare('UPDATE users SET full_name=?, email=?, address=?, contact_numbers=?, role=? WHERE id=?');
                    $stmt->bind_param('sssssi', $name, $email, $address, $contacts, $role, $id);
                }
                $stmt->execute();
                $stmt->close();
                audit_log('update_user', 'Updated user ID ' . $id . ' role ' . $role);
                flash_set('success', 'User updated.');
            } else {
                $hash = password_hash($password ?: 'ChangeMe123!', PASSWORD_DEFAULT);
                $verified = 1;
                $stmt = $mysqli->prepare('INSERT INTO users (full_name, email, password_hash, address, contact_numbers, role, email_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
                $stmt->bind_param('ssssssi', $name, $email, $hash, $address, $contacts, $role, $verified);
                $stmt->execute();
                $stmt->close();
                audit_log('create_user', 'Created admin user ' . $email);
                flash_set('success', 'User added.');
            }
        }
        header('Location: /seller/users.php');
        exit;
    }
}
$users = $mysqli->query('SELECT id, full_name, email, role, address, contact_numbers, email_verified FROM users ORDER BY created_at DESC');
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $mysqli->prepare('SELECT id, full_name, email, role, address, contact_numbers FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
include '../includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card card-shadow"><div class="card-body p-4">
      <h4><?php echo $edit ? 'Edit User' : 'Add User'; ?></h4>
      <form method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo h($edit['id'] ?? 0); ?>">
        <div class="mb-2"><label class="form-label">Full name</label><input class="form-control" name="full_name" value="<?php echo h($edit['full_name'] ?? ''); ?>" required></div>
        <div class="mb-2"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo h($edit['email'] ?? ''); ?>" required></div>
        <div class="mb-2"><label class="form-label">Password</label><input type="password" class="form-control" name="password" placeholder="Leave blank to keep current"></div>
        <div class="mb-2"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="2" required><?php echo h($edit['address'] ?? ''); ?></textarea></div>
        <div class="mb-2"><label class="form-label">Contact numbers</label><input class="form-control" name="contact_numbers" value="<?php echo h($edit['contact_numbers'] ?? ''); ?>" required></div>
        <div class="mb-3"><label class="form-label">Role</label>
          <select class="form-select" name="role">
            <?php foreach (['buyer','admin','superadmin'] as $role): ?>
              <option <?php echo (($edit['role'] ?? 'admin') === $role) ? 'selected' : ''; ?>><?php echo h($role); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="btn btn-primary">Save</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-7">
    <div class="card card-shadow"><div class="card-body p-4">
      <h4>Users</h4>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Verified</th><th></th></tr></thead>
          <tbody>
          <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
              <td><?php echo h($u['full_name']); ?></td>
              <td><?php echo h($u['email']); ?></td>
              <td><?php echo h($u['role']); ?></td>
              <td><?php echo ((int)$u['email_verified'] === 1) ? 'Yes' : 'No'; ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo (int)$u['id']; ?>">Edit</a></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div></div>
  </div>
</div>
