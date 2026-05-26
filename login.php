<?php
session_start();
$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php');
    $login = mysqli_real_escape_string($con, $_POST['login']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $query = $con->query("SELECT * FROM users WHERE login='$login' AND password='$password'");
    if($query && $query->num_rows > 0) {
        $user = $query->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['admin'] = ($user['login'] == 'Admin26');
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Водить.РФ</title>
    <link rel='stylesheet' href='styles/style.css'>
    <style>
        .btn-back { background: #6C757D; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-block; margin-bottom: 20px; transition: background 0.2s; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body class="body-form">
    <div class="login-container">
        <a href="index.php" class="btn-back">← Назад</a>
        <h1>Вход в систему</h1>
        <p>Войдите в свой аккаунт</p>
        <?php if($error): ?>
            <div class="error-message" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center;"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Логин</label>
            <input type="text" name="login" required>
            <label>Пароль</label>
            <input type="password" name="password" required>
            <button type="submit" class="btn-sub">Войти</button>
        </form>
        <p>Нет аккаунта? <a href="register.php" class='register-link'>Зарегистрироваться</a></p>
    </div>
</body>
</html>