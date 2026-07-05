<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db_config.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false]);
    exit;
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$exists = $stmt->fetch();

echo json_encode(['available' => !$exists]);
