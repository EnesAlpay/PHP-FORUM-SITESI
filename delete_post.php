<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if (isset($_POST['post_id'])) {
    $post_id = (int) $_POST['post_id'];

 
    $stmt_comments = $pdo->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $stmt_comments->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt_comments->execute();


    $stmt_posts = $pdo->prepare("DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id");
    $stmt_posts->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt_posts->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT); 
    if ($stmt_posts->execute()) {

        header('Location: forum.php?message=Gönderi başarıyla silindi.');
    } else {
      
        header('Location: forum.php?error=Gönderi silerken bir hata oluştu.');
    }
} else {
   
    header('Location: forum.php');
}
?>
