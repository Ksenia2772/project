<?php
// success.php — страница с результатом отправки (если JS отключён)
session_start();

if (!isset($_SESSION['temp_login']) || !isset($_SESSION['temp_password'])) {
    header('Location: index.html');
    exit;
}

$login = $_SESSION['temp_login'];
$password = $_SESSION['temp_password'];
$name = $_SESSION['temp_name'] ?? '';

// Очищаем временные данные
unset($_SESSION['temp_login']);
unset($_SESSION['temp_password']);
unset($_SESSION['temp_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка отправлена - WorldTravel</title>
    <link rel="stylesheet" href="style-2.css">
    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .success-card h1 { color: #28a745; margin-bottom: 20px; }
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }
        .credentials code {
            display: inline-block;
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 18px;
            margin: 5px 0;
        }
        .btn-back {
            display: inline-block;
            background: #4a6fa5;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn-login {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <h1>✅ Заявка отправлена!</h1>
            <p>Спасибо, <?= htmlspecialchars($name) ?>! Ваша заявка успешно получена.</p>
            
            <div class="credentials">
                <strong>🔐 Ваши данные для входа:</strong><br>
                Логин: <code><?= htmlspecialchars($login) ?></code><br>
                Пароль: <code><?= htmlspecialchars($password) ?></code><br>
                <small style="color: #666;">Сохраните эти данные. Они понадобятся для редактирования заявки.</small>
            </div>
            
            <a href="login.php" class="btn-login">🔐 Войти и редактировать</a>
            <a href="index.html" class="btn-back">← На главную</a>
        </div>
    </div>
</body>
</html>