<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

session_start();

$user_id = $_GET['user_id'] ?? 0;

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$result = updateApplication($pdo, $user_id, $data);
echo json_encode(['success' => $result]);