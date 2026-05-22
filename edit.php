<?php
// edit.php - страница редактирования заявки (fallback, если JS отключён)
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = getApplication($pdo, $user_id);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'tour' => $_POST['tour'] ?? '',
        'message' => trim($_POST['message'] ?? '')
    ];
    
    // Валидация
    $errors = [];
    if (empty($data['name'])) $errors[] = 'Имя обязательно';
    if (empty($data['email'])) $errors[] = 'Email обязателен';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
    if (empty($data['phone'])) $errors[] = 'Телефон обязателен';
    
    if (empty($errors)) {
        if (updateApplication($pdo, $user_id, $data)) {
            $success = '✅ Данные успешно обновлены!';
            // Обновляем данные в переменной
            $user = getApplication($pdo, $user_id);
        } else {
            $error = '❌ Ошибка при сохранении';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование заявки - WorldTravel</title>
    <link rel="stylesheet" href="style-2.css">
    <style>
        .edit-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
        }
        .edit-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .edit-container h1 {
            color: #4a6fa5;
            margin-bottom: 10px;
        }
        .user-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4a6fa5;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .btn-save {
            width: 100%;
            padding: 14px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .btn-save:hover {
            background: #218838;
        }
        .btn-logout {
            width: 100%;
            padding: 12px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-logout:hover {
            background: #c82333;
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #4a6fa5;
            text-decoration: none;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .tour-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            background: white;
        }
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #e1e1e1;
        }
    </style>
</head>
<body>
    <div class="edit-page">
        <div class="edit-container">
            <h1>✏️ Редактирование заявки</h1>
            <div class="user-info">
                <strong>👤 Вы вошли как:</strong> <?= h($user['login']) ?> 
                (заявка от <?= date('d.m.Y', strtotime($user['created_at'])) ?>)
            </div>
            
            <?php if ($success): ?>
                <div class="success-message"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="name" value="<?= h($user['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= h($user['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон *</label>
                    <input type="tel" name="phone" value="<?= h($user['phone']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Интересующий тур</label>
                    <select name="tour" class="tour-select">
                        <option value="">Выберите тур</option>
                        <option value="paris" <?= $user['tour'] === 'paris' ? 'selected' : '' ?>>Романтический Париж</option>
                        <option value="japan" <?= $user['tour'] === 'japan' ? 'selected' : '' ?>>Загадочная Япония</option>
                        <option value="iceland" <?= $user['tour'] === 'iceland' ? 'selected' : '' ?>>Исландия: земля льдов и огня</option>
                        <option value="thailand" <?= $user['tour'] === 'thailand' ? 'selected' : '' ?>>Тропический Таиланд</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Сообщение</label>
                    <textarea name="message"><?= h($user['message']) ?></textarea>
                </div>
                
                <button type="submit" class="btn-save">💾 Сохранить изменения</button>
            </form>
            
            <hr>
            
            <form method="GET">
                <button type="submit" name="logout" value="1" class="btn-logout">🚪 Выйти из системы</button>
            </form>
            
            <a href="index.html" class="btn-back">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>