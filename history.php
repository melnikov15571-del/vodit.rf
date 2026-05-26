<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die('Чтобы посмотреть историю заявок, надо войти в аккаунт.');
}

// Обработка POST-запроса (обновление отзыва)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php'); // Подключаем БД только при обработке формы

    $review = mysqli_real_escape_string($con, $_POST['review']);
    $request_id = (int)$_POST['request_id'];
    $con->query("UPDATE request SET review='$review' WHERE id='$request_id' AND user_id='{$_SESSION['user_id']}'");
    header('Location: history.php');
    exit;
}

include('db.php'); // Подключаем БД для отображения истории заявок
include 'header.php';

// Получаем историю заявок
$query = $con->query("SELECT * FROM request WHERE user_id='{$_SESSION['user_id']}' ORDER BY id DESC");
if (!$query) {
    die('query error: ' . $con->error);
}
?>
<style>
    .request-card { transition: transform 0.2s, box-shadow 0.2s; }
    .request-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,123,255,0.1); }
    .btn-sub { transition: background 0.2s, transform 0.1s; }
    .btn-sub:active { transform: scale(0.97); }
    .btn-exit { transition: background 0.2s, transform 0.1s; }
    .btn-exit:active { transform: scale(0.97); }
    .btn-back { background: #6C757D; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background 0.2s; }
    .btn-back:hover { background: #5a6268; }
    .action-buttons { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .request-card { animation: fadeInUp 0.3s ease-out; }
</style>
<div class="container">
    <div class="booking-card">
        <div class="action-buttons">
            <a href="javascript:history.back()" class="btn-back">← Назад</a>
        </div>
        <div class="booking-header"><h1>История заявок</h1></div>
        <div style="clear:both;"></div>
        <?php
        $i = 0;
        while ($request = $query->fetch_assoc()) {
            $i++;
            $status_badge = '';
            if ($request['status'] == 'Новая') {
                $status_badge = '<span style="background:#dc3545; color:white; padding:3px 10px; border-radius:20px; font-size:12px;">Новая</span>';
            } elseif ($request['status'] == 'Идет обучение') {
                $status_badge = '<span style="background:#ffc107; color:#333; padding:3px 10px; border-radius:20px; font-size:12px;">Идет обучение</span>';
            } else {
                $status_badge = '<span style="background:#28a745; color:white; padding:3px 10px; border-radius:20px; font-size:12px;">Обучение завершено</span>';
            }

            $formatted_date = date('d.m.Y H:i', strtotime($request['date']));

            echo "<div class='request-card'>
                    <h2 style='text-align:center'>Заявка №{$i} (ID: {$request['id']}) {$status_badge}</h2>
                    <b>Дата начала: </b>{$formatted_date}<br>
                    <b>Вид услуги: </b>{$request['curses']}<br>
                    <b>Тип оплаты: </b>{$request['payment']}<br>";

            if (!empty($request['review'])) {
                echo "<b>Ваш отзыв: </b>{$request['review']}<br>";
            }

            if ($request['status'] === 'Обучение завершено') {
                echo "<form method='POST' style='margin-top:15px;'>
                        <input type='hidden' name='request_id' value='{$request['id']}'>
                        <input name='review' placeholder='Отзыв об услуге' value='{$request['review']}' style='width:100%; margin-bottom:10px;'>
                        <button class='btn-sub'>Оставить отзыв</button>
                      </form>";
            }
            echo "</div>";
        }
        if ($i === 0) {
            echo "<div class='request-card'><p>У вас пока нет заявок</p></div>";
        }
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>
