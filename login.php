<?php
require 'config.php';
$activePage = '';

$errorMsg = '';

if (!empty($_SESSION['user_id'])) {
    $sessionRole = $_SESSION['role'] ?? 'customer';
    header('Location: ' . ($sessionRole === 'admin' ? 'admin.php' : 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'] ?? 'customer';
        header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'index.php'));
        exit;
    } else {
        $errorMsg = 'Incorrect email or password.';
    }
}

include 'includes/header.php';
?>

<section class="club-section">
    <h2>Log In</h2>
    <div class="form-card">
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-block">LOG IN</button>
        </form>
        <p style="margin-top:14px; font-size:0.85rem;">
            Don't have an account? <a href="register.php" style="color:var(--mocha); text-decoration:underline;">Sign up</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>