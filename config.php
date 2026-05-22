<?php
// config.php - Конфигурация БД и общие функции

$db_host = 'localhost';
$db_user = 'u82194';
$db_pass = '8381502';
$db_name = 'u82194';

// Режим отладки
define('DEBUG_MODE', false);

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        die("Ошибка БД: " . $e->getMessage());
    }
    die("Технические неполадки. Попробуйте позже.");
}

// Безопасный вывод
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Генерация логина из имени
function generateLogin($name) {
    $clean = preg_replace('/[^a-zA-Zа-яА-Я]/u', '', $name);
    $clean = substr($clean, 0, 6);
    if (strlen($clean) < 3) $clean = 'traveler';
    return strtolower($clean) . rand(100, 999);
}

// Генерация случайного пароля
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

// Сохранение новой заявки
function saveApplication($pdo, $data) {
    $login = generateLogin($data['name']);
    $password = generatePassword();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO travel_applications (login, password_hash, name, email, phone, tour, message)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $login,
        $password_hash,
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['tour'],
        $data['message']
    ]);
    
    return [
        'id' => $pdo->lastInsertId(),
        'login' => $login,
        'password' => $password
    ];
}

// Обновление заявки (только для авторизованных)
function updateApplication($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE travel_applications 
        SET name = ?, email = ?, phone = ?, tour = ?, message = ?
        WHERE id = ?
    ");
    return $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['tour'],
        $data['message'],
        $id
    ]);
}

// Получение заявки по ID
function getApplication($pdo, $id) {
    $stmt = $pdo->prepare("SELECT id, login, name, email, phone, tour, message, created_at FROM travel_applications WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Авторизация пользователя
function authenticateUser($pdo, $login, $password) {
    $stmt = $pdo->prepare("SELECT id, login, password_hash FROM travel_applications WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}