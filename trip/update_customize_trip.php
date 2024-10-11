<?php

session_start();

if (!isset($_SESSION["user"])) {
  header("Location: ../index.php");
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
            destination.id as destination_id
            FROM trip
            JOIN destination
            ON destination.id = trip.destination_id
            WHERE trip.id = $trip_id";
$result_trip = mysqli_query($conn, $sql_trip);
$row_trip = mysqli_fetch_assoc($result_trip);
$destination_id = $row_trip["destination_id"];
$destination = $row_trip["destination"];
$destination_country = $row_trip["destination_country"];
$start_date = $row_trip["start_date"];
$end_date = $row_trip["end_date"];


// to fetch all accommodations
$sql_accom = "SELECT * FROM `accommodation` WHERE `destination_id` = '$destination_id'";
$result_accom = mysqli_query($conn, $sql_accom);
$rows_accom = mysqli_fetch_all($result_accom, MYSQLI_ASSOC);
// to have selected accommodations prechecked
$selected_accom_sql = "SELECT accommodation_id FROM trip_accommodation WHERE trip_id = $trip_id";
$result_selected_accom = mysqli_query($conn, $selected_accom_sql);
$row_selected_accom = mysqli_fetch_assoc($result_selected_accom);
$selected_accom = $row_selected_accom["accommodation_id"];

$display_accom = "";

foreach ($rows_accom as $key => $row_accom) {
  $selected_checkbox_accom = "<input type='checkbox' name='accom_selected[]' class='checkbox' value='{$row_accom['id']}' checked='true'>";
  $not_selected_checkbox_accom = "<input type='checkbox' name='accom_selected[]' class='checkbox' value='{$row_accom['id']}'>";
  $checked_selected_accom = ($selected_accom == $row_accom["id"]) ? $selected_checkbox_accom : $not_selected_checkbox_accom;
  $display_accom .= "<div><div class='card mb-2' style='border:none'>
                       <div>
                          <img src='../images/{$row_accom["image"]}' class='card-img-top' width='1000' alt='{$row_accom["name"]}'>
                        </div>
                      <div class='card-body'>
                        <h4>{$row_accom["name"]}</h4>
                        <p>{$row_accom["description"]}</p>
                        <p>€{$row_accom["price"]} per night</p>
                        <div>
                          {$checked_selected_accom}
                          <label for='rest_selected'>{$row_accom["name"]}</label>
                        </div>
                      </div>
                    </div></div>";
}

$sql_rest = "SELECT * FROM `restaurant` WHERE `destination_id` = '$destination_id'";
$result_rest = mysqli_query($conn, $sql_rest);
$rows_rest = mysqli_fetch_all($result_rest, MYSQLI_ASSOC);

$selected_rest_sql = "SELECT restaurant_id FROM trip_restaurant WHERE trip_id = $trip_id";
$result_selected_rest = mysqli_query($conn, $selected_rest_sql);
$rows_selected_rest = mysqli_fetch_all($result_selected_rest, MYSQLI_ASSOC);
$selected_rest = [];
foreach ($rows_selected_rest as $key => $row_selected_rest) {
  array_push($selected_rest, $row_selected_rest["restaurant_id"]);
}

$display_rest = "";

foreach ($rows_rest as $key => $row_rest) {
  $selected_checkbox_rest = "<input type='checkbox' name='rest_selected[]' class='checkbox' value='{$row_rest['id']}' checked='true'>";
  $not_selected_checkbox_rest = "<input type='checkbox' name='rest_selected[]' class='checkbox' value='{$row_rest['id']}'>";
  $checked_selected_rest = (in_array($row_rest["id"], $selected_rest)) ? $selected_checkbox_rest : $not_selected_checkbox_rest;
  $display_rest .= "<div><div class='card mb-2' style='border:none'>
                       <div>
                          <img src='../images/{$row_rest["image"]}' class='card-img-top' width='1000' alt='{$row_rest["name"]}'>
                        </div>
                      <div class='card-body'>
                        <h4>{$row_rest["name"]}</h4>
                        <p>{$row_rest["description"]}</p>
                        <div>
                          {$checked_selected_rest}
                          <label for='rest_selected'>{$row_rest["name"]}</label>
                        </div>
                      </div>
                    </div></div>";
}

$season = "";
$start_date_month = date("m", strtotime($start_date));
if ($start_date_month >= 6 && $start_date_month < 10) {
  $season = 1;
} elseif ($start_date_month >= 12 || $start_date_month < 4) {
  $season = 2;
} else {
  $season = 3;
}

$season_name_sql = "SELECT name FROM season WHERE id = $season";
$season_name_result = mysqli_query($conn, $season_name_sql);
$season_name_row = mysqli_fetch_assoc($season_name_result);
$season_name = $season_name_row["name"];

$sql_activ = "SELECT activity.id as id,
                activity.name as name,
                activity.description as description,
                activity.image as image
                FROM activity
                JOIN destination_activity
                ON destination_activity.activity_id = activity.id
                JOIN season_activity
                ON season_activity.activity_id = activity.id
                WHERE destination_activity.destination_id = '$destination_id'
                AND season_activity.season_id = '$season'";
$result_activ = mysqli_query($conn, $sql_activ);
$rows_activ = mysqli_fetch_all($result_activ, MYSQLI_ASSOC);

$selected_activ_sql = "SELECT activity_id FROM trip_activity WHERE trip_id = $trip_id";
$result_selected_activ = mysqli_query($conn, $selected_activ_sql);
$rows_selected_activ = mysqli_fetch_all($result_selected_activ, MYSQLI_ASSOC);
$selected_activ = [];
foreach ($rows_selected_activ as $key => $row_selected_activ) {
  array_push($selected_activ, $row_selected_activ["activity_id"]);
}

$display_activ = "";

foreach ($rows_activ as $key => $row_activ) {
  $selected_checkbox_activ = "<input type='checkbox' name='activ_selected[]' class='checkbox' value='{$row_activ['id']}' checked='true'>";
  $not_selected_checkbox_activ = "<input type='checkbox' name='activ_selected[]' class='checkbox' value='{$row_activ['id']}'>";
  $checked_selected_activ = (in_array($row_activ["id"], $selected_activ)) ? $selected_checkbox_activ : $not_selected_checkbox_activ;
  $display_activ .= "<div><div class='card mb-2' style='border:none'>
                       <div>
                          <img src='../images/{$row_activ["image"]}' class='card-img-top' width='1000' alt='{$row_activ["name"]}'>
                        </div>
                      <div class='card-body'>
                        <h4>{$row_activ["name"]}</h4>
                        <p>{$row_activ["description"]}</p>
                        <div>
                            {$checked_selected_activ}
                           <label for='rest_selected'>{$row_activ["name"]}</label>
                        </div>
                      </div>
                    </div></div>";
}

$error = false;

$accommodationError = "";
$restaurantsError = "";
$activitiesError = "";
$mainError = "";

if (isset($_POST["save"])) {

  if (isset($_POST["accom_selected"])) {
    $accommodation = $_POST["accom_selected"];
    if (empty($accommodation)) {
      $error = true;
      $accommodationError = "Please select an accommodation";
    } elseif (count($accommodation) > 1) {
      $error = true;
      $accommodationError = "Only one accommodation can be selected";
    }
  }

  if (isset($_POST["rest_selected"])) {
    $selected_rest_sql = "DELETE FROM `trip_restaurant` WHERE trip_id = $trip_id";
    $result_selected_rest = mysqli_query($conn, $selected_rest_sql);

    $restaurants = $_POST["rest_selected"];

    if (empty($restaurants)) {
      $error = true;
      $restaurantsError = "Please select restaurants";
      } elseif (count($restaurants) < 2) {
        $error = true;
        $restaurantsError = "Please select at least 2 restaurants";
      }

    }

  if (isset($_POST["activ_selected"])) {
    $selected_rest_sql = "DELETE FROM `trip_activity` WHERE trip_id = $trip_id";
    $result_selected_rest = mysqli_query($conn, $selected_rest_sql);

    $activities = $_POST["activ_selected"];

    if (empty($activities)) {
      $error = true;
      $activitiesError = "Please select activities";
      } elseif (count($activities) < 3) {
        $error = true;
        $activitiesError = "Please select at least 3 activities";
      }
    }


  if ($error) {
    $mainError = "Something has been selected incorrectly";
  }

  if (!$error) {

    if (isset($_POST["accom_selected"])) {
      $accommodation_selected = $accommodation[0];
      $sql_add_accom = "UPDATE `trip_accommodation` SET `accommodation_id`='$accommodation_selected' WHERE trip_id = $trip_id";
      $result_add_accom = mysqli_query($conn, $sql_add_accom);
    }

    if (isset($_POST["rest_selected"])) {
      foreach ($restaurants as $key => $restaurant) {
        $sql_add_rest = "INSERT INTO `trip_restaurant`(`restaurant_id`, `trip_id`) VALUES ('$restaurant','$trip_id')";
        $result_add_rest = mysqli_query($conn, $sql_add_rest);
      }
    }

    if (isset($_POST["activ_selected"])) {
      foreach ($activities as $key => $activity) {
        $sql_add_activ = "INSERT INTO `trip_activity`(`activity_id`, `trip_id`) VALUES ('$activity','$trip_id')";
        $result_add_activ = mysqli_query($conn, $sql_add_activ);
      }
    }

    echo "<div class = 'alert alert-success' role='alert'>
           Customization saved!
          </div>";

    header("refresh: 1; url= 'details_trip.php");
  }
}

?>

<?php include "../components/header.php" ?>

  <h2>Customize Your Trip</h2>
  <div class="customize-form-div">
    <p class="main-error-msg"><?=$mainError?></p>
    <form method="post">
      <h5>Accommodations in <?= $destination ?> (please select 1):</h5>
      <p class="error-msg"><?= $accommodationError ?></p>
      <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
        <?= $display_accom ?>
      </div>
      <h5 class="mt-3">Restaurants in <?= $destination ?> (please select at least 2):</h5>
      <p class="error-msg"><?= $restaurantsError ?></p>
      <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
        <?= $display_rest ?>
      </div>
      <h5 class="mt-3">Activities for <?= $season_name ?> weather in <?= $destination ?> (please select at least 3):</h5>
      <p class="error-msg"><?= $activitiesError ?></p>
      <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
        <?= $display_activ ?>
      </div>
      <div class="form-button-right">
        <input type="submit" class="btn btn-success" value="Save Customization" name="save">
      </div>
    </form>
  </div>


<?php include "../components/footer.php" ?>
