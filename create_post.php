<?php
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF koruması
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Geçersiz CSRF token!";
    }

    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    // Resim yükleme işlemi
    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . "_" . $image_name;

        // Sadece güvenli resim formatlarını kabul et
        $allowed_file_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_file_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                $errors[] = "Resim yüklenirken bir hata oluştu!";
            }
        } else {
            $errors[] = "Sadece JPEG, PNG ve GIF formatları kabul ediliyor.";
        }
    }

    if (empty($errors)) {
        // SQL Injection koruması için prepared statement kullanımı
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image_path) VALUES (:user_id, :title, :content, :image_path)");
        $stmt->execute([
            'user_id'    => $user_id,
            'title'      => $title,
            'content'    => $content,
            'image_path' => $image_path
        ]);

        header('Location: forum.php');
        exit;
    }
}

// CSRF token oluştur
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gönderi Oluştur</title>

    <style>
        /* Matrix efekti için CSS */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1b1b1b;
            color: #ffffff;
            position: relative;
            overflow: hidden; /* Taşan içerikleri gizle */
        }

        .matrix-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none; /* Tıklama olaylarını devre dışı bırak */
    overflow: hidden;
}

.column {
    position: absolute;
    color: lime; /* Metin rengi */
    font-family: monospace; /* Yazı tipi */
    font-size: 20px; /* Yazı boyutu */
    white-space: nowrap; /* Satır sonu ekleme */
    opacity: 1; /* Başlangıçta görünür */
    transition: opacity 0.1s ease; /* Opaklık geçişi */
}

        .container {
            position: relative;
            z-index: 1; /* İçeriklerin üstte görünmesini sağla */
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #00ff99; /* Neon yeşil başlık rengi */
            margin-bottom: 20px;
        }

        .navbar {
            width: 100%;
            background-color: #2b2b2b;
            padding: 10px 0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar a {
            margin: 0 15px;
            color: #00ff99;
            font-weight: bold;
        }

        .navbar a:hover {
            color: #ffffff;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 80%;
            margin: 50px auto;
        }

        input[type="text"],
        textarea,
        input[type="file"],
        input[type="submit"] {
            margin: 10px 0;
            padding: 12px;
            border: 1px solid #00ff99; /* Neon yeşil kenarlık */
            border-radius: 5px;
            background: #2b2b2b; /* Form arka plan rengi */
            color: #ffffff; /* Yazı rengi */
            font-size: 16px;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #00ff99; /* Odaklanınca neon yeşil kenarlık */
            outline: none; /* Kenarlık dışına çıkma */
        }

        input[type="submit"] {
            background: #00ff99; /* Buton rengi */
            color: #000; /* Buton yazı rengi */
            cursor: pointer;
            transition: background 0.3s; /* Geçiş efekti */
        }

        input[type="submit"]:hover {
            background: #009966; /* Hover rengi */
        }

        .errors {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #2b2b2b;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="matrix-container"></div> <!-- Matrix efekti için kapsayıcı -->
    <div class="container">
        <h1>CyberForum</h1>
        <div class="navbar">
            <a href="forum.php">Ana Sayfa</a>
            <a href="profile.php">Profil</a>
            <a href="create_post.php">Gönderi Oluştur</a>
            <a href="login.php">Giriş Yap</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="create_post.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Başlık" required><br>
            <textarea name="content" placeholder="İçerik" required></textarea><br>
            <input type="file" name="image" accept="image/*"><br>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="submit" value="Gönder">
        </form><footer>
        © 2024 Cyber Forum
    </footer>
    </div>    
    
    
</body>
</html>