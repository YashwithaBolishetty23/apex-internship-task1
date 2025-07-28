<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] !== 'admin'){
    header("location: index.php"); // Redirect non-admins
    exit;
}
require_once "header.php";

// Fetch all users
$sql_users = "SELECT id, username, role, created_at FROM users ORDER BY created_at DESC";
$users_stmt = $pdo->query($sql_users);

// Fetch all posts
$sql_posts = "SELECT posts.id, posts.title, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
$posts_stmt = $pdo->query($sql_posts);
?>
<h2>Admin Dashboard</h2>
<hr>
<h4>All Users</h4>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Registered On</th>
        </tr>
    </thead>
    <tbody>
        <?php while($user = $users_stmt->fetch()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo $user['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h4 class="mt-5">All Posts</h4>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
        </tr>
    </thead>
    <tbody>
        <?php while($post = $posts_stmt->fetch()): ?>
            <tr>
                <td><?php echo $post['id']; ?></td>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td><?php echo htmlspecialchars($post['username']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once "footer.php"; ?>