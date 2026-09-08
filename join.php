<?php
require 'config.php';
require 'includes/auth.php';
$activePage = 'join';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $plan     = $_POST['plan'] ?? '';

    if ($fullName === '' || $email === '' || !in_array($plan, ['Standard', 'Premium'], true)) {
        $errorMsg = 'Please fill in all fields and choose a plan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } else {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $pdo->prepare(
            "INSERT INTO memberships (user_id, full_name, email, plan) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $fullName, $email, $plan]);
        $successMsg = "Welcome to the Coffee Club, $fullName! You're signed up for the $plan plan.";
    }
}

include 'includes/header.php';
?>

<section class="club-section join-section">
    <h2>Join the Coffee Club</h2>
    <p class="sub">Exclusive Access & Savings at DON Vincent</p>

    <div class="club-grid">
        <div class="plan-card">
            <h3>Standard</h3>
            <div class="price">&#8369;300/mo</div>
            <ul>
                <li>10% Off All Drinks</li>
                <li>Free Monthly Pastry</li>
            </ul>
        </div>
        <div class="plan-card">
            <h3>Premium</h3>
            <div class="price">&#8369;500/mo</div>
            <ul>
                <li>10% Off All Drinks</li>
                <li>Free Monthly Pastry</li>
                <li>Exclusive Tasting Invites</li>
            </ul>
        </div>
        <div class="plan-card">
            <h3>Subscription Benefits</h3>
            <ul class="benefits-list">
                <li>&#9825; Free Shipping on Coffee Bags</li>
                <li>&#9825; Early Access to Blends</li>
                <li>&#9825; Member-Only Events</li>
            </ul>
        </div>
    </div>

    <div class="form-card">
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="post" action="join.php">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required
                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="plan">Membership Plan</label>
            <select id="plan" name="plan" required>
                <option value="">-- Choose a plan --</option>
                <option value="Standard" <?= (($_POST['plan'] ?? '') === 'Standard') ? 'selected' : '' ?>>Standard - ₱300/mo</option>
                <option value="Premium" <?= (($_POST['plan'] ?? '') === 'Premium') ? 'selected' : '' ?>>Premium - ₱500/mo</option>
            </select>

            <button type="submit" class="btn btn-block">JOIN NOW</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>