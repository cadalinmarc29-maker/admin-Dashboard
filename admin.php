<?php
require 'config.php';

$adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$setupError = '';

if ($adminCount === 0 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_admin'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $setupError = 'Enter a name, valid email, and password with at least 6 characters.';
    } else {
        $existing = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $existing->execute([$email]);
        $user = $existing->fetch(PDO::FETCH_ASSOC);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($user) {
            $createAdmin = $pdo->prepare('UPDATE users SET role = ?, password = ?, full_name = ? WHERE user_id = ?');
            $createAdmin->execute(['admin', $hash, $fullName, $user['user_id']]);
            $_SESSION['user_id'] = $user['user_id'];
        } else {
            $createAdmin = $pdo->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
            $createAdmin->execute([$fullName, $email, $hash, 'admin']);
            $_SESSION['user_id'] = $pdo->lastInsertId();
        }

        $_SESSION['full_name'] = $fullName;
        header('Location: admin.php');
        exit;
    }
}

if ($adminCount > 0) {
    require 'includes/admin_auth.php';
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $itemId = (int) ($_POST['item_id'] ?? 0);
    $stock = max(0, (int) ($_POST['stock'] ?? 0));
    $updateStock = $pdo->prepare('UPDATE menu_items SET stock = ? WHERE item_id = ?');
    $updateStock->execute([$stock, $itemId]);
    $message = 'Stock updated.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';
    if (in_array($status, ['Pending', 'Preparing', 'Completed', 'Cancelled'], true)) {
        $updateStatus = $pdo->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
        $updateStatus->execute([$status, $orderId]);
        $message = 'Order status updated.';
    }
}

$items = $pdo->query('SELECT item_id, item_name, price, stock FROM menu_items ORDER BY item_name')->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT o.order_id, o.order_number, o.total_amount, o.status, o.created_at,
    u.full_name, u.email, i.item_name, oi.quantity, oi.unit_price
    FROM orders o
    JOIN users u ON u.user_id = o.user_id
    JOIN order_items oi ON oi.order_id = o.order_id
    JOIN menu_items i ON i.item_id = oi.item_id
    ORDER BY o.created_at DESC, o.order_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$orderStats = $pdo->query("SELECT COUNT(*) AS order_count, COALESCE(SUM(total_amount), 0) AS sales_total FROM orders")->fetch(PDO::FETCH_ASSOC);
$lowStockCount = (int) $pdo->query('SELECT COUNT(*) FROM menu_items WHERE stock <= 5')->fetchColumn();
$memberships = $pdo->query('SELECT membership_id, full_name, email, plan, created_at FROM memberships ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
$messages = $pdo->query('SELECT message_id, name, email, message, created_at FROM messages ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<section class="club-section auth-section">
    <h2>Admin Dashboard</h2>
    <?php if ($adminCount === 0): ?>
        <div class="form-card">
            <h3>Create the first admin account</h3>
            <?php if ($setupError): ?><div class="alert alert-error"><?= htmlspecialchars($setupError) ?></div><?php endif; ?>
            <form method="post">
                <label for="full_name">Full Name</label>
                <input id="full_name" name="full_name" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" required>
                <button class="btn btn-block" name="setup_admin" type="submit">CREATE ADMIN</button>
            </form>
        </div>
    <?php else: ?>
        <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <div class="admin-stats">
            <div class="admin-stat"><strong><?= (int) $orderStats['order_count'] ?></strong><span>Total orders</span></div>
            <div class="admin-stat"><strong>&#8369;<?= number_format((float) $orderStats['sales_total'], 2) ?></strong><span>Total sales</span></div>
            <div class="admin-stat"><strong><?= count($memberships) ?></strong><span>Coffee club members</span></div>
            <div class="admin-stat"><strong><?= count($messages) ?></strong><span>Contact messages</span></div>
            <div class="admin-stat"><strong><?= $lowStockCount ?></strong><span>Low-stock products</span></div>
        </div>

        <div class="admin-card">
            <h3>Stock</h3>
            <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Action</th></tr></thead><tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td>&#8369;<?= number_format($item['price'], 2) ?></td>
                        <td class="<?= (int) $item['stock'] <= 5 ? 'stock-low' : '' ?>"><?= (int) $item['stock'] ?></td>
                        <td><form method="post" class="admin-inline-form"><input type="hidden" name="item_id" value="<?= (int) $item['item_id'] ?>"><input type="number" name="stock" min="0" value="<?= (int) $item['stock'] ?>"><button class="btn small-btn" name="update_stock" type="submit">SAVE</button></form></td>
                    </tr>
                <?php endforeach; ?>
            </tbody></table></div>
        </div>
        <div class="admin-card">
            <h3>All Purchases</h3>
            <?php if (!$orders): ?><p>No purchases yet.</p><?php endif; ?>
            <?php if ($orders): ?><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Product</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>
                <?php foreach ($orders as $order): ?>
                    <tr><td><?= htmlspecialchars($order['order_number']) ?></td><td><?= htmlspecialchars($order['full_name']) ?><br><small><?= htmlspecialchars($order['email']) ?></small></td><td><?= htmlspecialchars($order['item_name']) ?> x <?= (int) $order['quantity'] ?></td><td>&#8369;<?= number_format($order['total_amount'], 2) ?></td><td><form method="post" class="admin-inline-form"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><select name="status"><?php foreach (['Pending', 'Preparing', 'Completed', 'Cancelled'] as $status): ?><option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= $status ?></option><?php endforeach; ?></select><button class="btn small-btn" name="update_status" type="submit">UPDATE</button></form></td><td><?= htmlspecialchars($order['created_at']) ?></td></tr>
                <?php endforeach; ?>
            </tbody></table></div><?php endif; ?>
        </div>
        <div class="admin-card">
            <h3>Coffee Club Members</h3>
            <?php if (!$memberships): ?><p>No club members yet.</p><?php else: ?><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Plan</th><th>Joined</th></tr></thead><tbody><?php foreach ($memberships as $member): ?><tr><td><?= htmlspecialchars($member['full_name']) ?></td><td><?= htmlspecialchars($member['email']) ?></td><td><?= htmlspecialchars($member['plan']) ?></td><td><?= htmlspecialchars($member['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
        </div>
        <div class="admin-card">
            <h3>Contact Messages</h3>
            <?php if (!$messages): ?><p>No contact messages yet.</p><?php else: ?><?php foreach ($messages as $contact): ?><article class="admin-message"><p><strong><?= htmlspecialchars($contact['name']) ?></strong> &lt;<?= htmlspecialchars($contact['email']) ?>&gt; <small><?= htmlspecialchars($contact['created_at']) ?></small></p><p><?= nl2br(htmlspecialchars($contact['message'])) ?></p></article><?php endforeach; ?><?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>