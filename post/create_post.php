<?php

session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../registration/login.php");
  exit();
}

if (isset($_SESSION["admin"])) {
  header("Location: ../admin/dashboard_admin.php");
  exit();
}

require_once "../components/connection.php";

$user_id = $_SESSION["user"];

if (isset($_POST["index_trip_id"])) {
  $trip_id = $_POST["index_trip_id"];
  $_SESSION["trip_id"] = $trip_id;
} else {
  $trip_id = $_SESSION["trip_id"];
}

$sql_user = "SELECT * FROM user WHERE id = $user_id";
$result_user = mysqli_query($conn, $sql_user);
$row_user = mysqli_fetch_assoc($result_user);
$username = $row_user["username"];

$sql = "SELECT destination.name as destination,
        destination.country as destination_country
        FROM destination
        JOIN trip
        ON trip.destination_id = destination.id
        WHERE trip.id = $trip_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$destination = $row["destination"];
$destination_country = $row["destination_country"];

$text = "";

$error = false;
$text_error = "";
$image_error = "";

if (isset($_POST["post"])) {
  $trip = $trip_id;
  $text = cleanInput($_POST["post_text"]);
  $image = $_POST["image"];

  if (empty($text)) {
    $error = true;
    $text_error = "Post must include text!";
  }

  if (empty($image)) {
    $error = true;
    $image_error = "Please upload an image";
  }

  if (!$error) {
    $sql_post = "INSERT INTO `post`(`image`, `text`, `trip_id`) VALUES ('$image','$text','$trip')";
    $result_post = mysqli_query($conn, $sql_post);

    if ($result_post) {
      echo "<div class='alert alert-success' role='alert'>
              Your post is live!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

    header("refresh: 1; url= '../user/my_journal.php?username={$username}");
  }
}


?>

<?php include "../components/header.php" ?>

<h2>Create Post</h2>

<div class="center-div form-div">
  <h5 class="subtitle">For my trip to <?= $destination ?></h5>
  <form method="post" enctype="multipart/form-data">
  <label for="image" class="mb-2">Post image:</label><br>
  <input id="uploader-preview-here-3536" class="simple-file-upload" name="image" type="hidden" data-maxFileSize="10" data-accepted="image/*" data-width="200" data-height="200">
    <p class="error-msg"><?= $image_error ?></p>
    <textarea class="form-format" placeholder="Text..." name="post_text" rows="5"></textarea>
    <p class="error-msg"><?= $text_error ?></p>
    <div class="form-button-right mb-3">
      <input type="submit" class="btn btn-success mt-3" value="Post" name="post">
    </div>
  </form>
</div>

<?php include "../components/footer.php" ?>
