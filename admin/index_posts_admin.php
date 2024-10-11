<?php

session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../registration/login.php");
  exit();
}

if (isset($_SESSION["user"])) {
  header("Location: ../user/home_user.php");
  exit();
}

require_once "../components/connection.php";

$sql_posts = "SELECT post.image as image,
              post.text as text,
              post.timestamp as timestamp,
              post.trip_id as trip_id,
              destination.name as destination,
              user.image as user_image,
              post.id as post_id,
              user.username as username
              FROM trip
              JOIN post
              ON trip.id = post.trip_id
              JOIN destination
              ON destination.id = trip.destination_id
              JOIN user
              ON user.id = trip.user_id";
$result_posts = mysqli_query($conn, $sql_posts);
$rows_posts = mysqli_fetch_all($result_posts, MYSQLI_ASSOC);

$post_num = mysqli_num_rows($result_posts);

$display_posts = "";

if ($post_num == 0) {
  $display_posts = "<h5>Nothing posted yet</h5>";
  } else {
  foreach ($rows_posts as $key => $row_posts) {
    $timestamp = new DateTime($row_posts["timestamp"]);
    $now = new DateTime('now');
    $diff = $now->diff($timestamp);
    $timing = ($diff->m < 1)?
    (($diff->d < 1)?
    ((($diff->h) < 1)?
    "Posted ".$diff->i." minutes ago":
    "Posted ".$diff->h." hours ".$diff->i." minutes ago"):
    "Posted ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago"):
    "Posted ".$diff->m." months ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago";
    $display_posts .= "<div>
          <div class='card'>
            <img src='{$row_posts["image"]}' class='card-img-top' alt='img'>
            <div class='card-body'>
              <h5 class='card-title'>{$row_posts["destination"]}</h5>
              <p class='card-text'>{$row_posts["text"]}</p>
            </div>
            <div class='card-footer'>
              <div class='d-flex'>
                <div><img src='{$row_posts["user_image"]}' class='user-avatar' alt='{$username}'></div>
                <div><p class='mx-3'>{$row_posts["username"]}</p></div>
              </div>
              <small class='text-body-secondary'>{$timing}</small>
            </div>
            <div class='d-flex p-3' style='justify-content:space-between'>
              <form method='POST' action='../post/update_post.php'>
                <input type='hidden' value='{$row_posts['post_id']}' name='post_id'>
                <input type='submit' class='btn btn-info' name='submit' value='Edit'>
              </form>
              <form method='POST' action='../post/delete_post.php'>
                <input type='hidden' value='{$row_posts['post_id']}' name='post_id'>
                <input type='submit' class='btn btn-warning mx-3' name='submit' value='Delete'>
              </form>
            </div>
          </div>
        </div>";
  }
}
?>

<?php include "../components/header.php" ?>

<div class="mt-5 mb-3">
  <h2>All Posts</h2>
</div>

<!-- include ajax search bar -->

<div class="container" style="width: 100; margin-top: 5vh;">
<div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
<?= $display_posts ?>
  </div>
</div>

<?php include "../components/footer.php" ?>
