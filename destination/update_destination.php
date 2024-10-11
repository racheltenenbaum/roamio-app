<?php

session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../registration/login.php");
  exit();
}

if (isset($_SESSION["user"])) {
  header("Location: ../user/home_user.php");
  exit();
}

require_once "../components/connection.php";
require_once "../components/file_upload.php";

$destination_id = $_POST["destination_id"];

$sql = "SELECT * FROM destination WHERE destination.id = $destination_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$name = $row["name"];
$_SESSION["country"] = $row["country"];
$country = $_SESSION["country"];
$image = $row["image"];

require_once "../components/country_list_logic.php";
require_once "../components/country_list.php";

$error = false;
$name_error = "";
$country_error = "";
$image_error = "";

if (isset($_POST["update"])) {
  $name = cleanInput($_POST["name"]);
  $country = $_POST["country"];
  $image = $_FILES["image"];

  if (empty($name)) {
    $error = true;
    $name_error = "Please enter a destination name";
  } elseif (strlen($name) < 2) {
    $error = true;
    $name_error = "Destination name must be at least 2 characters long";
  }

  if (empty($country)) {
    $error = true;
    $country_error = "Please select the destination's country";
  }

  if ($image["error"] == 4) {
    $error = true;
    $image_error = "Please select an image";
  } else {
    $image = fileUpload($_FILES['image']);
  }

  if (!$error) {
    $sql = "INSERT INTO `destination`(`name`, `country`, `image`) VALUES ('$name','$country','$image[0]')";
    $result = mysqli_query($conn, $sql);

    $sql_new_destination = "SELECT * FROM destination WHERE id = (SELECT MAX(id) FROM destination)";
    $result_new_destination = mysqli_query($conn, $sql_new_destination);
    $row_new_destination = mysqli_fetch_assoc($result_new_destination);
    $_SESSION["destination_id"] = $row_new_destination["id"];

    if ($result) {
      echo "<div class='alert alert-success' role='alert'>
              Your new destination has been created successfully!
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

<div class="container">
  <h2 class="my-3">Update Destination</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Name" value="<?=$row["name"]?>">
    <p class="error-msg"><?= $name_error ?></p>
    <?=$country_list?>
    <p class="error-msg"><?= $country_error ?></p>
    <label for="image">Upload destination image:</label>
    <input type="file" name="image">
    <p class="error-msg"><?= $image_error ?></p>
    <input type="submit" class="btn btn-success my-3" value="Create" name="update">
  </form>
</div>


<?php include "../components/footer.php" ?>
