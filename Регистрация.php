<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = "Все поля обязательны.";
    } elseif ($password !== $confirm) {
        $error = "Пароли не совпадают.";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен быть не менее 6 символов.";
    } else {
        // Проверка уникальности имени и email
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = "Пользователь с таким именем или email уже существует.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hash);
            if ($stmt->execute()) {
                $success = "Регистрация успешна! Теперь вы можете войти.";
                // Можно автоматически войти, но лучше отправить на страницу входа
            } else {
                $error = "Ошибка БД: " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINUM - Регистрация</title>
    <link rel="stylesheet" href="Регистрация.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-overlay">
        <h1 class="logo-main">VINUM</h1>
    </div>
</header>

<main class="registration-container">
    <h2>Регистрация</h2>
    <?php if ($error): ?>
        <div style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="color: green; text-align: center;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form class="reg-form" method="POST">
        <input type="text" name="username" placeholder="Имя пользователя" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" id="pass" placeholder="Пароль" required>
        <input type="password" name="confirm_password" id="confirm_pass" placeholder="Подтверждение пароля" required>
        
        <div class="button-group">
            <button type="submit" class="btn btn-action">Создать</button>
            <a href="Войти.php" class="btn btn-action">Уже есть аккаунт</a>
        </div>
    </form>
</main>

<footer class="footer">
    <!-- тот же футер -->
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