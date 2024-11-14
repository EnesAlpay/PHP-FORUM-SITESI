<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        die('CSRF token validation failed.');
    }

    $post_id = $_POST['post_id'];
    $comment = htmlspecialchars(trim($_POST['comment']), ENT_QUOTES, 'UTF-8');
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (:post_id, :user_id, :comment, NOW())");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':comment', $comment);
    
    if ($stmt->execute()) {
        header("Location: forum.php"); 
        exit();
    } else {
        echo "Yorum eklenirken bir hata oluştu.";
             
        print_r($stmt->errorInfo());
    }
}
?>
