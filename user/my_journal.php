<?php

  session_start();

  if (isset($_SESSION["user"])) {
    $surfing_user_id = $_SESSION["user"];
  }

  require_once "../components/connection.php";

  $username = $_GET["username"];
  $_SESSION["username"] = $username;

  $sql_user = "SELECT * FROM user WHERE username = '$username'";
  $result_user = mysqli_query($conn, $sql_user);
  $row_user = mysqli_fetch_assoc($result_user);

  $user_id = $row_user["id"];

  $sql_posts = "SELECT post.image as image,
              post.text as text,
              post.timestamp as timestamp,
              post.trip_id as trip_id,
              destination.name as destination,
              user.image as user_image,
              post.id as post_id
              FROM trip
              JOIN post
              ON trip.id = post.trip_id
              JOIN destination
              ON destination.id = trip.destination_id
              JOIN user
              ON user.id = trip.user_id
              WHERE trip.user_id = $user_id
              ORDER BY timestamp DESC";;
  $result_posts = mysqli_query($conn, $sql_posts);
  $rows_posts = mysqli_fetch_all($result_posts, MYSQLI_ASSOC);

  $post_num = mysqli_num_rows($result_posts);

  $display_posts = "";

  if ($post_num == 0) {
    $display_posts = "<h5>Nothing posted yet</h5>";
  } else {
    foreach ($rows_posts as $key => $row_post) {
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

      $comments_icon = "";

      if (mysqli_num_rows($result_comments) == 0) {
        $comments_icon = "";
      } else {
        $comments_num = mysqli_num_rows($result_comments);
        $comments_icon = "<button type='button' data-bs-toggle='modal' data-bs-target='#{$post_id}' class='btn btn-outline-success'>{$comments_num}<i class='fa-regular fa-comment fa-margin-left'></i></button>";
      }

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

      $editing_buttons = "";

      if ($surfing_user_id == $user_id) {
        $editing_buttons = "<div class='d-flex mb-1'>
                                  <div>
                                    <form method='POST' action='../post/update_post.php'>
                                      <input type='hidden' value='{$row_post['post_id']}' name='post_id'>
                                      <button type='submit' class='btn btn-orange' name='submit'><i class='fa-solid fa-pen-to-square'></i></button>
                                    </form>
                                  </div>
                                  <div style='margin-left:10px'>
                                    <form method='POST' action='../post/delete_post.php'>
                                      <input type='hidden' value='{$row_post['post_id']}' name='post_id'>
                                      <button type='submit' class='btn btn-secondary' name='submit'><i class='fa-regular fa-trash-can'></i></button>
                                    </form>
                                  </div>
                                </div>";
      }

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
      // first modal, then post card
      $display_posts .= "<div class='modal fade' id='{$post_id}' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                        <div class='modal-dialog modal-dialog-scrollable'>
                          <div class='card' style='border:none;'>
                            <div class='card-header' style='background-color:white'>
                              <div class='d-flex jcsb' style='align-items:center'>
                                <div class='d-flex' style='align-items:center;pointer-events: all;'>
                                  <div><img src='{$row_post["user_image"]}' class='user-avatar' alt='{$username}'></div>
                                  <div><a href='../user/my_journal.php?username={$username}' class='mx-2 journal-username'>{$username}</a></div>
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
                            <h6 style='text-align:center'>Comments</h6>
                            $comments
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class='modal fade' id='{$post_id}' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                          <div class='modal-content'>
                            <div class='modal-header' style='align-items:center'>
                              <text class='delete-modal-title'>Wait a second!</text>
                              <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                              <text style='color:#f66f4a'>Are you sure you want to delete this trip to {$row_post["destination"]}?</text><br>
                              <small style='color:#2e4c40'>(All posts associated with this trip will also be deleted)</small>
                            </div>
                            <div class='modal-footer'>
                              <form method='POST' action='../trip/delete_trip.php'>
                                <input type='hidden' value='{$post_id}' name='index_trip_id'>
                                <button type='submit' class='btn btn-secondary' data-bs-dismiss='#{$post_id}'>Yes, delete</button>
                              </form>
                              <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>Cancel</button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div><div class='card'>
                        <div class='card-header' style='background-color:white'>
                          <div class='d-flex jcsb'>
                                <div class='d-flex' style='align-items:center'>
                                  <div><img src='{$row_post["user_image"]}' class='user-avatar' alt='{$username}'></div>
                                  <div><a href='../user/my_journal.php?username={$username}' class='mx-2 journal-username'>{$username}</a></div>
                                </div>
                                <div>$comments_icon</div>
                            </div>
                            </div>
                          <button type='button' data-bs-toggle='modal' data-bs-target='#{$post_id}' class='btn-modal-no-format'>
                          <img src='{$row_post["image"]}' class='post-img' alt='img'>
                          </button>
                            <div class='card-body'>
                              <div class='d-flex jcsb'>
                                <h5 class='card-title'><i class='fa-solid fa-plane fa-margin'></i> {$row_post["destination"]}</h5>
                                $editing_buttons
                              </div>
                              <p class='card-text'><q>{$row_post["text"]}</q></p>
                            </div>
                            <div class='card-footer p-1' style='text-align:center'>
                              <small class='text-body-secondary'>{$timing}</small>
                            </div>
                          </div>
                        </div>";

    }
  }


  if (isset($_SESSION["user"])) {
    if ($surfing_user_id == $user_id) {
      $follow_button = "<a href='' class='btn btn-outline-secondary'><i class='fa-regular fa-heart'></i> Follow</a>";
    } else {
      $sql_follows = "SELECT * FROM follow WHERE follower_id = $surfing_user_id AND followee_id = $user_id";
      $result_follows = mysqli_query($conn, $sql_follows);

      if (mysqli_num_rows($result_follows) == 1) {
        $follow_button = "<form method='POST'><button type='submit' class='btn btn-secondary' name='unfollow'><i class='fa-solid fa-heart'></i> Unfollow</button></form>";
      } else {
        $follow_button = "<form method='POST'><button type='submit' class='btn btn-outline-success' name='follow'><i class='fa-regular fa-heart'></i> Follow</button></form>";
      }
    }
  } else {
    $follow_button = "<a href='../registration/login.php' class='btn btn-outline-success'><i class='fa-regular fa-heart'></i> Follow</a>";
  }

  $sql_followers = "SELECT * FROM follow WHERE followee_id = $user_id";
  $result_followers = mysqli_query($conn, $sql_followers);
  $followers_num = mysqli_num_rows($result_followers);

  $display_user = "";

  $display_user .= "<div class='card mb-3' style='max-width: 540px;border:none'>
                      <div class='row g-0'>
                        <div class='col-md-4'>
                          <img src='{$row_user["image"]}' class='img-fluid rounded-start horizontal-card-img' alt='...'>
                        </div>
                        <div class='col-md-8'>
                          <div class='card-body' style='text-align:center'>
                            <h4 class='card-title'>{$username}</h4>
                            <p>{$row_user["description"]}</p>
                            <div class='d-flex' style='justify-content:space-between; align-items:center'>
                            <div><p class='card-text'>{$post_num} posts</p></div>
                            <div><p class='card-text'>{$followers_num} followers</p></div>
                              <div>
                                {$follow_button}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    ";

if (isset($_POST["follow"])) {
  $sql_follow = "INSERT INTO `follow`(`follower_id`, `followee_id`) VALUES ('$surfing_user_id','$user_id')";
  $result_follow = mysqli_query($conn, $sql_follow);
  header("refresh:0;");
}

if (isset($_POST["unfollow"])) {
  $sql_follow = "DELETE FROM `follow` WHERE follower_id = $surfing_user_id AND followee_id = $user_id";
  $result_follow = mysqli_query($conn, $sql_follow);
  header("refresh:0;");
}

?>

<?php include "../components/header.php" ?>
  <div class=" my-5 center-div">
    <?=$display_user?>
  </div>
  <div class="details-trip-div">
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?=$display_posts?>
    </div>
  </div>

<?php include "../components/footer.php" ?>
