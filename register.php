<?php
session_start();
$error = '';
$success = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php');
    $login = mysqli_real_escape_string($con, $_POST['login']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    
    if(!preg_match('/^[a-zA-Z0-9]{6,}$/', $login)) {
        $error = 'Логин должен содержать только латиницу и цифры, минимум 6 символов';
    } elseif(strlen($password) < 8) {
        $error = 'Пароль должен быть не менее 8 символов';
    } else {
        $check = $con->query("SELECT id FROM users WHERE login='$login'");
        if($check && $check->num_rows > 0) {
            $error = 'Пользователь с таким логином уже существует';
        } else {
            $con->query("INSERT INTO users (login, password, fullname, phone, email) VALUES ('$login', '$password', '$fullname', '$phone', '$email')");
            $success = 'Регистрация успешна! Перенаправление на страницу входа...';
            echo '<meta http-equiv="refresh" content="2;url=login.php">';
        }
    }
}
?>
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Водить.РФ</title>
    <link rel='stylesheet' href='styles/style.css'>
    <style>
        .btn-back { background: #6C757D; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-block; margin-bottom: 20px; transition: background 0.2s; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body class="body-form">
    <div class='register-container'>
        <a href="index.php" class="btn-back">← Назад</a>
        <h1>Регистрация</h1>
        <p>Создайте аккаунт для составления заявки</p>
        <?php if($error): ?>
            <div class="error-message" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:8px; margin-bottom:15px;"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success-message" style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:15px;"><?= $success ?></div>
        <?php endif; ?>
        <form method='POST'>
            <label>ФИО*</label>
            <input type='text' name='fullname' required>
            <label>Телефон*</label>
            <input type='tel' name='phone' placeholder='+7(___)___-__-__' required>
            <label>Email*</label>
            <input type='email' name='email' required>
            <label>Логин* (латиница и цифры, от 6 символов)</label>
            <input type='text' name='login' pattern='[a-zA-Z0-9]{6,}' required>
            <label>Пароль* (от 8 символов)</label>
            <input type='password' name='password' minlength='8' required>
            <button type='submit' class='btn-sub'>Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href='login.php' class='login-link'>Войти</a></p>
    </div>
</body>
</html>