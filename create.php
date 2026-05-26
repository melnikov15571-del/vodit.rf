<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die('Необходимо авторизоваться');
}

// Обработка формы ДО включения header.php — критически важно!
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php'); // Подключаем БД только для обработки формы

    $date_formatted = $_POST['date'];
    $con->query("INSERT INTO request (review, date, curses, payment, user_id) VALUES ('', '$date_formatted', '{$_POST['curses']}', '{$_POST['payment']}', '{$_SESSION['user_id']}')") or die('Ошибка: ' . $con->error);

    header('Location: history.php');
    exit;
}

// Теперь подключаем header.php — после всех заголовков
include 'header.php';
?>
<style>
    .btn-sub { transition: background 0.2s, transform 0.1s; }
    .btn-sub:active { transform: scale(0.97); }
    input, select { transition: border-color 0.2s, box-shadow 0.2s; }
    input:focus, select:focus { border-color: #4CCFE0; box-shadow: 0 0 0 3px rgba(76,207,224,0.2); }
    .btn-back { background: #6C757D; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background 0.2s; }
    .btn-back:hover { background: #5a6268; }
    .action-buttons { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px; }
</style>
<div class="container">
    <div class="booking-card">
        <div class="action-buttons">
            <a href="javascript:history.back()" class="btn-back">← Назад</a>
        </div>
        <div class="booking-header"><h1>Создание заявки</h1></div>
        <form method="POST" class="form-group">
            <label for='curses'>Вид транспорта для обучения</label>
            <select required name="curses">
                <option value="Катер">Катер</option>
                <option value="Круизный лайнер">Круизный лайнер</option>
                <option value="Яхта">Яхта</option>
            </select>

            <label>Дата начала обучения</label>
            <input type="datetime-local" name="date" required>

            <label>Способ оплаты</label>
            <select required name="payment">
                <option value="Предоплата qr">Предоплата по QR-коду</option>
                <option value="Оплата картой">Оплата картой МИР</option>
                <option value="Постоплата">Постоплата в офисе организации</option>
            </select>
            <button class="btn-sub">Отправить</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
