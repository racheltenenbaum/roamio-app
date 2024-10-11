<?php

session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
  header("Location: ../registration/login.php");
  exit();
}

require_once "../components/connection.php";

if (isset($_SESSION["user"])) {
  $user_id = $_SESSION["user"];
} elseif (isset($_SESSION["user_selected"])){
  $user_id = $_SESSION["user_selected"];
} else {
  $user_id = $_POST["user_id"];
  $_SESSION["user_selected"] = $user_id;
}

$sql = "SELECT * FROM user WHERE id = $user_id";
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);
$error = false;

$usernameError = "";
$first_nameError = "";
$last_nameError = "";
$emailError = "";
$descriptionError = "";
$imageError = "";

if (isset($_POST["update"])) {
  $username = cleanInput($_POST["username"]);
  $first_name = cleanInput($_POST["first_name"]);
  $last_name = cleanInput($_POST["last_name"]);
  $email = cleanInput($_POST["email"]);
  $description = cleanInput($_POST["description"]);
  if (empty($_POST["image"])) {
    $image = $row["image"];
  } else {
    $image = $_POST["image"];
  }
  $_SESSION["user_selected"] = $user_id;

  if (empty($username)) {
    $error = true;
    $usernameError = "Username can't be empty!";
  } elseif (strlen($username) < 8) {
    $error = true;
    $usernameError = "Username can't be less than 8 characters";
  } elseif (!preg_match("/^(?=.{8,20}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/", $username)) {
    $error = true;
    $usernameError = "Invalid username";
  } else {
    $usernameInUse = "SELECT * FROM user where username = '$username'";
    $result_username = mysqli_query($conn, $usernameInUse);
    $row_username = mysqli_fetch_assoc($result_username);
    if (mysqli_num_rows($result_username) != 0 && $row_username["id"] != $user_id) {
      $error = true;
      $usernameError = "This username is already in use!";
    }
  }

  if (empty($first_name)) {
    $error = true;
    $first_nameError = "first name can't be empty!";
  } elseif (strlen($first_name) < 2) {
    $error = true;
    $first_nameError = "first name can't be less than 2 characters";
  } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
    $error = true;
    $first_nameError = "first name can only include letters and spaces";
  }

  if (empty($last_name)) {
    $error = true;
    $last_nameError = "last name can't be empty!";
  } elseif (strlen($last_name) < 2) {
    $error = true;
    $last_nameError = "last name can't be less than 2 characters";
  } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
    $error = true;
    $last_nameError = "last name can only include letters and spaces";
  }

  if (empty($description)) {
    $error = true;
    $descriptionError = "description can't be empty!";
  }

  if (!$error) {
    $sql_update = "UPDATE `user` SET `username`='{$username}',`first_name`='{$first_name}',`last_name`='{$last_name}',`email`='{$email}',`image`='{$image}',`description`='{$description}' WHERE id = $user_id";

    $result_update = mysqli_query($conn, $sql_update);

    if ($result_update) {
      echo "<div class='alert alert-success' role='alert'>
                {$username} has been updated successfully!
              </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
                Something went wrong, please try again later!
              </div>";
    }

    header("refresh: 1; url= '../user/details_user.php'");
  }
}

?>

<?php include "../components/header.php" ?>

  <h2>Edit Profile</h2>

  <div class="center-div form-div">
    <div class="mb-3">
      <img src="<?= $row["image"] ?>" width='100%' alt="<?= $row["first_name"] ?>">
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="post">
      <input type="text" class="form-format" placeholder="Username" name="username" value="<?= $row["username"] ?>">
      <p class="error-message"><?= $usernameError ?></p>
      <input type="text" class="form-format" placeholder="First name" name="first_name" value="<?= $row["first_name"] ?>">
      <p class="error-message"><?= $first_nameError ?></p>
      <input type="text" class="form-format" placeholder="Last name" name="last_name" value="<?= $row["last_name"] ?>">
      <p class="error-message"><?= $last_nameError ?></p>
      <input type="email" class="form-format" placeholder="Email" name="email" value="<?= $row["email"] ?>">
      <p class="error-message"><?= $emailError ?></p>
      <input type="text" class="form-format" placeholder="Description" name="description" value="<?= $row["description"] ?>">
      <p class="error-message"><?= $descriptionError ?></p>
      <label for="image" class="mb-2">Replace profile image:</label><br>
      <input id="uploader-preview-here-3536" class="simple-file-upload" name="image" type="hidden" data-maxFileSize="10" data-accepted="image/*" data-width="200" data-height="200">
      <div class="form-button-right mb-3">
      <input type="submit" class="btn btn-success" value="Update" name="update">
      </div>
    </form>
  </div>


<?php include "../components/footer.php" ?>
