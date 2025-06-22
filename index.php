<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "header.php";

$sql = "SELECT posts.id, title, content, created_at, username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
$stmt = $pdo->query($sql);
?>
<h2>Blog Posts</h2>
<?php while($row = $stmt->fetch()): ?>
    <div class="card my-3">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars(substr($row['content'], 0, 150)); ?>...</p>
            <p class="card-text"><small class="text-muted">By <?php echo htmlspecialchars($row['username']); ?> on <?php echo $row['created_at']; ?></small></p>
            <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
            <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
        </div>
    </div>
<?php endwhile; ?>
<?php require_once "footer.php"; ?>
