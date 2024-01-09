<?php
session_start();

$db = new PDO('sqlite:database.db');
$db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL, password TEXT NOT NULL)");
$db->exec("CREATE TABLE IF NOT EXISTS posts (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, content TEXT NOT NULL, user_id INTEGER NOT NULL, date DATETIME, FOREIGN KEY (user_id) REFERENCES users(id))");
$db->exec("CREATE TABLE IF NOT EXISTS comments (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, comment TEXT, date DATETIME, post_id TEXT)");

$posts_per_page = 100;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_index = ($page - 1) * $posts_per_page;

$query = "SELECT posts.*, users.username, users.id AS userid FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC LIMIT $start_index, $posts_per_page";
$posts = $db->query($query)->fetchAll();

$count_query = "SELECT COUNT(*) FROM posts";
$total_posts = $db->query($count_query)->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title>HiroFORUM</title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <?php include('header.php'); ?>
    <div class="container my-4">
      <?php foreach ($posts as $post): ?>
        <div class="card border-0 shadow mb-1 position-relative bg-body-tertiary rounded-4">
          <div class="card-body">
            <div class="d-flex mb-3">
              <small class="small fw-medium">Thread by <?php echo (mb_strlen($post['username']) > 15) ? mb_substr($post['username'], 0, 15) . '...' : $post['username']; ?>・<?php echo (new DateTime($post['date']))->format("Y/m/d - H:i:s"); ?></small>
            </div>
            <h5 class="mb-2 fw-bold"><?php echo $post['title']; ?></h5>
            <?php
              if (!empty($post['content'])) foreach (explode("\n", $post['content']) as $paragraph) echo '<p style="white-space: break-spaces; overflow: hidden;">' . preg_replace_callback('/\bhttps?:\/\/\S+/i', fn($matches) => '<a href="' . htmlspecialchars($matches[0]) . '">' . htmlspecialchars($matches[0]) . '</a>', strip_tags($paragraph)) . '</p>';
              else echo "Sorry, no text...";
            ?>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['userid']): ?>
              <a class="btn btn-sm border-0 m-2 position-absolute top-0 end-0" href="edit.php?id=<?php echo $post['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
            <?php endif; ?>
            <div class="m-2 position-absolute bottom-0 end-0">
              <a class="btn btn-sm border-0 fw-medium" href="reply.php?id=<?php echo $post['id']; ?>"><i class="bi bi-reply-fill"></i> Reply this thread</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="pagination my-4 justify-content-center gap-2">
      <?php if ($page > 1): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?page=<?php echo $page - 1 ?>">Prev</a>
      <?php endif ?>

      <?php
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);

      for ($i = $start_page; $i <= $end_page; $i++):
      ?>
        <a class="btn btn-sm fw-bold btn-primary <?php echo ($i == $page) ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
      <?php endfor ?>

      <?php if ($page < $total_pages): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?page=<?php echo $page + 1 ?>">Next</a>
      <?php endif ?>
    </div>
  </body>
</html>