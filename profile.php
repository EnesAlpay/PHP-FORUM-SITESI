<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

// Kullanıcı oturumunun kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini çek
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Kullanıcı bulunamadı.");
}

// Kullanıcının paylaşımlarını çek
$stmt_posts = $pdo->prepare("SELECT post_id, title, content, image_path FROM posts WHERE user_id = :user_id");
$stmt_posts->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_posts->execute();
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Forum - Profil</title>
    
</head>
<style>
   /* Profil Sayfası Stil */
body {
    font-family: 'Courier New', Courier, monospace; /* Monospace font stili */
    background-color: #0c0c0c; /* Koyu arka plan */
    color: #f0f0f0; /* Açık yazı rengi */
    margin: 0;
    padding: 0;
}

.navbar {
    background-color: #1a1a1a; /* Koyu menü rengi */
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

.navbar a {
    float: left;
    display: block;
    color: #f0f0f0; /* Menü bağlantı rengi */
    text-align: center;
    padding: 14px 20px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.navbar a:hover {
    background-color: #007bff; /* Menü üzeri hover rengi */
    color: white;
}

.container {
    width: 80%;
    margin: 20px auto;
    background: #1e1e1e; /* Koyu içerik alanı rengi */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
}

h1, h2 {
    color: #00ff00; /* Yeşil başlık rengi */
    padding-bottom: 10px;
}

.profile-info {
    border: 1px solid #444; /* Koyu kenar rengi */
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
    background-color: #2a2a2a; /* Koyu arka plan */
}

.profile-info p {
    margin: 5px 0;
}

.post-list {
    margin-top: 30px;
}

.post-item {
    border: 1px solid #444; /* Koyu kenar rengi */
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
    background-color: #2a2a2a; /* Koyu arka plan */
    width: 300px;
    
}

.post-item h3 {
    color: #00ff00; /* Yeşil başlık rengi */
}

.post-item img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    margin-top: 10px;
}

.post-item a {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: white; /* Bağlantı rengi */
    transition: color 0.3s ease;
}

.post-item a:hover {
    color: #00ff00; /* Bağlantı hover rengi */
}

.post-item form {
    display: inline;
}

footer {
    text-align: center;
    padding: 20px 0;
    background-color: #1a1a1a; /* Koyu alt bilgi rengi */
    color: #f0f0f0; /* Açık yazı rengi */
    position: relative;
    bottom: 0;
    width: 100%;
    margin-top: 30px;
    
}



</style>
<body>
    <div class="navbar">
        <a href="forum.php">Ana Sayfa</a>
        <a href="profile.php">Profil</a>
        <a href="create_post.php">Gönderi Oluştur</a>
        <a href="login.php">Giriş Yap</a>
    </div>

    <div class="container">
        <h1>Profil Bilgileri</h1>
        <div class="profile-info">
            <p><strong>Kullanıcı Adı:</strong> <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
            <a href="edit_profile.php">Profili Düzenle</a>
        </div>

        <h2>Paylaşımlar</h2>
        <div class="post-list">
            <?php if (empty($posts)): ?>
                <p>Henüz bir paylaşım yapmadınız.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-item">
                        <h3><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if ($post['image_path']): ?>
                            <img src="<?= htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Gönderi Resmi" style="max-width: 100%; width: 200px; height: 200px;">
                        <?php endif; ?>
                        <a href="edit_post.php?id=<?= htmlspecialchars($post['post_id'], ENT_QUOTES, 'UTF-8') ?>">Gönderiyi Düzenle</a>
                        <form action="delete_post.php" method="POST" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['post_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="submit" value="Sil" onclick="return confirm('Bu gönderiyi silmek istediğinizden emin misiniz?');">
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        © 2024 Cyber Forum
    </footer>

</body>
</html>
