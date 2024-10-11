<?php

session_start();

if (isset($_SESSION["user"])) {
  header("Location: ../user/home_user.php");
  exit();
}

if (isset($_SESSION["admin"])) {
  header("Location: ../admin/dashboard_admin.php");
  exit();
}

require_once "../components/connection.php";

$errorEmail = "";
$errorPassword = "";

$email = "";

if (isset($_POST["login"])) {

  $email = cleanInput($_POST["email"]);
  $password = cleanInput($_POST["password"]);

  $password = hash("sha256", $password);

  $sql = "SELECT * FROM user WHERE email = '$email'";
  $result_email = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result_email) == 1) {
    $row = mysqli_fetch_assoc($result_email);
    $_SESSION["username"] = $row["username"];
    $_SESSION["image"] = $row["image"];

    if ($password == $row["password"]) {
      if ($row["role"] == "admin") {
        $_SESSION["admin"] = $row["id"];
      } else {
        $_SESSION["user"] = $row["id"];
      }
      echo "<div class='alert alert-success' role='alert'>Welcome, {$row["username"]}!</div>";
      header("refresh: 1; url= '../user/home_user.php");
    } else {
      $errorPassword = "Password incorrect. Try again.";
    }
  } else {
    $errorEmail = "This email is not registered yet.";
  }
}
?>

<?php include "../components/header.php" ?>

<h2>Log In</h2>
<div class="center-div form-div">
  <form method="post" >
    <input class="form-format" type="text" placeholder="Email" name="email" value="<?= $email ?>">
    <p class="error-msg"><?= $errorEmail ?></p>
    <input placeholder="Password" class="form-format" type="password" name="password">
    <p class="error-msg"><?= $errorPassword ?></p>
    <div class="form-button-right mb-3">
      <input type="submit" class="btn btn-success" value="Log In" name="login">
    </div>
    <text class="mt-4">-> Don't have an account?</text>
    <a href="register.php" class="link-button">  Register Here</a>
  </form>
</div>


<?php include "../components/footer.php" ?>
