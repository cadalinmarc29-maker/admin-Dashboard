<?php
require 'config.php';
require 'includes/auth.php';

$activePage = 'menu';
$successMsg = '';
$errorMsg = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['item_id']) && !empty($_POST['quantity'])) {
    $itemId = (int) $_POST['item_id'];
    $qty = (int) $_POST['quantity'];

    if ($qty < 1) {
        $errorMsg = 'Please enter a valid quantity.';
    } else {
        $itemStmt = $pdo->prepare("SELECT item_id, item_name, price, stock FROM menu_items WHERE item_id = ?");
        $itemStmt->execute([$itemId]);
        $item = $itemStmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $errorMsg = 'Selected item was not found.';
        } elseif ($qty > (int) $item['stock']) {
            $errorMsg = 'Not enough stock available for this item.';
        } else {
            $total = $item['price'] * $qty;
            $orderNumber = 'DV-' . date('YmdHis') . '-' . str_pad((string) rand(100, 999), 3, '0', STR_PAD_LEFT);

            $pdo->beginTransaction();
            try {
                $decreaseStock = $pdo->prepare('UPDATE menu_items SET stock = stock - ? WHERE item_id = ? AND stock >= ?');
                $decreaseStock->execute([$qty, $itemId, $qty]);
                if ($decreaseStock->rowCount() !== 1) {
                    throw new RuntimeException('Stock changed while placing the order.');
                }

                $insertOrder = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'Pending')");
                $insertOrder->execute([$_SESSION['user_id'], $orderNumber, $total]);
                $orderId = $pdo->lastInsertId();

                $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                $insertItem->execute([$orderId, $item['item_id'], $qty, $item['price']]);
                $pdo->commit();
            } catch (Throwable $e) {
                $pdo->rollBack();
                $errorMsg = 'The order could not be placed. Please try again.';
            }

            if (!$errorMsg) {
                $successMsg = 'Your order for ' . htmlspecialchars($item['item_name']) . ' has been placed successfully.';
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM menu_categories ORDER BY category_id")->fetchAll(PDO::FETCH_ASSOC);
$itemsStmt = $pdo->prepare("SELECT * FROM menu_items WHERE category_id = ? ORDER BY item_id");

include 'includes/header.php';
?>

<section class="menu-section">
    <div class="menu-card">
        <h2>"The Don's Selection"</h2>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <div class="menu-columns">
            <?php foreach ($categories as $cat): ?>
                <div>
                    <h3><?= htmlspecialchars($cat['category_name']) ?></h3>
                    <ul>
                        <?php
                        $itemsStmt->execute([$cat['category_id']]);
                        foreach ($itemsStmt->fetchAll(PDO::FETCH_ASSOC) as $item):
                        ?>
                            <li class="menu-item-row">
                                <div class="menu-item-info">
                                    <span><?= htmlspecialchars($item['item_name']) ?></span>
                                    <span class="menu-price">&#8369;<?= number_format($item['price'], 2) ?></span>
                                    <small><?= (int) $item['stock'] ?> available</small>
                                </div>
                                <form method="post" class="order-form">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['item_id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?= max(1, (int) $item['stock']) ?>" <?= (int) $item['stock'] < 1 ? 'disabled' : '' ?>>
                                    <button type="submit" class="btn small-btn" <?= (int) $item['stock'] < 1 ? 'disabled' : '' ?>>Order</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>