<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}

if (isset($_POST['submit'])) {
  $title = htmlspecialchars($_POST['title']);
  $content = htmlspecialchars($_POST['content']);
  $date = date('Y-m-d H:i:s'); // format the current date as "YYYY-MM-DD"
  $stmt = $db->prepare("INSERT INTO posts (title, content, user_id, date) VALUES (:title, :content, :user_id, :date)"); // added the "date" column
  $stmt->execute(array(':title' => $title, ':content' => $content, ':user_id' => $_SESSION['user_id'], ':date' => $date)); // insert the formatted date into the "date" column
  header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title>Upload</title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <?php include('header.php'); ?>
    <form method="post" enctype="multipart/form-data" class="container-fluid mt-3">
      <div class="form-floating mb-2">
        <input class="form-control rounded border-3 focus-ring focus-ring-dark" type="text" name="title" placeholder="Enter title" maxlength="100" required>  
        <label for="floatingInput" class="fw-bold"><small>Enter title</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control rounded border-3 focus-ring focus-ring-dark vh-100" name="content" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }" placeholder="Enter content" required></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter content</small></label>
      </div>
      <button class="btn btn-primary fw-bold mb-5 w-100" type="submit" name="submit">Submit</button>
    </form>
  </body>
</html>