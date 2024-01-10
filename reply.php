<?php
session_start();

$db = new PDO('sqlite:database.db');

// Check if the user is logged in
$user = null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // Fetch user information
  $query = "SELECT * FROM users WHERE id='$user_id'";
  $user = $db->query($query)->fetch();

  // Get the 'id' parameter from the URL
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
  }

  // Handle comment creation
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $username = $user['username'];
    $comment = nl2br(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW));

    // Check if the comment is not empty
    if (!empty(trim($comment))) {
      // Insert the comment with the associated post_id
      $stmt = $db->prepare('INSERT INTO comments (username, comment, date, post_id) VALUES (?, ?, ?, ?)');
      $stmt->execute([$username, $comment, date("Y-m-d H:i:s"), $id]);

      // Redirect to prevent form resubmission
      header("Location: reply.php?id=$id");
      exit();
    } else {
      // Handle the case where the comment is empty
      echo "<script>alert('Reply cannot be empty.');</script>";
    }
  }

  // Handle comment deletion
  if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['action']) &&
    $_GET['action'] === 'delete' &&
    isset($_GET['commentId']) && // Use commentId instead of id
    isset($id) &&
    isset($user)
  ) {
    // Delete the comment based on ID and username
    $stmt = $db->prepare('DELETE FROM comments WHERE id = ? AND username = ?');
    $stmt->execute([$_GET['commentId'], $user['username']]);

    // Redirect to prevent form resubmission
    header("Location: reply.php?id=$id");
    exit();
  }
}

// Fetch post information
$query = "SELECT posts.id, posts.title, posts.content, posts.user_id, posts.date, users.username, users.id AS userid FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = '$id'";
$post = $db->query($query)->fetch();

// Get comments for the current page, ordered by id in descending order
$query = "SELECT * FROM comments WHERE post_id='$id' ORDER BY id ASC";
$comments = $db->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to <?php echo $post['title']; ?></title>
    <?php include('bootstrapcss.php'); ?>
	<meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="HiroFORUM">
    <meta property="og:description" content="<?php echo $post['title']; ?>">
    <meta property="og:image" content="/favicon.svg">
  </head>
  <body>
    <?php include('header.php'); ?>
    <div class="container mt-3 mb-5">
      <div class="card rounded-4 bg-body-tertiary border-0 my-5 fw-medium">
        <div class="card-body">
          <small class="small fw-medium">Thread by <?php echo (mb_strlen($post['username']) > 15) ? mb_substr($post['username'], 0, 15) . '...' : $post['username']; ?>・<?php echo (new DateTime($post['date']))->format("Y/m/d - H:i:s"); ?></small>
          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['userid']): ?>
            <a class="btn btn-sm border-0 m-2 position-absolute top-0 end-0" href="edit.php?id=<?php echo $post['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
          <?php endif; ?>
          <h5 class="mb-2 fw-bold mt-3"><?php echo $post['title']; ?></h5>
          <?php
            if (!function_exists('getYouTubeVideoId')) {
              function getYouTubeVideoId($urlCommentThread)
              {
                $videoIdThread = '';
                $patternThread = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
                if (preg_match($patternThread, $urlCommentThread, $matchesThread)) {
                  $videoIdThread = $matchesThread[1];
                }
                return $videoIdThread;
              }
            }

            $mainTextThread = isset($post['content']) ? $post['content'] : '';

            if (!empty($mainTextThread)) {
              $paragraphsThread = explode("\n", $mainTextThread);

              foreach ($paragraphsThread as $indexThread => $paragraphThread) {
                $textWithoutTagsThread = strip_tags($paragraphThread);
                $patternThread = '/\bhttps?:\/\/\S+/i';

                $formattedTextThread = preg_replace_callback($patternThread, function ($matchesThread) {
                  $urlThread = htmlspecialchars($matchesThread[0]);

                  // Check if the URL ends with .png, .jpg, .jpeg, or .webp
                  if (preg_match('/\.(png|jpg|jpeg|webp)$/i', $urlThread)) {
                    return '<a href="' . $urlThread . '" target="_blank"><img class="img-fluid rounded-4" loading="lazy" src="' . $urlThread . '" alt="Image"></a>';
                  } elseif (strpos($urlThread, 'youtube.com') !== false) {
                    // If the URL is from YouTube, embed it as an iframe with a very low-resolution thumbnail
                    $videoIdThread = getYouTubeVideoId($urlThread);
                    if ($videoIdThread) {
                      $thumbnailUrlThread = 'https://img.youtube.com/vi/' . $videoIdThread . '/default.jpg';
                      return '<div class="w-100 overflow-hidden position-relative ratio ratio-16x9"><iframe loading="lazy" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" class="rounded-4 position-absolute top-0 bottom-0 start-0 end-0 w-100 h-100 border-0 shadow" src="https://www.youtube.com/embed/' . $videoIdThread . '" frameborder="0" allowfullscreen></iframe></div>';
                    } else {
                      return '<a href="' . $urlThread . '">' . $urlThread . '</a>';
                    }
                  } else {
                    return '<a href="' . $urlThread . '">' . $urlThread . '</a>';
                  }
                }, $textWithoutTagsThread);

                echo "<p style=\"white-space: break-spaces; overflow: hidden;\">$formattedTextThread</p>";
              }
            } else {
              echo "Sorry, no text...";
            }
          ?>
        </div>
        <br>
        <button type="button" class="btn btn-sm border-0 fw-medium m-2 position-absolute bottom-0 end-0" onclick="sharePage()"><i class="bi bi-share-fill"></i></button>
      </div>

      <!-- Comment form, show only if the user is logged in -->
      <?php if ($user): ?>
        <form method="post" action="reply.php?id=<?php echo $id; ?>">
          <textarea id="comment" name="comment" class="form-control border-2 rounded-4 focus-ring focus-ring-dark rounded-bottom-0 border-bottom-0" rows="6" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }"></textarea>
          <button type="submit" class="btn w-100 btn-primary rounded-top-0 rounded-4 fw-medium">Submit</button>
        </form>
      <?php else: ?>
        <h5 class="text-center">You must <a href="session.php">login</a> or <a href="session.php">register</a> to reply this thread!</h5>
      <?php endif; ?>
      <br>
      <?php foreach ($comments as $comment): ?>
        <div class="card rounded-4 bg-body-tertiary border-0 mt-1">
          <div class="card-body">
            <div class="d-flex mb-3">
              <small class="small fw-medium">Reply by <?php echo (mb_strlen($comment['username']) > 15) ? mb_substr($comment['username'], 0, 15) . '...' : $comment['username']; ?>・<?php echo (new DateTime($comment['date']))->format("Y/m/d - H:i:s"); ?></small>
              <?php if ($user && $comment['username'] == $user['username']): ?>
                <a href="reply.php?action=delete&commentId=<?php echo $comment['id']; ?>&id=<?php echo $id; ?>" style="max-height: 30px;" onclick="return confirm('Are you sure?');" class="btn btn-outline-light border-0 btn-sm ms-auto"><i class="bi bi-trash-fill"></i></a>
              <?php endif; ?>
            </div>
            <div>
              <?php
                if (!function_exists('getYouTubeVideoId')) {
                  function getYouTubeVideoId($urlComment)
                  {
                    $videoId = '';
                    $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
                    if (preg_match($pattern, $urlComment, $matches)) {
                      $videoId = $matches[1];
                    }
                    return $videoId;
                  }
                }

                $replyText = isset($comment['comment']) ? $comment['comment'] : '';

                if (!empty($replyText)) {
                  $paragraphs = explode("\n", $replyText);

                  foreach ($paragraphs as $index => $paragraph) {
                    $textWithoutTags = strip_tags($paragraph);
                    $pattern = '/\bhttps?:\/\/\S+/i';

                    $formattedText = preg_replace_callback($pattern, function ($matches) {
                      $url = htmlspecialchars($matches[0]);

                      // Check if the URL ends with .png, .jpg, .jpeg, or .webp
                      if (preg_match('/\.(png|jpg|jpeg|webp)$/i', $url)) {
                        return '<a href="' . $url . '" target="_blank"><img class="img-fluid rounded-4" loading="lazy" src="' . $url . '" alt="Image"></a>';
                      } elseif (strpos($url, 'youtube.com') !== false) {
                        // If the URL is from YouTube, embed it as an iframe with a very low-resolution thumbnail
                        $videoId = getYouTubeVideoId($url);
                        if ($videoId) {
                          $thumbnailUrl = 'https://img.youtube.com/vi/' . $videoId . '/default.jpg';
                          return '<div class="w-100 overflow-hidden position-relative ratio ratio-16x9"><iframe loading="lazy" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" class="rounded-4 position-absolute top-0 bottom-0 start-0 end-0 w-100 h-100 border-0 shadow" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
                        } else {
                          return '<a href="' . $url . '">' . $url . '</a>';
                        }
                      } else {
                        return '<a href="' . $url . '">' . $url . '</a>';
                      }
                    }, $textWithoutTags);

                    echo "<p style=\"white-space: break-spaces; overflow: hidden;\">$formattedText</p>";
                  }
                } else {
                  echo "Sorry, no text...";
                }
              ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-outline-light rounded-pill border-0 btn-sm position-fixed end-0 bottom-0 m-2 fw-medium" data-bs-toggle="modal" data-bs-target="#exampleModal">help</button>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header border-0">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Help</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            If you want to upload image, use image link address, only support jpg, jpeg, png, and gif.
          </div>
        </div>
      </div>
    </div>
    <script>
      function sharePage() {
        if (navigator.share) {
          navigator.share({
            title: document.title,
            url: window.location.href
          }).then(() => {
            console.log('Page shared successfully.');
          }).catch((error) => {
            console.error('Error sharing page:', error);
          });
        } else {
          console.log('Web Share API not supported.');
        }
      }
    </script>
  </body>
</html>