<?php
session_start(); // Oturumu başlat
require 'config.php';

// Kullanıcı oturumunun kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Silinecek gönderinin ID'sini al
if (isset($_POST['post_id'])) {
    $post_id = (int) $_POST['post_id'];

    // Önce bu gönderiye ait yorumları sil
    $stmt_comments = $pdo->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $stmt_comments->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt_comments->execute();

    // Ardından gönderiyi sil
    $stmt_posts = $pdo->prepare("DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id");
    $stmt_posts->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt_posts->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT); // user_id parametreyi buradan bağla

    if ($stmt_posts->execute()) {
        // Silme işlemi başarılıysa
        header('Location: forum.php?message=Gönderi başarıyla silindi.');
    } else {
        // Hata durumunda
        header('Location: forum.php?error=Gönderi silerken bir hata oluştu.');
    }
} else {
    // Post ID yoksa yönlendir
    header('Location: forum.php');
}
?>
