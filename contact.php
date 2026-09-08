<?php
require 'config.php';
$activePage = 'contact';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $errorMsg = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        $successMsg = 'Thanks for reaching out, ' . $name . '! We will get back to you soon.';
    }
}

include 'includes/header.php';
?>

<section class="club-section">
    <h2>Get in Touch!!</h2>

    <div class="form-card">
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="post" action="contact.php">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="message">Message</label>
            <textarea id="message" name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

            <button type="submit" class="btn btn-block">SEND MESSAGE</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
