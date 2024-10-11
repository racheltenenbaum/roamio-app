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

if (isset($_SESSION["trip_id"])) {
  unset($_SESSION["trip_id"]);
}

$user_id = $_SESSION["user"];

$destination_sql = "SELECT * FROM destination WHERE name = 'London' OR name = 'Shanghai'";
$destination_result = mysqli_query($conn, $destination_sql);
$destination_rows = mysqli_fetch_all($destination_result, MYSQLI_ASSOC);

$destinations = "";

foreach ($destination_rows as $key => $row) {
  $destinations .= "<option value='{$row["id"]}'>{$row["name"]}, {$row["country"]}</option>";
}

$error = false;
$destination_error = "";
$start_date_error = "";
$end_date_error = "";
$option = "<option selected>Select destination</option>";

if (isset($_POST["submit"])) {
  $destination_id = $_POST["destination"];
  $start_date = $_POST["start_date"];
  $end_date = $_POST["end_date"];

  if (empty($destination_id)) {
    $error = true;
    $destination_error = "Please select a destination";
  } else {
    $selected_dest_sql = "SELECT * FROM destination WHERE id = $destination_id";
    $selected_dest_result = mysqli_query($conn, $selected_dest_sql);
    $selected_dest_row = mysqli_fetch_assoc($selected_dest_result);
    $option = "<option value='{$selected_dest_row["id"]}'>{$selected_dest_row["name"]}, {$selected_dest_row["country"]}</option>";
  }

  if (empty($start_date)) {
    $error = true;
    $start_date_error = "Please select a start date";
  } elseif (strtotime($start_date) < strtotime('now')) {
    $error = true;
    $start_date_error = "Start date must be in the future";
  }

  if (empty($end_date)) {
    $error = true;
    $end_date_error = "Please select an end date";
  } elseif (strtotime($end_date) < strtotime($start_date)) {
    $error = true;
    $end_date_error = "End date cannot be before start date";
  }

  if (!$error) {
    $sql = "INSERT INTO `trip`(`start_date`, `end_date`, `destination_id`, `user_id`) VALUES
    ('$start_date','$end_date','$destination_id','$user_id')";
    $result = mysqli_query($conn, $sql);

    $sql_new_trip = "SELECT * FROM trip WHERE id = (SELECT MAX(id) FROM trip)";
    $result_new_trip = mysqli_query($conn, $sql_new_trip);
    $row_new_trip = mysqli_fetch_assoc($result_new_trip);
    $_SESSION["trip_id"] = $row_new_trip["id"];

    if ($result) {
      echo "<div class='alert alert-success' role='alert'>
              Your new trip has been created successfully!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

    header("url= 'customize_trip.php");

  }

}

?>

<?php include "../components/header.php" ?>

  <h2>Create a New Trip</h2>
  <div class="center-div form-div">
    <form method="post">
      <label for="destination">Select destination:</label>
      <select class="form-format" name="destination">
        <?=$option?>
        <?= $destinations ?>
      </select>
      <small>(we are working on more destinations - please bear with us!)</small>
      <p class="error-msg"><?= $destination_error ?></p>
      <label for="start_date">Start date:</label>
      <input type="date" class="form-format" placeholder="Start Date" name="start_date" value="<?= $start_date ?>">
      <p class="error-msg"><?= $start_date_error ?></p>
      <label for="end_date">End date:</label>
      <input type="date" class="form-format" placeholder="End Date" name="end_date" value="<?= $end_date ?>">
      <p class="error-msg"><?= $end_date_error ?></p>
      <div class="form-button-right mb-3">
        <input type="submit" class="btn btn-success" value="Create" name="submit">
      </div>
    </form>
  </div>


<?php include "../components/footer.php" ?>
