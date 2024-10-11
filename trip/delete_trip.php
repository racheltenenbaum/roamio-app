<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
    header("Location: ../registration/login.php");
    exit();
}

if (isset($_SESSION["admin"])) {
    header("Location: ../admin/dashboard_admin.php");
    exit();
}

require_once "../components/connection.php";

if (isset($_SESSION["trip_id"])) {
    $trip_id = $_SESSION["trip_id"];
} else {
    $trip_id = $_POST["index_trip_id"];
    $_SESSION["trip_id"] = $trip_id;
}

$trip_id = $_SESSION["trip_id"];
$sql_condition = "SELECT * FROM `trip` WHERE id = $trip_id";
$result = mysqli_query($conn, $sql_condition);

$sql_delete = "DELETE FROM `trip` WHERE id = $trip_id";

$display = "";

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $result_deletion = mysqli_query($conn, $sql_delete);

    if ($result_deletion) {
        $display .= "<div class='alert alert-success' role='alert'>
              Your trip has been successfully deleted
              </div>";
    } else {
        $display .= "<div class='alert alert-danger' role='alert'>
              Something went wrong, please try again later!
              </div>";
    }
} else {
    $display .= "<div class='alert alert-danger' role='alert'>Sorry, no trip found. </div>";
}


header("refresh: 2; url= '../user/index_trips_user.php'");
?>

<?php include "../components/header.php" ?>

    <?= $display ?>

<?php include "../components/footer.php" ?>
