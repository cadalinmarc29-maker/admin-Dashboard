<?php
require 'config.php';
require 'includes/auth.php';

$activePage = 'menu';

$stmt = $pdo->prepare("
    SELECT o.order_id, o.order_number, o.total_amount, o.status, o.created_at,
           i.item_name, oi.quantity, oi.unit_price
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.order_id
    JOIN menu_items i ON i.item_id = oi.item_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC, o.order_id DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<section class="club-section">
    <h2>All Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-error">You have no orders yet.</div>
    <?php else: ?>
        <div class="form-card">
            <?php foreach ($orders as $order): ?>
                <div style="border-bottom:1px solid rgba(113,83,61,0.2); padding:12px 0;">
                    <p><strong>Order:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
                    <p><strong>Item:</strong> <?= htmlspecialchars($order['item_name']) ?></p>
                    <p><strong>Quantity:</strong> <?= (int) $order['quantity'] ?></p>
                    <p><strong>Price:</strong> ₱<?= number_format($order['unit_price'], 2) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>