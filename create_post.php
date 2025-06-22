<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sql = "INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":title", $_POST['title'], PDO::PARAM_STR);
        $stmt->bindParam(":content", $_POST['content'], PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $_SESSION['id'], PDO::PARAM_INT);
        if($stmt->execute()){
            header("location: index.php");
        }
    }
}
require_once "header.php";
?>
<h2>Create New Post</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" class="form-control">
    </div>
    <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="5"></textarea>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
    </div>
</form>
<?php require_once "footer.php"; ?>