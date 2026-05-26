<?php
ob_start();
include('db.php');
session_start();
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die('Доступ запрещен. Только для администратора.');
include 'header.php';
// Параметры фильтрации и сортировки
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

// Обновление статуса
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $id = (int)$_POST['request_id'];
    $con->query("UPDATE request SET status='$status' WHERE id=$id");
    header('Location: admin.php?' . $_SERVER['QUERY_STRING']);
    exit;
}

// Построение WHERE для фильтра
$where = "";
if($status_filter) {
    $where = "WHERE request.status='$status_filter'";
}

// Подсчет общего количества
$count_query = $con->query("SELECT COUNT(*) as cnt FROM request INNER JOIN users ON request.user_id = users.id $where");
$total = $count_query->fetch_assoc()['cnt'];
$total_pages = ceil($total / $per_page);

// Основной запрос с сортировкой и пагинацией
$query = $con->query("SELECT request.*, users.login, users.fullname FROM request INNER JOIN users ON request.user_id = users.id $where ORDER BY $sort_by $sort_order LIMIT $offset, $per_page");
if(!$query) die('query error: ' . $con->error);

?>
<style>
    .filter-bar { background: #f0f8ff; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
    .filter-bar select, .filter-bar a { padding: 8px 15px; border-radius: 8px; border: 1px solid #007BFF; background: white; }
    .filter-bar a { text-decoration: none; background: #007BFF; color: white; }
    .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
    .pagination a, .pagination span { padding: 8px 15px; border-radius: 8px; text-decoration: none; background: #007BFF; color: white; }
    .pagination .current { background: #4CCFE0; }
    .request-card { transition: transform 0.2s, box-shadow 0.2s; }
    .request-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,123,255,0.15); }
    .btn-sub { transition: background 0.2s, transform 0.1s; }
    .btn-sub:active { transform: scale(0.98); }
    .btn-back { background: #6C757D; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background 0.2s; }
    .btn-back:hover { background: #5a6268; }
    .action-buttons { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px; }
    @media (max-width: 390px) { .filter-bar { flex-direction: column; align-items: stretch; } }
</style>
<div class="container">
    <div class="admin-card">
        <div class="action-buttons">
            <a href="javascript:history.back()" class="btn-back">← Назад</a>
        </div>
        <div class="admin-header">
            <h1>Панель администратора</h1>
            <p>Управление заявками пользователей</p>
        </div>
        
        <div class="filter-bar">
            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
                <select name="status">
                    <option value="">Все статусы</option>
                    <option value="Новая" <?= $status_filter=='Новая'?'selected':'' ?>>Новая</option>
                    <option value="Идет обучение" <?= $status_filter=='Идет обучение'?'selected':'' ?>>Идет обучение</option>
                    <option value="Обучение завершено" <?= $status_filter=='Обучение завершено'?'selected':'' ?>>Обучение завершено</option>
                </select>
                <select name="sort">
                    <option value="id" <?= $sort_by=='id'?'selected':'' ?>>Сортировка: по ID</option>
                    <option value="date" <?= $sort_by=='date'?'selected':'' ?>>Сортировка: по дате</option>
                    <option value="status" <?= $sort_by=='status'?'selected':'' ?>>Сортировка: по статусу</option>
                </select>
                <select name="order">
                    <option value="asc" <?= $sort_order=='ASC'?'selected':'' ?>>Порядок: по возрастанию</option>
                    <option value="desc" <?= $sort_order=='DESC'?'selected':'' ?>>Порядок: по убыванию</option>
                </select>
                <button type="submit" class="btn-sub" style="width:auto; margin-top:0;">Применить</button>
            </form>
            <a href="admin.php">Сбросить фильтры</a>
        </div>

        <?php
        $number = $offset + 1;
        while($request = $query->fetch_assoc()) {
            $status_class = '';
            if($request['status'] == 'Новая') $status_class = 'style="border-left-color: #dc3545;"';
            elseif($request['status'] == 'Идет обучение') $status_class = 'style="border-left-color: #ffc107;"';
            else $status_class = 'style="border-left-color: #28a745;"';
            
            $formatted_date = date('d.m.Y H:i', strtotime($request['date']));
            
            echo "
            <div class='request-card' $status_class>
                <h2>№{$number} | Заявка от {$request['login']} (ID: {$request['id']})</h2>
                <b>ФИО: </b>{$request['fullname']}<br>
                <b>Дата: </b>{$formatted_date}<br>
                <b>Вид услуги: </b>{$request['curses']}<br>
                <b>Тип оплаты: </b>{$request['payment']}<br><br>
                <b>Комментарий пользователя: </b>" . nl2br(htmlspecialchars($request['review'])) . "<br>
                <form action='' method='POST' style='margin-top:15px;'>
                    <input type='hidden' name='request_id' value='{$request['id']}'>
                    <select name='status'>
                        <option " . ($request['status'] == 'Новая' ? 'selected' : '') . " value='Новая'>Новая</option>
                        <option " . ($request['status'] == 'Идет обучение' ? 'selected' : '') . " value='Идет обучение'>Идет обучение</option>
                        <option " . ($request['status'] == 'Обучение завершено' ? 'selected' : '') . " value='Обучение завершено'>Обучение завершено</option>
                    </select>
                    <button type='submit' class='btn-sub'>Сохранить</button>
                </form>
            </div>";
            $number++;
        }
        if($number == $offset + 1) echo "<div class='request-card'><p>Нет заявок для отображения</p></div>";
        ?>

        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>">◄ Предыдущая</a>
            <?php endif; ?>
            <?php for($p=1; $p<=$total_pages; $p++): ?>
                <?php if($p == $page): ?>
                    <span class="current"><?= $p ?></span>
                <?php else: ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$p])) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if($page < $total_pages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>">Следующая ►</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
ob_end_flush();
include 'footer.php';
?>
