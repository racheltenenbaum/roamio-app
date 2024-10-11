<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../index.php");
  exit();
}
if (isset($_SESSION["user"])) {
  header("Location: ../user/home_user.php");
  exit();
}

require_once "../components/connection.php";
require_once "../components/file_upload.php";

$destination_id = $_SESSION["destination_id"];

$sql_dest = "SELECT * FROM destination WHERE id = $destination_id";
$result_dest = mysqli_query($conn, $sql_dest);
$row_dest = mysqli_fetch_assoc($result_dest);

$destination_name = $row_dest["name"].", ".$row_dest["country"];

$sql_activ = "SELECT * FROM activity";
$result_activ = mysqli_query($conn, $sql_activ);
$rows_activ = mysqli_fetch_all($result_activ, MYSQLI_ASSOC);

$activities = "";

foreach ($rows_activ as $key => $row) {
  $activities .= "<div><input type='checkbox' name='activ_selected[]' value='{$row['id']}'>
  <label for='activ_selected'>{$row["name"]}</label><div>";
}

$error = false;
$activ_error = "";

if (isset($_POST["submit"])) {
  $activ_selected = $_POST["activ_selected"];

  if (empty($activ_selected)) {
    $error = true;
    $activ_error = "Please select activities";
  } elseif (count($activ_selected) < 3) {
    $error = true;
    $activ_error = "Please select at least 3 activities";
  }

  if (!$error) {
    foreach ($activ_selected as $key => $activity) {
      $sql = "INSERT INTO `destination_activity`(`destination_id`, `activity_id`) VALUES ('$destination_id','$activity')";
      $result = mysqli_query($conn, $sql);
    }

    if ($result) {
      echo "<div class='alert alert-success' role='alert'>
              Activities have been added to destination successfully!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

    header("refresh: 2; url= 'index_destinations_admin.php");
  }
}

?>

<?php include "../components/header.php" ?>

<div class="container my-4">
<div class="d-flex my-3" style="justify-content: space-between; align-items:center">
  <div>
    <h2>Add Activities to Destination for: <?=$destination_name?></h2>
  </div>
  <div>
    <a href="../admin/create_activity.php" class="btn btn-danger">Add new activity</a>
  </div>
</div>
  <form method="post">
    <?=$activities?>
    <p class="error-msg"><?= $activ_error ?></p>
    <input type="submit" class="btn btn-success my-3" value="Add" name="submit">
  </form>
</div>


<?php include "../components/footer.php" ?>
