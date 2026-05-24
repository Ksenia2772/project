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
    echo json_encode(['success' => false, 'errors' => ['general' => 'Нет данных']]);
    exit;
}

$errors = [];

// Валидация имени (только буквы, пробелы, дефисы)
$name = trim($data['name'] ?? '');
if (empty($name)) {
    $errors['name'] = 'Имя обязательно для заполнения';
} elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $name)) {
    $errors['name'] = 'Имя может содержать только буквы, пробелы и дефисы';
}

// Валидация email
$email = trim($data['email'] ?? '');
if (empty($email)) {
    $errors['email'] = 'Email обязателен для заполнения';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Введите корректный email (например: name@domain.com)';
}

// Валидация телефона (+7XXXXXXXXXX или 8XXXXXXXXXX)
$phone = trim($data['phone'] ?? '');
if (empty($phone)) {
    $errors['phone'] = 'Телефон обязателен для заполнения';
} elseif (!preg_match('/^(\\+7|8)[0-9]{10}$/', $phone)) {
    $errors['phone'] = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX (10 цифр после кода)';
}

// Валидация тура
$tour = trim($data['tour'] ?? '');
if (empty($tour)) {
    $errors['tour'] = 'Выберите интересующий тур';
}

// Сообщение не обязательное, просто очищаем
$message = trim($data['message'] ?? '');

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Сохраняем в БД
$result = saveApplication($pdo, ['name' => $name, 'email' => $email, 'phone' => $phone, 'tour' => $tour, 'message' => $message]);

// Устанавливаем cookies на год для полей формы
setcookie('saved_name', $name, time() + 365 * 24 * 3600, '/');
setcookie('saved_email', $email, time() + 365 * 24 * 3600, '/');
setcookie('saved_phone', $phone, time() + 365 * 24 * 3600, '/');
setcookie('saved_tour', $tour, time() + 365 * 24 * 3600, '/');
setcookie('saved_message', $message, time() + 365 * 24 * 3600, '/');

echo json_encode(['success' => true, 'data' => $result]);
?>