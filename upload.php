<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}

// Retrieve the list of albums created by the current user
$stmt = $db->prepare('SELECT * FROM category ORDER BY category_name ASC');
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
  $title = htmlspecialchars($_POST['title']);
  $content = htmlspecialchars($_POST['content']);
  $category = htmlspecialchars($_POST['category']);
  $date = date('Y-m-d H:i:s'); // format the current date as "YYYY-MM-DD"
  $stmt = $db->prepare("INSERT INTO posts (title, content, user_id, date, category) VALUES (:title, :content, :user_id, :date, :category)"); // added the "date" column
  $stmt->execute(array(':title' => $title, ':content' => $content, ':user_id' => $_SESSION['user_id'], ':date' => $date, ':category' => $category)); // insert the formatted date into the "date" column
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
    <form method="post" enctype="multipart/form-data" class="container mt-3">
      <div class="form-floating mb-2">
        <input class="form-control rounded border-3 focus-ring focus-ring-dark" type="text" name="title" placeholder="Enter title" maxlength="100" required>  
        <label for="floatingInput" class="fw-bold"><small>Enter title</small></label>
      </div>
      <div class="form-floating mb-2">
        <select class="form-select border rounded border-3 fw-bold focus-ring focus-ring-dark py-0 text-start" name="category">
          <option class="form-control" value="">Add category:</option>
          <?php
            // Loop through each category and create an option in the dropdown list
            foreach ($results as $row) {
              $category_name = $row['category_name'];
              $id = $row['id'];
              echo '<option value="' . htmlspecialchars($category_name) . '">' . htmlspecialchars($category_name) . '</option>';
            }
          ?>
        </select>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control rounded border-3 focus-ring focus-ring-dark vh-100" name="content" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }" placeholder="Enter content" required></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter content</small></label>
      </div>
      <button class="btn btn-primary fw-bold mb-5 w-100" type="submit" name="submit">Submit</button>
    </form>
  </body>
</html>