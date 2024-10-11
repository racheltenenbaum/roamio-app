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

if (isset($_SESSION["trip_id"])) {
  $trip_id = $_SESSION["trip_id"];
} else {
  $trip_id = $_POST["index_trip_id"];
  $_SESSION["trip_id"] = $trip_id;
}

$sql_trip = "SELECT trip.start_date as start_date,
        trip.end_date as end_date,
        destination.name as destination,
        destination.country as destination_country,
        destination.image as image,
        trip.id as id
        FROM trip
        JOIN destination
        ON trip.destination_id = destination.id
        WHERE trip.id = $trip_id";
$result_trip = mysqli_query($conn, $sql_trip);
$row_trip = mysqli_fetch_assoc($result_trip);
$destination = $row_trip["destination"];
$start_date = $row_trip["start_date"];
$formatted_s_date = date_format(date_create($start_date),'D, d.m.y');
$end_date = $row_trip["end_date"];
$formatted_e_date = date_format(date_create($end_date),'D, d.m.y');
$destination_country = $row_trip["destination_country"];
$destination_image = $row_trip["image"];

$update_buttons = "<a href='update_trip.php' class='btn btn-outline-success'><i class='fa-regular fa-calendar-days fa-margin'></i> Reschedule Trip Dates</a><a href='update_customize_trip.php' class='btn btn-success my-3'><i class='fa-solid fa-pen-to-square fa-margin'></i> Edit Customized Selection</a>
";
if (strtotime($end_date) < strtotime('now')) {
  $update_buttons = "";
}

// Customization selection display

$display_trip_accom = "";
$display_trip_rest = "";
$display_trip_activ = "";

$sql_trip_accom = "SELECT
    accommodation.name as accom_name,
    accommodation.description as accom_desc,
    accommodation.image as accom_image,
    accommodation.price as accom_price,
    accommodation.rating as accom_rating
    FROM accommodation
    JOIN trip_accommodation
    ON trip_accommodation.accommodation_id = accommodation.id
    WHERE trip_accommodation.trip_id = $trip_id";
$result_trip_accom = mysqli_query($conn, $sql_trip_accom);
$row_trip_accom = mysqli_fetch_assoc($result_trip_accom);

$display_trip_accom .= "<div><div class='card' style='width:70%;flex:1'>
                        <div class='row g-0'>
                          <div class='col-md-6'>
                            <img src='../images/{$row_trip_accom["accom_image"]}' class='img-fluid rounded-start horizontal-card-img' alt='{$row_trip_accom["accom_name"]}'>
                          </div>
                          <div class='col-md-6'>
                            <div class='card-body'>
                              <h5 class='card-title'>{$row_trip_accom["accom_name"]}</h5>
                              <p class='card-text mt-3'>{$row_trip_accom["accom_desc"]}</p>
                              <div>
                                <p class='card-text mt-3'>€{$row_trip_accom["accom_price"]} per night</p>
                                <p class='card-text mt-3'>Rated {$row_trip_accom["accom_rating"]} / 10</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div></div>";


$sql_trip_rest = "SELECT
    restaurant.name as rest_name,
    restaurant.description as rest_desc,
    restaurant.image as rest_image
    FROM restaurant
    JOIN trip_restaurant
    ON trip_restaurant.restaurant_id = restaurant.id
    WHERE trip_restaurant.trip_id = $trip_id";
$result_trip_rest = mysqli_query($conn, $sql_trip_rest);
$rows_trip_rest = mysqli_fetch_all($result_trip_rest, MYSQLI_ASSOC);

foreach ($rows_trip_rest as $key => $row_trip_rest) {
  $display_trip_rest .= "<div><div class='card mb-3'>
                  <img src='../images/{$row_trip_rest["rest_image"]}' class='card-img img-fluid rounded-start' alt='{$row_trip_rest["rest_name"]}'>
                  <div class='card-body'>
                    <h5 class='card-title'>{$row_trip_rest["rest_name"]}</h5>
                    <p class='card-text mt-3'>{$row_trip_rest["rest_desc"]}</p>
                    </div>
                  </div>
                </div>";
}

$sql_trip_activ = "SELECT
    activity.name as activ_name,
    activity.description as activ_desc,
    activity.image as activ_image
    FROM activity
    JOIN trip_activity
    ON trip_activity.activity_id = activity.id
    WHERE trip_activity.trip_id = $trip_id";
$result_trip_activ = mysqli_query($conn, $sql_trip_activ);
$rows_trip_activ = mysqli_fetch_all($result_trip_activ, MYSQLI_ASSOC);

foreach ($rows_trip_activ as $key => $row_trip_activ) {
  $display_trip_activ .= "<div><div class='card mb-3'>
                  <img src='../images/{$row_trip_activ["activ_image"]}' class='card-img img-fluid rounded-start' alt='{$row_trip_activ["activ_name"]}'>
                  <div class='card-body'>
                    <h5 class='card-title'>{$row_trip_activ["activ_name"]}</h5>
                    <p class='card-text mt-3'>{$row_trip_activ["activ_desc"]}</p>
                    </div>
                  </div>
                </div>";
}

// Daily itinerary setup

$trip_length_interval = date_diff(date_create($start_date), date_create($end_date));
$trip_length_integer = $trip_length_interval->format('%d');

$display_day = "";

for ($i = 1; $i < $trip_length_integer + 1; $i++) {
  // randomizing restaurants and preventing duplication
  // necessary to have at least 2 selected!
  $ran_rest_array_lunch = array_rand($rows_trip_rest);
  $ran_rest_lunch = $rows_trip_rest[$ran_rest_array_lunch]["rest_name"];
  $ran_rest_array_dinner = array_rand($rows_trip_rest);
  while ($ran_rest_array_dinner == $ran_rest_array_lunch) {
    $ran_rest_array_dinner = array_rand($rows_trip_rest);
  }
  $ran_rest_dinner = $rows_trip_rest[$ran_rest_array_dinner]["rest_name"];

  // randomizing activities and preventing duplication
  // necessary to have at least 3 selected!
  $ran_activ_array_1 = array_rand($rows_trip_activ);
  $ran_activ_1 = $rows_trip_activ[$ran_activ_array_1]["activ_name"];
  while ($ran_activ_1 == "Sunset at Oia") {
    $ran_activ_array_1 = array_rand($rows_trip_activ);
    $ran_activ_1 = $rows_trip_activ[$ran_activ_array_1]["activ_name"];
  }
  $ran_activ_array_2 = array_rand($rows_trip_activ);
  while ($ran_activ_array_2 == $ran_activ_array_1) {
    $ran_activ_array_2 = array_rand($rows_trip_activ);
  }
  $ran_activ_2 = $rows_trip_activ[$ran_activ_array_2]["activ_name"];
  $ran_activ_array_3 = array_rand($rows_trip_activ);
  while ($ran_activ_array_3 == $ran_activ_array_2 || $ran_activ_array_3 == $ran_activ_array_1) {
    $ran_activ_array_3 = array_rand($rows_trip_activ);
  }
  $ran_activ_3 = $rows_trip_activ[$ran_activ_array_3]["activ_name"];
  $display_day .= "<div>
                    <div class='card mb-3 itin-card' style='text-align:center'>
                      <div>
                        <h5 class='card-title itin-title'>Day {$i}</h5>
                        <h5 class='my-3 itin-subtitle' style='text-align:center'> Morning </h5>
                        <p>Breakfast at hotel buffet (until 10:00)</p>
                        <p>$ran_activ_1</p>
                        <h5 class='itin-subtitle' style='text-align:center'> Afternoon </h5>
                        <p>Lunch at {$ran_rest_lunch}</p>
                        <p>$ran_activ_2</p>
                        <p>$ran_activ_3</p>
                        <h5 class='itin-subtitle' style='text-align:center'> Evening </h5>
                        <p>Dinner at {$ran_rest_dinner}</p>
                        <p class='card-text mt-3'>Sleeping at {$row_trip_accom["accom_name"]}</p>
                      </div>
                    </div>
                  </div>";
    }

?>

<?php include "../components/header.php" ?>

<h2>My Trip</h2>
<div class="details-trip-div">
  <h5 class="subtitle">To <?= $destination ?> from <?= $formatted_s_date ?> until <?= $formatted_e_date ?></h5>
    <div class="d-flex my-4" style="justify-content: space-between;">
      <div>
        <h3 class="mb-3 mid-title">My Accommodation:</h3>
        <div>
          <?= $display_trip_accom ?>
        </div>
      </div>
      <div class="d-flex flex-column mt-5" style="align-items:end; width:40%">
        <?=$update_buttons?>
        <button onClick="window.print()" class="btn btn-primary"><i class="fa-solid fa-download fa-margin"></i> Save this Itinerary as a PDF</button>
      </div>
    </div>
    <h3 class="mb-3 mid-title">My Restaurants:</h3>
    <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
      <?= $display_trip_rest ?>
    </div>
    <h3 class="mb-3 mid-title">My Activities:</h3>
    <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
      <?= $display_trip_activ ?>
    </div>
    <h3 class="mb-3 mid-title">My Daily Intinerary:</h3>
    <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
      <?= $display_day ?>
    </div>
  </div>

<?php include "../components/footer.php" ?>
