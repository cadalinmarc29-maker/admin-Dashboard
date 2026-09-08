<?php
require 'config.php';
$activePage = '';

$errorMsg = '';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '') {
        $errorMsg = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $errorMsg = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $errorMsg = 'Passwords do not match.';
    } else {
        $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errorMsg = 'An account with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$fullName, $email, $hash]);

            $_SESSION['user_id']   = $pdo->lastInsertId();
            $_SESSION['full_name'] = $fullName;
            header('Location: index.php');
            exit;
        }
    }
}

include 'includes/header.php';
?>

<section class="club-section">
    <h2>Create an Account</h2>
    <div class="form-card">
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required
                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn btn-block">CREATE ACCOUNT</button>
        </form>
        <p style="margin-top:14px; font-size:0.85rem;">
            Already have an account? <a href="login.php" style="color:var(--mocha); text-decoration:underline;">Log in</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>