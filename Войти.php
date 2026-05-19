<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ? AND email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                header("Location: Личный кабинет.php");
                exit;
            } else {
                $error = "Неверный пароль.";
            }
        } else {
            $error = "Пользователь с таким именем и email не найден.";
        }
        $stmt->close();
    } else {
        $error = "Заполните все поля.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINUM - Вход</title>
    <link rel="stylesheet" href="Войти.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-content">
        <h1 class="logo-main">VINUM</h1>
    </div>
</header>

<main class="login-wrapper">
    <div class="login-container">
        <h2 class="login-title">Войти</h2>
        <?php if ($error): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form class="login-form" method="POST">
            <input type="text" name="username" placeholder="Имя пользователя" class="login-input" required>
            <input type="email" name="email" placeholder="Email" class="login-input" required>
            <input type="password" name="password" placeholder="Пароль" class="login-input" required>
            
            <div class="button-group">
                <button type="submit" class="btn-action">Войти</button>
                <a href="Регистрация.php" class="btn-action">Регистрация</a>
            </div>
        </form>
    </div>
</main>

<footer class="footer">
    <!-- футер такой же как в оригинале -->
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