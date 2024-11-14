<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: forum.php');
        exit;   
    } else {
        $errors[] = "Kullanıcı adı veya şifre yanlış!";
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Forum - Giriş Yap</title>
    <title>Cyber Forum - Kayıt Ol</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: 'Courier New', monospace;
            overflow: hidden;
        }
        .numbers {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            opacity: 0.1;
        }
        .numbers span {
            position: absolute;
            font-size: 20px;
            color: green;
            animation: fall linear infinite;
        }
        @keyframes fall {
            0% {
                top: -100px;
            }
            100% {
                top: 100vh;
            }
        }
        .form-container {
            text-align: center;
            margin-top: 10%;
        }
        input {
            margin: 10px;
            padding: 10px;
            background-color: #333;
            border: none;
            color: white;
            font-size: 18px;
        }
        input[type="submit"] {
            background-color: green;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="numbers">
        <!-- Aynı kayan sayılar kodu -->
    </div>

    <div class="form-container">
        <h1>Giriş Yap</h1>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
            <input type="password" name="password" placeholder="Şifre" required><br>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="submit" value="Giriş Yap">
        </form>
    </div>
</body>
</html>
