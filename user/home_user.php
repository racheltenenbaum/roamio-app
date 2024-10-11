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

$sql_trips = "SELECT trip.start_date as start_date,
              trip.end_date as end_date,
              trip.id as id,
              destination.name as destination,
              destination.image as image
              FROM trip
              JOIN destination
              ON trip.destination_id = destination.id
              WHERE user_id = $user_id AND (trip.start_date > CURRENT_DATE() or trip.end_date > CURRENT_DATE())
              ORDER BY trip.start_date ASC LIMIT 1";
$result_trips = mysqli_query($conn, $sql_trips);
$rows_trips = mysqli_fetch_all($result_trips, MYSQLI_ASSOC);

$display_trips = "";

if (mysqli_num_rows($result_trips) == 0) {
  $new_trip_btn = "";
  $display_trips = "No trips booked. <br><a href='../trip/create_trip.php' class='btn btn-success my-2 p-3'>Plan New Trip</a>";
} else {
  $new_trip_btn = "<a href='../trip/create_trip.php' class='btn btn-orange'>Plan New Trip</a>";
  foreach ($rows_trips as $key => $row_trips) {
    $trip_img = "";
    $trip_timing = (strtotime($row_trips["start_date"]) < strtotime('now')) ? ((strtotime($row_trips["end_date"]) < strtotime('now')) ? "<span class='tag-grey'>Past</span>": "<span class='tag-green'>Ongoing</span>") : "<span class='tag-purple'>Upcoming</span>";
    $start_date = date('d.m.y', strtotime($row_trips["start_date"]));
    $end_date = date('d.m.y', strtotime($row_trips["end_date"]));
    $display_trips .= "<div><div class='card' style='max-width: 540px;'>
                        <div class='row g-0'>
                          <div class='col-md-8'>
                            <img src='../images/{$row_trips["image"]}' class='img-fluid rounded-start' alt='{$row_trips["destination"]}'>
                          </div>
                          <div class='col-md-4'>
                            <div class='card-body'>
                                $trip_timing
                              <div>
                                <div class='mt-3'>
                                  <h4>{$row_trips["destination"]}</h4>
                                </div>
                                <text>Start date: {$start_date}</text><br>
                                <text>End date: {$end_date}</text>
                              </div>
                              <div class='form-button-bottom-right'>
                                <form method='POST' action='../trip/details_trip.php'>
                                  <input type='hidden' value='{$row_trips['id']}' name='index_trip_id'>
                                  <input class='btn btn-outline-success' type='submit' value='Trip Details'>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div></div>";
  }

}

$sql_journal = "SELECT DISTINCT post.image as image,
                post.text as text,
                post.timestamp as timestamp,
                user.username as username,
                user.image as user_image,
                destination.name as destination
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
                ORDER BY timestamp DESC LIMIT 3";
$result_journal = mysqli_query($conn, $sql_journal);
$rows_journal = mysqli_fetch_all($result_journal, MYSQLI_ASSOC);

$display_journal = "";

if (mysqli_num_rows($result_journal) == 0) {
  $more_posts_btn = "";
  $display_journal = "<div>You don't follow anyone yet. <a href='../user/discover_journal.php' class='btn btn-primary my-2 p-3'><img src='../images/Roamio-icon-no-background.png' alt='roamio' width='35px'>&nbsp; Discover Roamers</a></div>";
} else {
  foreach ($rows_journal as $key => $row_journal) {
    $more_posts_btn = "<a href='../user/my_feed.php' class='btn btn-outline-success mt-4' style='width: 100%;'>More posts</a>";
    $timestamp = new DateTime($row_journal["timestamp"]);
    $now = new DateTime('now');
    $diff = $now->diff($timestamp);
    $timing = ($diff->m < 1)?
    (($diff->d < 1)?
    ((($diff->h) < 1)?
    "Posted ".$diff->i." minutes ago":
    "Posted ".$diff->h." hours ".$diff->i." minutes ago"):
    "Posted ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago"):
    "Posted ".$diff->m." months ".$diff->d." days ".$diff->h." hours ".$diff->i." minutes ago";
    $display_journal .= "<div><div class='card'>
                            <div class='card-header' style='background-color:white'>
                              <div class='d-flex' style='justify-content:space-between; align-items:center'>
                                <div class='d-flex' style='align-items:center'>
                                  <div><img src='{$row_journal["user_image"]}' class='user-avatar' alt='{$row_journal["username"]}'></div>
                                  <div><a href='../user/my_journal.php?username={$row_journal["username"]}' class='mx-2 journal-username'>{$row_journal["username"]}</a></div>
                                </div>
                              </div>
                            </div>
                          <img src='{$row_journal["image"]}' class='post-img' alt='img'>
                            <div class='card-body'>
                              <h5 class='card-title'><i class='fa-solid fa-plane fa-margin'></i> {$row_journal["destination"]}</h5>
                              <p class='card-text'><q>{$row_journal["text"]}</q></p>
                            </div>
                            <div class='card-footer p-1' style='text-align:center'>
                              <small class='text-body-secondary'>{$timing}</small>
                            </div>
                          </div>
                        </div>";
  }
}
?>
<?php include "../components/header.php" ?>
    <div>
      <h2 style="text-align: left"><a href="../user/index_trips_user.php">My Trips</a></h2>
    </div>
    <div class="details-trip-div d-flex jcsb">
      <div>
        <?= $display_trips ?>
      </div>
      <div>
        <?= $new_trip_btn ?>
      </div>
    </div>

  <div>
    <h2>Journal Feed</h2>
  </div>
  <div class="details-trip-div">
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?= $display_journal ?>
    </div>
    <?=$more_posts_btn?>
  </div>
<?php include "../components/footer.php" ?>
