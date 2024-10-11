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

  if (isset($_SESSION["country"])) {
    unset($_SESSION["country"]);
  }

  $sql = "SELECT * FROM destination";
  $result = mysqli_query($conn, $sql);
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

  $destination_num = mysqli_num_rows($result);

  $display = "";

  foreach ($rows as $key => $row) {
    $display .= "<div>
          <div class='card m-3' style='width: 18rem;'>
            <img src='../images/{$row["image"]}' class='card-img-top' alt='img'>
            <div class='card-body'>
              <h5 class='card-title'>{$row["name"]}</h5>
              <p class='card-text'>{$row["country"]}</p>
              <div>
              <form method='POST' action='../admin/update_destination.php'>
                            <input type='hidden' value='{$row["id"]}' name='destination_id'>
                          <input class='btn btn-info' type='submit' value='Edit'>
                        </form>
                        <form method='POST' action='../admin/delete_destination.php'>
                            <input type='hidden' value='{$row["id"]}' name='destination_id'>
                          <input class='btn btn-warning my-3' type='submit' value='Delete'>
                        </form></div>
            </div></div></div>";
  }

?>

<?php include "../components/header.php" ?>

<div class="container" style="width: 100; margin-top: 5vh;">
<div class="d-flex" style="justify-content: space-between;">
  <div>
    <h2>All Destinations</h2>
  </div>
  <div>
    <a href="../admin/create_destination.php" class="btn btn-danger">Add new destination</a>
  </div>
</div>

<!-- include ajax search bar -->

<div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
  <?= $display ?>
  </div>
</div>

<?php include "../components/footer.php" ?>
