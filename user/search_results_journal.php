<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../index.php");
  exit();
}

if (isset($_SESSION["admin"])) {
  header("Location: ../admin/dashboard_admin.php");
  exit();
}

require_once "../components/connection.php";

$user_id = $_SESSION["user"];

$search_entry = $_GET["search"];

$sql_post = "SELECT post.image as image,
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
                WHERE NOT trip.user_id = $user_id
                AND (user.username LIKE '%$search_entry%' OR destination.name LIKE '%$search_entry%' OR destination.country LIKE '%$search_entry%')
                ORDER BY timestamp DESC";
$result_post = mysqli_query($conn, $sql_post);
$rows_post = mysqli_fetch_all($result_post, MYSQLI_ASSOC);

$display_post = "";

if (mysqli_num_rows($result_post) == 0) {
  $display_post = "No posts found for {$search_entry}.";
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

    $display_post .= "<div><div class='card'>
                      <div class='card-header' style='background-color:white'>
                      <div class='d-flex' style='justify-content:space-between; align-items:center'>
                                <div class='d-flex' style='align-items:center'>
                                  <div><img src='{$row_post["user_image"]}' class='user-avatar' alt='{$username}'></div>
                                  <div><a href='../user/my_journal.php?username={$row_post["username"]}' class='mx-2 journal-username'>{$row_post["username"]}</a></div>
                                </div>
                                <div>
                                  <a href='../user/my_journal.php?username={$row_post["username"]}' class='btn btn-outline-success'>Explore</a>
                                </div>
                              </div>
                            </div>
                          <img src='{$row_post["image"]}' class='post-img' alt='img'>
                            <div class='card-body'>
                              <h5 class='card-title'><i class='fa-solid fa-plane fa-margin'></i> {$row_post["destination"]}</h5>
                              <p class='card-text'><q>{$row_post["text"]}</q></p>
                            </div>
                            <div class='card-footer p-1' style='text-align:center'>
                              <small class='text-body-secondary'>{$timing}</small>
                            </div>
                          </div>
                        </div>";

  }
}

if (isset($_POST["submit"])) {
  $search_entry = cleanInput($_POST["search"]);
  header("refresh: 0; url= 'search_results_journal.php?search={$search_entry}'");
}

?>
<?php include "../components/header.php" ?>

  <div class="d-flex jcsb" style="align-items: center;">
    <div>
      <h2>Discover Roamers <img src="../images/Roamio-icon.png" class="mb-1" width="80px" alt=""></h2>
    </div>
    <div>
      <form class="d-flex" method="post">
        <input class="form-control me-2" type="text" placeholder="user / destination..." name="search">
        <input type="submit" class="btn btn-outline-success" value="Search" name="submit">
      </form>
    </div>
  </div>
  <div><a href="../user/discover_journal.php" class="btn btn-primary mb-3"><i class="fa-solid fa-arrow-left"></i> All roamers</a></div>
  <div class="details-trip-div">
    <h5 class="subtitle mb-3">Results for: <?=$search_entry?></h5>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?= $display_post ?>
    </div>
  </div>
<?php include "../components/footer.php" ?>
