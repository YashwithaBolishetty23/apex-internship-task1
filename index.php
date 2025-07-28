<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "header.php";

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.user_id, users.username FROM posts JOIN users ON posts.user_id = users.id";
$count_sql = "SELECT COUNT(posts.id) FROM posts JOIN users ON posts.user_id = users.id";

$where_clauses = [];
$params = [];

if ($_SESSION['role'] !== 'admin') {
    $where_clauses[] = "posts.user_id = :user_id";
    $params[':user_id'] = $_SESSION['id'];
}

if (!empty($search_term)) {
    $where_clauses[] = "(posts.title LIKE :search OR posts.content LIKE :search)";
    $params[':search'] = '%' . $search_term . '%';
}

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
    $count_sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

$sql .= " ORDER BY posts.created_at DESC LIMIT :start, :limit";
$stmt = $pdo->prepare($sql);

if (isset($params[':user_id'])) { $stmt->bindValue(':user_id', $params[':user_id'], PDO::PARAM_INT); }
if (isset($params[':search'])) { $stmt->bindValue(':search', $params[':search'], PDO::PARAM_STR); }
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
?>
<div class="d-flex justify-content-between align-items-center">
    <h2><?php echo ($_SESSION['role'] === 'admin') ? 'All Posts' : 'My Posts'; ?></h2>
    <form action="index.php" method="get" class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search for posts..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
</div>
<hr>

<div class="row">
    <?php while($row = $stmt->fetch()): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <!-- **FIXED**: Link now points to the new post.php page -->
                    <h5 class="card-title"><a href="post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</p>
                    <p class="card-text mt-auto"><small class="text-muted">By <?php echo htmlspecialchars($row['username']); ?> on <?php echo date('M j, Y', strtotime($row['created_at'])); ?></small></p>
                    
                    <?php if(isset($_SESSION['id']) && ($_SESSION['id'] == $row['user_id'] || $_SESSION['role'] === 'admin')): ?>
                        <div class="mt-2">
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<nav>
    <ul class="pagination justify-content-center">
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                <a class="page-link" href="index.php?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search_term); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require_once "footer.php"; ?>