<?php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$adminCheck = $pdo->prepare('SELECT role FROM users WHERE user_id = ?');
$adminCheck->execute([$_SESSION['user_id']]);
$currentUser = $adminCheck->fetch(PDO::FETCH_ASSOC);

if (!$currentUser || $currentUser['role'] !== 'admin') {
    http_response_code(403);
    exit('Admin access required.');
}