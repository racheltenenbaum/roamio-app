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

if (isset($_SESSION["trip_id"])) {
  unset($_SESSION["trip_id"]);
}

$sql = "SELECT trip.start_date as start_date,
        trip.end_date as end_date,
        destination.name as destination,
        destination.country as destination_country,
        destination.image as image,
        trip.id as id
        FROM trip
        JOIN destination
        ON trip.destination_id = destination.id
        WHERE user_id = $user_id
        ORDER BY trip.start_date DESC";
$result = mysqli_query($conn, $sql);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$display = "";

if (mysqli_num_rows($result) == 0) {
  $display = "<h5>No trips booked yet</h5>";
} else {
  foreach ($rows as $key => $row) {
    $trip_timing = (strtotime($row["start_date"]) < strtotime('now')) ? ((strtotime($row["end_date"]) < strtotime('now')) ? "<span class='tag-grey'>Past</span>": "<span class='tag-green'>Ongoing</span>") : "<span class='tag-purple'>Upcoming</span>";
    $posting_option = (strtotime($row["start_date"]) < strtotime('now')) ?
                        "<form method='POST' action='../post/create_post.php'>
                          <input type='hidden' value='{$row['id']}' name='index_trip_id'>
                          <button type='submit' class='btn btn-primary' name='submit'><i class='fa-regular fa-pen-to-square'></i> Post</button>
                        </form>"
                        :"";
    $start_date = date('d.m.y', strtotime($row["start_date"]));
    $end_date = date('d.m.y', strtotime($row["end_date"]));
    $display .= "<div class='modal fade' id='{$row["id"]}' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header' style='align-items:center'>
            <text class='delete-modal-title'>Wait a second!</text>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class='modal-body'>
            <text style='color:#f66f4a'>Are you sure you want to delete this trip to {$row["destination"]}?</text><br>
            <small style='color:#2e4c40'>(All posts associated with this trip will also be deleted)</small>
          </div>
          <div class='modal-footer'>
            <form method='POST' action='../trip/delete_trip.php'>
              <input type='hidden' value='{$row['id']}' name='index_trip_id'>
              <button type='submit' class='btn btn-secondary' data-bs-dismiss='#{$row["id"]}'>Yes, delete</button>
            </form>
            <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <div><div class='card mb-3'>
                    <div>
                      <img src='../images/{$row["image"]}' class='card-img-top'>
                    </div>
                      <div class='card-body'>
                        <div class='d-flex justify-content-between mb-3'>
                          <div class='my-2'>$trip_timing</div>
                          <div>$posting_option</div>
                        </div>
                          <h5>{$row["destination"]}, {$row["destination_country"]}</h5>
                      <div>
                          <p>Start date: {$start_date}</p>
                          <p>End date: {$end_date}</p>
                      </div>
                      <div class='d-flex jcsb'>
                        <form method='POST' action='../trip/details_trip.php'>
                          <input type='hidden' value='{$row['id']}' name='index_trip_id'>
                          <input type='submit' class='btn btn-outline-success' name='submit' value='Details'>
                        </form>
                        <button type='button' class='btn btn-secondary' data-bs-toggle='modal' data-bs-target='#{$row["id"]}'><i class='fa-regular fa-trash-can'></i></button>
                      </div>
                    </div>
                  </div>
                </div>";
  }
}


?>

<?php include "../components/header.php" ?>

  <div class="d-flex" style="justify-content: space-between; align-items:center">
    <h2>My Trips</h2>
    <div>
      <a href="../trip/create_trip.php" class="btn btn-success">New Trip</a>
    </div>
  </div>
  <div class="details-trip-div">
  <div class="row row-cols-1 row-cols-md-3 g-4">
  <?= $display ?>
    </div>
  </div>

<?php include "../components/footer.php" ?>
