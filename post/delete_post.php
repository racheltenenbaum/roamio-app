<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
    header("Location: ../registration/login.php");
    exit();
}

require_once "../components/connection.php";

if (isset($_POST["post_id"])) {
  $_SESSION["post_id"] = $_POST["post_id"];
  $post_id = $_SESSION["post_id"];
} else {
  $post_id = $_SESSION["post_id"];
}

$sql_condition = "SELECT * FROM `post` WHERE id = $post_id";
$result = mysqli_query($conn, $sql_condition);

$sql_delete = "DELETE FROM `post` WHERE id = $post_id";

$display = "";

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $result_deletion = mysqli_query($conn, $sql_delete);

    if ($result_deletion) {
        $display .= "<div class='alert alert-success' role='alert'>
              Your post has been successfully deleted
              </div>";
    } else {
        $display .= "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
              </div>";
    }
} else {
    $display .= "<div class='alert alert-danger' role='alert'>Sorry, no post found </div>";
}


header("refresh: 2; url= '../user/journal_user.php'");
?>

<?php include "../components/header.php" ?>

<div class="container">
    <?= $display ?>
</div>

<?php include "../components/footer.php" ?>
