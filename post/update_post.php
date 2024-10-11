<?php

session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../registration/login.php");
  exit();
}

require_once "../components/connection.php";

if (isset($_POST["post_id"])) {
  $_SESSION["post_id"] = $_POST["post_id"];
  $post_id = $_SESSION["post_id"];
} else {
  $post_id = $_SESSION["post_id"];
}

$username = $_SESSION["username"];

$sql = "SELECT * FROM post WHERE post.id = $post_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$text = $row["text"];
$image = $row["image"];

$error = false;
$text_error = "";

if (isset($_POST["post"])) {
  $text = cleanInput($_POST["post_text"]);

  if (empty($text)) {
    $error = true;
    $text_error = "Post must include text!";
  }

  if (!$error) {
    $sql_post = "UPDATE `post` SET `text`='$text' WHERE id = $post_id";
    $result_post = mysqli_query($conn, $sql_post);

    if ($result_post) {
      echo "<div class='alert alert-success' role='alert'>
              Your post has been updated!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

    if (isset($_SESSION["user"])) {
      header("refresh: 1; url= '../user/my_journal.php?username={$username}");
    } else {
      header("refresh: 1; url= '../admin/index_posts_admin.php");
    }
  }

}

?>

<?php include "../components/header.php" ?>

<h2>Update Post</h2>
<div class="details-trip-div">
  <div class="card pb-0" style='margin: auto;width: 36rem; border: none'>
    <img src="<?=$row["image"]?>" class="card-img-top" alt="img">
    <form method="post" enctype="multipart/form-data">
      <textarea class="form-control" placeholder="Text..." name="post_text" rows="5"><?=$text?></textarea>
      <input type="submit" class="btn btn-success" style="position: absolute; bottom: 20px; right: 20px" value="Update" name="post">
    </form>
  </div>
  <p class="error-msg"><?= $text_error ?></p>
</div>

<?php include "../components/footer.php" ?>
