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

if (isset($_SESSION["post_id"])) {
  unset($_SESSION["post_id"]);
}

$sql_post = "SELECT DISTINCT post.image as image,
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
                WHERE follow.follower_id = $user_id OR trip.user_id = $user_id
                ORDER BY timestamp DESC";
$result_post = mysqli_query($conn, $sql_post);
$rows_post = mysqli_fetch_all($result_post, MYSQLI_ASSOC);

$display_post = "";

if (mysqli_num_rows($result_post) == 0) {
  $display_post = "<div>You don't follow anyone yet. Find roamers here -> <a href='../user/discover_journal.php' class='btn btn-outline-success mx-3'> <img src='../images/Roamio-icon-no-background.png' alt='roamio' width='30px' class='mr-2'> Discover Roamers</a></div>";
} else {
  foreach ($rows_post as $key => $row_post) {
    $timestamp = new DateTime($row_post["timestamp"]);
    $now = new DateTime('now');
    $diff = $now->diff($timestamp);
    $timing = ($diff->m < 1)?
    (($diff->d < 1)?
    ((($diff->h) < 1)?
    "Posted ".$diff->i." minutes ago":
    "Posted ".$diff->h." hours ".$diff->i." minutes ago"):
    "Posted ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago"):
    "Posted ".$diff->m." months ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago";
    $post_id = $row_post["post_id"];
    $sql_comments = "SELECT comment.text as com_text,
                  comment.user_id as com_user_id,
                  comment.timestamp as com_timestamp,
                  user.username as com_username,
                  user.image as com_image,
                  post.id as post_id
                  FROM comment
                  JOIN user
                  ON comment.user_id = user.id
                  JOIN post
                  ON post.id = comment.post_id
                  WHERE comment.post_id = $post_id
                  ";
    $result_comments = mysqli_query($conn, $sql_comments);
    $rows_comments = mysqli_fetch_all($result_comments, MYSQLI_ASSOC);

    $comments = "";

    if (mysqli_num_rows($result_comments) == 0) {
      $comments = "No comments yet";
    } else {
      foreach ($rows_comments as $key => $row_comment) {
        $comments .= "<div class='mt-2'>
                            <p class='comment-styling'><img src='{$row_comment["com_image"]}' class='user-avatar-comment'>
                            <small><a href='../user/my_journal.php?username={$row_comment["com_username"]}' style='pointer-events: all;' data-bs-dismiss='#{$post_id}' class='mx-1'>{$row_comment["com_username"]}</a></small>
                            <text>- {$row_comment["com_text"]}</text></p>
                          </div>";
      }
    }

    $sql_comments_abbrev = "SELECT comment.text as com_text,
                  comment.user_id as com_user_id,
                  comment.timestamp as com_timestamp,
                  user.username as com_username,
                  user.image as com_image,
                  post.id as post_id
                  FROM comment
                  JOIN user
                  ON comment.user_id = user.id
                  JOIN post
                  ON post.id = comment.post_id
                  WHERE comment.post_id = $post_id LIMIT 2
                  ";
    $result_comments_abbrev = mysqli_query($conn, $sql_comments_abbrev);
    $rows_comments_abbrev = mysqli_fetch_all($result_comments_abbrev, MYSQLI_ASSOC);

    $comments_abbrev = "";

    if (mysqli_num_rows($result_comments_abbrev) == 0) {
      $comments_abbrev = "No comments yet";
    } else {
      foreach ($rows_comments_abbrev as $key => $row_comment_abbrev) {
        $comments_abbrev .= "<div class='mt-2'>
                            <p class='comment-styling'><img src='{$row_comment_abbrev["com_image"]}' class='user-avatar-comment'>
                            <small><a href='../user/my_journal.php?username={$row_comment_abbrev["com_username"]}' style='pointer-events: all;' data-bs-dismiss='#{$post_id}' class='mx-1'>{$row_comment_abbrev["com_username"]}</a></small>
                            <text>- {$row_comment_abbrev["com_text"]}</text></p>
                          </div>";
      }
    }


    $comments_title = "Comments";
    if (mysqli_num_rows($result_comments) > 0) {
      $num_comments = mysqli_num_rows($result_comments);
      $comments_title = "Comments <small>({$num_comments})</small>";
    }

      $display_post .= "<div class='modal fade' id='{$post_id}' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                          <div class='modal-dialog modal-dialog-scrollable'>
                            <div class='card' style='border:none;'>
                              <div class='card-header' style='background-color:white'>
                                <div class='d-flex jcsb' style='align-items:center'>
                                  <div class='d-flex' style='align-items:center;pointer-events: all;'>
                                    <div><img src='{$row_post["user_image"]}' class='user-avatar' alt='{$row_post["username"]}'></div>
                                    <div><a href='../user/my_journal.php?username={$row_post["username"]}' class='mx-2 journal-username'>{$row_post["username"]}</a></div>
                                  </div>
                                  <div>
                                  <small class='text-body-secondary'>{$timing}</small>
                                  </div>
                                  </div>
                                </div>
                              <img src='{$row_post["image"]}' alt='img'>
                              <div class='card-body'>
                                  <h5 class='card-title'><i class='fa-solid fa-plane fa-margin'></i> {$row_post["destination"]}</h5>
                                <p class='card-text'><q>{$row_post["text"]}</q></p>
                              </div>
                              <div class='card-footer pb-3'>
                              <h6 style='text-align:center'>{$comments_title}</h6>
                              $comments
                              </div>
                            </div>
                          </div>
                        </div>


                        <div><div class='card'>
                          <div class='card-header' style='background-color:white'>
                                  <div class='d-flex' style='align-items:center'>
                                    <div><img src='{$row_post["user_image"]}' class='user-avatar' alt='{$row_post["username"]}'></div>
                                    <div><a href='../user/my_journal.php?username={$row_post["username"]}' class='mx-2 journal-username'>{$row_post["username"]}</a></div>
                                  </div>
                              </div>
                               <button type='button' data-bs-toggle='modal' data-bs-target='#{$post_id}' class='btn-modal-no-format'>
                            <img src='{$row_post["image"]}' class='post-img' alt='img'></button>
                              <div class='card-body'>
                                <h5 class='card-title'><i class='fa-solid fa-plane fa-margin'></i> {$row_post["destination"]}</h5>
                                <p class='card-text'><q>{$row_post["text"]}</q></p>
                              </div>
                              <div class='card-footer p-1' style='text-align:center'>
                                <small class='text-body-secondary'>{$timing}</small>
                              </div>
                              <div class='card-footer'>
                                <div class='d-flex' style='justify-content:space-between'>
                                  <button type='button' data-bs-toggle='modal' data-bs-target='#{$post_id}' class='btn-modal-no-format'><h5>{$comments_title}</h5></button>
                                  <div>
                                    <form method='POST' action='../comment/create_comment.php'>
                                      <input type='hidden' value='{$row_post['post_id']}' name='post_id'>
                                      <button type='submit' class='btn btn-success' name='submit'>+ <i class='fa-regular fa-comment'></i></button>
                                    </form>
                                  </div>
                                  </div>
                                $comments_abbrev
                              </div>
                            </div>
                          </div>";

  }
}

?>
<?php include "../components/header.php" ?>

    <h2>My Feed</h2>

    <div class="details-trip-div">
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?= $display_post ?>
      </div>
    </div>
<?php include "../components/footer.php" ?>
