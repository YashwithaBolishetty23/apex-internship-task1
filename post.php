<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("location: index.php");
    exit;
}

$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.user_id, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = :id";

if($stmt = $pdo->prepare($sql)){
    $stmt->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$post){
        header("location: index.php");
        exit;
    }
}
require_once "header.php";
?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                <p class="card-text"><small class="text-muted">By <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></small></p>
                <hr>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            </div>
        </div>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Posts</a>
    </div>
</div>
<?php require_once "footer.php"; ?>