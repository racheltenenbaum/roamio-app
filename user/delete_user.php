<?php
  session_start();

  if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
    header("Location: ../registration/login.php");
    exit();
  }

  require_once "../components/connection.php";

  if (isset($_SESSION["user"])) {
    $user_id = $_SESSION["user"];
  } else {
    $user_id = $_POST["user_id"];
  }

  if (isset($_SESSION["user"])) {
    $header_location = '../index.php';
  } elseif (isset($_SESSION["admin"])) {
    $header_location = '../admin/index_users.php';
  }

  $sql_condition = "SELECT * FROM `user` WHERE id = $user_id";
  $result = mysqli_query($conn, $sql_condition);

  $sql_delete = "DELETE FROM `user` WHERE id = $user_id";

  $display = "";

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $result_deletion = mysqli_query($conn, $sql_delete);

    if ($result_deletion) {
      $display .= "<div class='alert alert-success' role='alert'>
              {$row["username"]} has been successfully deleted
              </div>";
      if (isset($_SESSION["user"])) {
        session_destroy();
      }
    } else {
      $display .= "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
              </div>";
    }
  } else {
    $display .= "<div class='alert alert-danger' role='alert'>Sorry, there's no such user. </div>";
  }


  header("refresh: 2; url= '{$header_location}'");
?>

<?php include "../components/header.php" ?>

  <div class="container">
    <?= $display ?>
  </div>

 <?php include "../components/footer.php" ?>
