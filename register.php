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

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Нет данных']);
    exit;
}

$errors = [];
if (empty($data['name'])) $errors[] = 'Имя обязательно';
if (empty($data['email'])) $errors[] = 'Email обязателен';
if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
if (empty($data['phone'])) $errors[] = 'Телефон обязателен';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$result = saveApplication($pdo, $data);
echo json_encode(['success' => true, 'data' => $result]);