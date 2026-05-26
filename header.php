<?php
// header.php - общая шапка для всех страниц
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/slider.css">
</head>
<body>
<div class="header">
    <div class="nav">

        <!-- ========================================== -->
        <!-- ЛОГОТИП + НАЗВАНИЕ КОМПАНИИ -->
        <!-- ========================================== -->
        <div class="logo-wrapper">
            <a href="index.php" class="logo-link">
                <img src="logo.png" alt="Логотип" class="logo-img">
            </a>
            <a href="index.php" class="logo-text">Водить.РФ</a>
        </div>
        <!-- ========================================== -->
        <!-- КОНЕЦ ЛОГОТИПА -->
        <!-- ========================================== -->

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="nav-buttons">
                <a href="login.php" class="btn-login">Войти</a>
                <a href="register.php" class="btn-register">Регистрация</a>
            </div>
        <?php elseif (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
            <div class="nav-buttons">
                <a href="admin.php" class="btn-admin">Панель администратора</a>
                <a href="logout.php" class="btn-exit">Выйти</a>
            </div>
        <?php else: ?>
            <div class="nav-buttons">
                <a href="history.php" class="btn-lk">Мои заявки</a>
                <a href="create.php" class="btn-create">Новая заявка</a>
                <a href="logout.php" class="btn-exit">Выйти</a>
            </div>
        <?php endif; ?>
    </div>
</div>
