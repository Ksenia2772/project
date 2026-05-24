<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$user = authenticateUser($pdo, $data['login'] ?? '', $data['password'] ?? '');

if ($user) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_login'] = $user['login'];
    
    $userData = getApplication($pdo, $user['id']);
    echo json_encode(['success' => true, 'user' => $userData]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Неверный логин или пароль']);
}