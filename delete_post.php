<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$sql = "DELETE FROM posts WHERE id = :id";
if($stmt = $pdo->prepare($sql)){
    $stmt->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
    if($stmt->execute()){
        header("location: index.php");
    } else{
        echo "Oops! Something went wrong.";
    }
}
?>