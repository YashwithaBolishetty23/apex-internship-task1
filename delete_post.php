<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if(isset($_GET["id"]) && !empty($_GET["id"])){
    $sql_check = "SELECT user_id FROM posts WHERE id = :id";
    if($stmt_check = $pdo->prepare($sql_check)){
        $stmt_check->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
        $stmt_check->execute();
        $post = $stmt_check->fetch();
        if(!$post || ($post['user_id'] != $_SESSION['id'] && $_SESSION['role'] !== 'admin')){
            die("Access Denied. You do not have permission to delete this post.");
        }
    }

    $sql = "DELETE FROM posts WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
        if($stmt->execute()){
            // **NEW**: Set flash message
            $_SESSION['flash_message'] = "Post deleted successfully!";
            header("location: index.php");
            exit;
        } else{
            echo "Oops! Something went wrong.";
        }
    }
}
?>