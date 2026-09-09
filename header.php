<?php
// Expects $activePage to be set by the including page (home, menu, join, about, contact)
if (!isset($activePage)) { $activePage = ''; }
$isAdmin = false;
if (!empty($_SESSION['user_id']) && isset($pdo)) {
    $roleStmt = $pdo->prepare('SELECT role FROM users WHERE user_id = ?');
    $roleStmt->execute([$_SESSION['user_id']]);
    $isAdmin = $roleStmt->fetchColumn() === 'admin';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DON Vincent Coffee Shop</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="topbar">
    <span>JOIN THE COFFEE CLUB!</span>
    <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="logout.php" class="topbar-login">LOG OUT</a>
    <?php endif; ?>
</div>

<header class="site-header">
    <div class="nav-wrap">
        <a href="index.php" class="logo">
        <span class="logo-badge">
            <img src="images/logo_for_webdev.png" alt="Logo">
        </span>
        <span class="brand-text">
            <div class="name">DON Vincent</div>
            <div class="tagline">Coffee Shop</div>
        </span>
    </a>

        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">HOME</a></li>
                <li><a href="menu.php" class="<?= $activePage === 'menu' ? 'active' : '' ?>">MENU</a></li>
                <li><a href="join.php" class="<?= $activePage === 'join' ? 'active' : '' ?>">JOIN</a></li>
                <li><a href="about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">ABOUT US</a></li>
                <li><a href="contact.php" class="<?= $activePage === 'contact' ? 'active' : '' ?>">CONTACT</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="nav-icon">
                    <a href="logout.php" title="Logout, <?= htmlspecialchars($_SESSION['full_name']) ?>">&#128100;</a>
                </div>
            <?php endif; ?>

            <div class="header-mini-actions">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="mini-action">LOG OUT</a>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                    <a href="admin.php" class="mini-action">ADMIN</a>
                <?php endif; ?>
                <a href="cart.php" class="mini-action">CART (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
                <a href="orders.php" class="mini-action">VIEW ORDERS</a>
            </div>
        </div>
    </div>
</header>
