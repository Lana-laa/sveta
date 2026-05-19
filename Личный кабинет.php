<?php
session_start();
require_once 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: Войти.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Получаем бронирования пользователя
$bookings = [];
$stmt = $conn->prepare("SELECT quest_name, status, booking_date FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

// Обработка добавления нового бронирования (пример из вашего JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_booking'])) {
    $quest_name = $_POST['quest_name'];
    $status = 'Забронирована';
    $booking_date = date('Y-m-d'); // или дата из формы
    $insert = $conn->prepare("INSERT INTO bookings (user_id, quest_name, status, booking_date) VALUES (?, ?, ?, ?)");
    $insert->bind_param("isss", $user_id, $quest_name, $status, $booking_date);
    $insert->execute();
    $insert->close();
    header("Location: Личный кабинет.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINUM - Личный кабинет</title>
    <link rel="stylesheet" href="Личный кабинет.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-content">
        <h1 class="logo-main">VINUM</h1>
    </div>
</header>

<nav class="user-nav">
    <div class="container">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($username) ?></span>
        </div>
        <a href="Каталог квестов.html" class="btn-all-quests">Все квесты</a>
        <a href="logout.php" class="btn-all-quests" style="background:#555;">Выйти</a>
    </div>
</nav>

<main class="content-wrapper">
    <div class="container">
        <h2 class="section-title">Мои бронирования</h2>
        
        <div class="bookings-grid">
            <?php if (empty($bookings)): ?>
                <div class="booking-card">У вас пока нет бронирований.</div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <h3><?= htmlspecialchars($booking['quest_name']) ?></h3>
                        <p class="status"><?= htmlspecialchars($booking['status']) ?></p>
                        <p class="date">Бронь: <?= date('d.m.Y', strtotime($booking['booking_date'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Простая форма для добавления новой брони (для демонстрации) -->
        <div style="margin-top: 30px;">
            <h3>Добавить новое бронирование</h3>
            <form method="POST">
                <input type="text" name="quest_name" placeholder="Название квеста" required>
                <button type="submit" name="add_booking">Забронировать</button>
            </form>
        </div>
    </div>
</main>

<footer class="footer">
    <!-- футер как ранее -->
    <div class="footer-accent-bar">
        <div class="container">
            <div class="barcode-box">
                <div class="barcode-lines">|||| || ||| || ||||</div>
                <div class="barcode-text">V I N U M</div>
            </div>
        </div>
    </div>
    <div class="footer-main">
        <div class="container">
            <div class="footer-flex">
                <div class="contacts">
                    <h3>Контакты</h3>
                    <p><i class="fab fa-telegram-plane"></i> @VINUM</p>
                    <p><i class="fas fa-phone-alt"></i> +375 33 349 931</p>
                </div>
                <div class="social-links">
                    <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                    <a href="https://vk.com/"><i class="fab fa-vk"></i></a>
                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-square"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>