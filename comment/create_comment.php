<?php
session_start();

require_once "../components/connection.php";

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../index.php");
  exit();
}

if (isset($_SESSION["admin"])) {
  header("Location: ../admin/dashboard_admin.php");
  exit();
}

$user_id = $_SESSION["user"];

$post_id = $_POST["post_id"];


$sql = "SELECT post.image as image,
                post.text as text,
                post.timestamp as timestamp,
                user.username as username,
                user.image as user_image,
                destination.name as destination,
                post.id as post_id
                FROM post
                JOIN trip
                ON trip.id = post.trip_id
                JOIN user
                ON trip.user_id = user.id
                JOIN destination
                ON destination.id = trip.destination_id
                JOIN follow
                ON follow.followee_id = user.id
                WHERE post.id = $post_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$timestamp = new DateTime($row["timestamp"]);
$now = new DateTime('now');
$diff = $now->diff($timestamp);
$timing = ($diff->d < 1)?
((($diff->h) < 1)?
"Posted ".$diff->i." minutes ago":
"Posted ".$diff->h." hours ".$diff->i." minutes ago"):
"Posted ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago";
$display = "<div><div class='card center-div' style='width:36rem'>
                        <div class='card-header' style='background-color:white'>
                                <div class='d-flex' style='align-items:center'>
                                  <div><img src='{$row["user_image"]}' class='user-avatar' alt='{$row["username"]}'></div>
                                  <div><a href='../user/my_journal.php?username={$row["username"]}' class='mx-2 journal-username'>{$row["username"]}</a></div>
                                </div>
                        </div>
                        <img src='{$row["image"]}' alt='img'>
                        <div class='card-footer' style='text-align:center'>
                            <small class='text-body-secondary'>{$timing}</small>
                          </div>
                          <div class='card-body'>
                              <h5 class='card-title'><i class='fa-solid fa-plane fa-margin'></i> {$row["destination"]}</h5>
                            <p class='card-text'>{$row["text"]}</p>
                          </div>
                          <form method='post'>
                          <div class='d-flex jcsb'>
                          <input type='text' class='form-control' placeholder='Comment...' name='text'>
                          <input type='hidden' value='{$row["post_id"]}' name='post_id'>
                          <input type='submit' class='btn btn-primary' name='comment' value='Comment'>
                          </div>
                          </form>
                        </div>
                      </div>";

if (isset($_POST["comment"])) {
  $text = cleanInput($_POST["text"]);
  $sql_comment = "INSERT INTO `comment`(`text`, `post_id`, `user_id`) VALUES ('$text','$post_id','$user_id')";
  $result_comment = mysqli_query($conn, $sql_comment);

  if ($result_comment) {
    echo "<div class='alert alert-success' role='alert'>
              Commented!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

  header("refresh: 1; url= '../user/my_feed.php");


}

?>
<?php include "../components/header.php" ?>

   <h2>Add Comment</h2>
   <div class="details-trip-div">
   <?=$display?>
  </div>
<?php include "../components/footer.php" ?>
