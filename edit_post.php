<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$title = $content = "";
$post_id = $_GET['id'];

// **IMPROVEMENT**: Security check to ensure user owns the post before editing
$sql_check = "SELECT user_id FROM posts WHERE id = :id";
if($stmt_check = $pdo->prepare($sql_check)){
    $stmt_check->bindParam(":id", $post_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $post = $stmt_check->fetch();
    if(!$post || $post['user_id'] != $_SESSION['id']){
        die("Access Denied. You do not have permission to edit this post.");
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sql = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":title", $_POST['title'], PDO::PARAM_STR);
        $stmt->bindParam(":content", $_POST['content'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $post_id, PDO::PARAM_INT);
        if($stmt->execute()){
            header("location: index.php");
        }
    }
} else {
    $sql = "SELECT title, content FROM posts WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $post_id, PDO::PARAM_INT);
        if($stmt->execute()){
            $row = $stmt->fetch();
            $title = $row['title'];
            $content = $row['content'];
        }
    }
}
require_once "header.php";
?>
<h2>Edit Post</h2>
<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
    </div>
    <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="5"><?php echo $content; ?></textarea>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Update">
    </div>
</form>
<?php require_once "footer.php"; ?>