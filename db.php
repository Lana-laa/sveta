<?php
// db.php – общий файл для подключения и создания БД
$host = 'localhost';
$user = 'root';      // как вы просили
$password = '';      // укажите свой пароль, если есть
$dbname = 'vinum_db';

// Создаём соединение без выбора БД
$conn = new mysqli($host, $user, $password);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Создаём БД, если её нет
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$conn->query($sql_create_db)) {
    die("Ошибка создания БД: " . $conn->error);
}

// Выбираем БД
$conn->select_db($dbname);

// Создаём таблицу пользователей
$table_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($table_users)) {
    die("Ошибка создания таблицы users: " . $conn->error);
}

// Создаём таблицу бронирований (связь с пользователем)
$table_bookings = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quest_name VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    booking_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (!$conn->query($table_bookings)) {
    die("Ошибка создания таблицы bookings: " . $conn->error);
}

// Добавляем тестовые брони для демонстрации (только если таблица пуста)
$check = $conn->query("SELECT COUNT(*) as cnt FROM bookings");
$row = $check->fetch_assoc();
if ($row['cnt'] == 0) {
    // Для примера добавим брони для пользователя с id=1 (если он существует)
    // Но лучше это делать при регистрации первого пользователя или отдельно.
    // Оставим пустым – заполнится через логику приложения.
}
?>