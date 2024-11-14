<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';
$errors = [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Geçersiz CSRF token!";
    } else {
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $userExists = $stmt->fetchColumn();

        if ($userExists > 0) {
            $errors[] = "Bu kullanıcı adı veya e-posta adresi zaten mevcut.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]);

            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
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
        footer {
            text-align: center;
            margin-top: 20px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="numbers">
        <?php
        for ($i = 0; $i < 100; $i++): ?>
            <span style="left: <?= rand(0, 100) ?>%; animation-duration: <?= rand(3, 10) ?>s;"><?= rand(0, 9) ?></span>
        <?php endfor; ?>
    </div>

    <div class="form-container">
        <h1>Kayıt Ol</h1>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
            <input type="email" name="email" placeholder="E-posta" required><br>
            <input type="password" name="password" placeholder="Şifre" required><br>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="submit" value="Kayıt Ol">
        </form>
    </div>

    <footer>
        © 2024 Cyber Forum
    </footer>
</body>
</html>
