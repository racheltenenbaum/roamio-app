<?php

session_start();

if (isset($_SESSION["user"])) {
  header("Location: ../user/home_user.php");
  exit();
}

require_once "../components/connection.php";

$error = false;

$username = "";
$first_name = "";
$last_name = "";
$email = "";
$image = "";
$password = "";

$usernameError = "";
$first_nameError = "";
$last_nameError = "";
$emailError = "";
$imageError = "";
$passwordError = "";

if (isset($_POST["register"])) {

  $username = cleanInput($_POST["username"]);
  $first_name = cleanInput($_POST["first_name"]);
  $last_name = cleanInput($_POST["last_name"]);
  $email = cleanInput($_POST["email"]);
  $image = $_POST["image"];
  $password = cleanInput($_POST["password"]);

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
    $usernameInUse = "SELECT username FROM user where username = '$username'";
    $result_username = mysqli_query($conn, $usernameInUse);
    if (mysqli_num_rows($result_username) != 0) {
      $error = true;
      $usernameError = "This username is already in use!";
    }
  }

  if (empty($first_name)) {
    $error = true;
    $first_nameError = "First name can't be empty!";
  } elseif (strlen($first_name) < 2) {
    $error = true;
    $first_nameError = "First name can't be less than 2 characters";
  } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
    $error = true;
    $first_nameError = "First name can only include letters and spaces";
  }

  if (empty($last_name)) {
    $error = true;
    $last_nameError = "Last name can't be empty!";
  } elseif (strlen($last_name) < 2) {
    $error = true;
    $last_nameError = "Last name can't be less than 2 characters";
  } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
    $error = true;
    $last_nameError = "Last name can only include letters and spaces";
  }

  if (empty($email)) {
    $error = true;
    $emailError = "Email cannot be empty!";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = true;
    $emailError = "Email not valid!";
  } else {
    $emailInUse = "SELECT email FROM user where email = '$email'";
    $result_email = mysqli_query($conn, $emailInUse);
    if (mysqli_num_rows($result_email) != 0) {
      $error = true;
      $emailError = "This email is already in use!";
    }
  }

  if (empty($password)) {
    $error = true;
    $passwordError = "Password cannot be empty!";
  } elseif (strlen($password) < 6) {
    $error = true;
    $passwordError = "Password cannot be less than 6 characters!";
  }

  if (empty($image)) {
    $error = true;
    $imageError = "Please select an image!";
  }

  if (!$error) {
    $password = hash('sha256', $password);
    $sql = "INSERT INTO `user`(`username`, `first_name`, `last_name`, `email`, `password`, `image`)
              VALUES ('{$username}','{$first_name}','{$last_name}','{$email}','{$password}','{$image}')";
    $result = mysqli_query($conn, $sql);

    if (!isset($_SESSION["admin"])) {
      $sql_session = "SELECT * FROM user WHERE email = '$email'";
      $result_session = mysqli_query($conn, $sql_session);
      $row = mysqli_fetch_assoc($result_session);
      $_SESSION["user"] = $row["id"];
    }

    if ($result) {
      echo "<div class='alert alert-success' role='alert'>
              Your new user has been created successfully!
            </div>";
    } else {
      echo "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
            </div>";
    }

    header("refresh: 1; url= 'login.php");
  }
}
?>

<?php include "../components/header.php" ?>

  <h2>Register</h2>

  <div class="center-div form-div">
    <div class="mb-3">
      <text>-> Already have an account? </text>
      <a href="login.php" class="link-button">Log In</a>
    </div>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="post">
      <input class="form-format" type="text" placeholder="Username (no spaces allowed!)" name="username" value="<?= $username ?>">
        <p class="error-msg"><?= $usernameError ?></p>
      <input class="form-format" type="text" placeholder="First name" name="first_name" value="<?= $first_name ?>">
        <p class="error-msg"><?= $first_nameError ?></p>
      <input class="form-format" type="text" placeholder="Last name" name="last_name" value="<?= $last_name ?>">
        <p class="error-msg"><?= $last_nameError ?></p>
      <input class="form-format" type="email" placeholder="Email" name="email" value="<?= $email ?>">
        <p class="error-msg"><?= $emailError ?></p>
      <input class="form-format" type="password" placeholder="Password" name="password">
        <p class="error-msg"><?= $passwordError ?></p>
      <label for="image" class="mb-2">Profile image:</label><br>
      <input id="uploader-preview-here-3536" class="simple-file-upload" name="image" type="hidden" data-maxFileSize="10" data-accepted="image/*" data-width="200" data-height="200">
      <p class="error-msg"><?=$imageError?></p>
      <div class="form-button-right mb-3">
       <input type="submit" class="btn btn-success" value="Register" name="register">
      </div>
  </form>
</div>

<?php include "../components/footer.php" ?>
