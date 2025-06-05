<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "header.php";

// --- PAGINATION & SEARCH LOGIC ---

// 1. Define how many posts to display per page
$limit = 6; // Changed to 6 to fit a grid layout better

// 2. Get the current page number from the URL, default to page 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// 3. Get the search term from the URL
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// 4. Prepare the base SQL queries
$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.user_id, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id";
$count_sql = "SELECT COUNT(posts.id) FROM posts JOIN users ON posts.user_id = users.id";

// 5. Add search condition if a search term exists
if (!empty($search_term)) {
    $sql .= " WHERE posts.title LIKE :search OR posts.content LIKE :search";
    $count_sql .= " WHERE posts.title LIKE :search OR posts.content LIKE :search";
}

// 6. Get the total number of posts (for calculating total pages)
$count_stmt = $pdo->prepare($count_sql);
if (!empty($search_term)) {
    $count_stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
}
$count_stmt->execute();
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// 7. Add ordering and pagination limit to the main query
$sql .= " ORDER BY posts.created_at DESC LIMIT :start, :limit";

// 8. Prepare and execute the final query to get the posts for the current page
$stmt = $pdo->prepare($sql);
if (!empty($search_term)) {
    $stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

?>
<div class="d-flex justify-content-between align-items-center">
    <h2>Blog Posts</h2>
    <!-- Search Form -->
    <form action="index.php" method="get" class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search for posts..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
</div>
<hr>

<!-- Improved Card Layout -->
<div class="row">
    <?php while($row = $stmt->fetch()): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        <a href="#"><?php echo htmlspecialchars($row['title']); ?></a>
                    </h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</p>
                    <p class="card-text mt-auto"><small class="text-muted">By <?php echo htmlspecialchars($row['username']); ?> on <?php echo date('M j, Y', strtotime($row['created_at'])); ?></small></p>
                    
                    <?php if(isset($_SESSION['id']) && $_SESSION['id'] == $row['user_id']): ?>
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

<!-- Pagination Links -->
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