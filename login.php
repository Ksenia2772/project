<?php
// login.php - страница входа для редактирования (fallback, если JS отключён)
session_start();
require_once 'config.php';

// Если уже авторизован, перенаправляем на edit.php
if (isset($_SESSION['user_id'])) {
    header('Location: edit.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $error = 'Заполните логин и пароль';
    } else {
        $user = authenticateUser($pdo, $login, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            header('Location: edit.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - WorldTravel</title>
    <link rel="stylesheet" href="style-2.css">
    <style>
        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .login-card h1 {
            color: #4a6fa5;
            text-align: center;
            margin-bottom: 30px;
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
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4a6fa5;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #4a6fa5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-login:hover {
            background: #385d8a;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4a6fa5;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .info {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <h1>🔐 Вход в систему</h1>
            
            <div class="info">
                💡 Введите логин и пароль, которые вы получили после отправки заявки.
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?= h($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Логин:</label>
                    <input type="text" name="login" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-login">Войти</button>
            </form>
            
            <a href="index.html" class="back-link">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>