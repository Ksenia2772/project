<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && strpos($request_uri, 'register') !== false) {
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
    
} elseif ($method === 'POST' && strpos($request_uri, 'login') !== false) {
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
    
} elseif ($method === 'PUT' && preg_match('/user\/(\d+)/', $request_uri, $matches)) {
    session_start();
    $user_id = (int)$matches[1];
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $user_id) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Не авторизован']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $result = updateApplication($pdo, $user_id, $data);
    echo json_encode(['success' => $result]);
    
} elseif ($method === 'GET' && preg_match('/user\/(\d+)/', $request_uri, $matches)) {
    session_start();
    $user_id = (int)$matches[1];
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $user_id) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Не авторизован']);
        exit;
    }
    
    $user = getApplication($pdo, $user_id);
    echo json_encode(['success' => true, 'user' => $user]);
    
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Endpoint not found', 'path' => $request_uri]);
}