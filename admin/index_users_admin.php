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

if (isset($_SESSION["user_selected"])) {
  unset($_SESSION["user_selected"]);
}

$sql = "SELECT * FROM user";
$result = mysqli_query($conn, $sql);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$display = "";

foreach ($rows as $key => $row) {
  $display .= "<div class='card m-3' style='width:300px'>
                <div>
                  <img src='{$row["image"]}' class='user-index-image'>
                </div>
                <div class='card-body'>
                  <div>
                    <p class='card-text'>Username: {$row["username"]}</p>
                    <p class='card-text'>Name: {$row["first_name"]} {$row["last_name"]}</p>
                    <p class='list-group-item'>Email: {$row["email"]}</p>
                  </div>
                   <div>
                        <form method='POST' action='../user/update_user.php'>
                            <input type='hidden' value='{$row["id"]}' name='user_id'>
                          <input class='btn btn-info' type='submit' value='Edit User'>
                        </form>
                        <form method='POST' action='../user/delete_user.php'>
                            <input type='hidden' value='{$row["id"]}' name='user_id'>
                          <input class='btn btn-warning my-3' type='submit' value='Delete User'>
                        </form>
                  </div>
                </div>
              </div>";
}

?>

<?php include "../components/header.php" ?>

<div class="mt-5 mb-3">
  <h2>All Users</h2>
</div>

<!-- include ajax search bar -->

<div class="container" style="width: 100; margin-top: 5vh;">
  <div class="d-flex">
    <?= $display ?>
  </div>
</div>

<?php include "../components/footer.php" ?>
