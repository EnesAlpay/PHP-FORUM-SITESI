<?php
session_start(); 
require 'config.php';


$search = '';
if (isset($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search']), ENT_QUOTES, 'UTF-8');
}


if ($search) {
   
    $stmt_posts = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE title LIKE :search ORDER BY created_at DESC");
    $stmt_posts->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
} else {
   
    $stmt_posts = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
}
$stmt_posts->execute();
$posts = $stmt_posts->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoSecForms - Ana Sayfa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
          body {
            background-color: black;
            color: white;
            font-family: 'Courier New', monospace;
            overflow: hidden;
        }
        .matrix-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; 
            overflow: hidden;
        }
        .column {
            position: absolute;
            color: lime;
            font-family: monospace; 
            font-size: 20px; 
            white-space: nowrap; 
            opacity: 1; 
            transition: opacity 0.1s ease; 
        }
    </style>
</head>
<body>
<div class="matrix-container"></div>

    <div class="container">
        <header class="navbar">
            <div class="logo">InfoSecForms</div>
            <nav>
                <a href="forum.php">Ana Sayfa</a>
                <a href="profile.php">Profil</a>
                <a href="create_post.php">Gönderi Oluştur</a>
                <a href="login.php">Giriş Yap</a>
                <form action="forum.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Başlık Ara..." required>
                    <input type="submit" value="Ara" class="fas-fa search">
                    <i class="fas fa-search"></i>
                </form>
            </nav>
        </header>

        <main>
            <h1>Forum Ana Sayfası</h1>
            <div class="posts-container">
                <?php foreach ($posts as $post): ?>
                    <div class="post-container">
                        <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php if ($post['image_path']): ?>
                            <img src="<?= htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Post Resmi" width="200">
                        <?php endif; ?>
                        <p><small>Yazar: <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?></small></p>

                        <div class="comment-section">
                            <h3>Yorumlar</h3>
                            <?php
                         
                            $stmt_comments = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = :post_id ORDER BY created_at ASC");
                            $stmt_comments->execute(['post_id' => $post['post_id']]);
                            $comments = $stmt_comments->fetchAll();
                            ?>
                            <?php if (empty($comments)): ?>
                                <p>Henüz yorum yapılmamış.</p>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <p><?= nl2br(htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8')) ?> - <small><?= htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8') ?></small></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <form class="form" action="add_comment.php" method="POST">
                                <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                                <textarea name="comment" required placeholder="Yorumunuzu yazın..."></textarea><br>
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                <input type="submit" value="Yorum Yap">
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <footer>
            © 2024 Cyber Forum
        </footer>
    </div>
    <script src="script.js"></script>
</body>
</html>
