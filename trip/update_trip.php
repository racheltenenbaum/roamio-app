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

$trip_id = $_SESSION["trip_id"];

$sql = "SELECT * FROM trip WHERE id = $trip_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$start_date = $row["start_date"];
$end_date = $row["end_date"];

$user_id = $_SESSION["user"];

$error = false;
$start_date_error = "";
$end_date_error = "";

if (isset($_POST["submit"])) {
  $start_date = $_POST["start_date"];
  $end_date = $_POST["end_date"];

  if (empty($start_date)) {
    $error = true;
    $start_date_error = "Please select a start date";
  } elseif (strtotime($start_date) < strtotime('now')) {
    $error = true;
    $start_date_error = "Start date cannot be in the past";
  }

  if (empty($end_date)) {
    $error = true;
    $end_date_error = "Please select an end date";
  } elseif (strtotime($end_date) < strtotime($start_date)) {
    $error = true;
    $end_date_error = "End date cannot be before start date";
  }

  if (!$error) {
    $sql = "UPDATE `trip` SET `start_date`='$start_date',`end_date`='$end_date' WHERE id = $trip_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
      echo "<div class='alert alert-success' role='alert'>
              Your trip has been udpated successfully!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

    header("refresh: 1; url= 'details_trip.php");

  }

}

?>

<?php include "../components/header.php" ?>

  <h2 class="my-3">Edit Your Trip Dates</h2>
  <div class="center-div form-div">
    <form method="post">
      <label for="start_date">Start date:</label>
      <input type="date" class="form-format" placeholder="Start Date" name="start_date" value="<?= $start_date ?>">
      <p class="error-msg"><?= $start_date_error ?></p>
      <label for="end_date">End date:</label>
      <input type="date" class="form-format" placeholder="End Date" name="end_date" value="<?= $end_date ?>">
      <p class="error-msg"><?= $end_date_error ?></p>
      <div class="form-button-right mb-3">
        <input type="submit" class="btn btn-success" value="Update" name="submit">
      </div>
    </form>
  </div>


<?php include "../components/footer.php" ?>
