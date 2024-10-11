<?php

session_start();

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
  header("Location: ../index.php");
  exit();
}
require_once "../components/connection.php";

if (isset($_SESSION["user"])) {
  $user_id = $_SESSION["user"];
} else {
  $user_id = $_SESSION["user_selected"];
}

$sql = "SELECT * FROM user WHERE id = $user_id";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

$display = "";

$display .= "<div class='center-div'><div class='card mb-3'>
                  <div class='row no-gutters'>
                    <div class='col-lg-5'>
                      <img src='{$row["image"]}' class='card-img img-fluid rounded-start horizontal-card-img' alt='{$row["username"]}'>
                    </div>
                    <div class='col-lg-7' style='align-items:center'>
                      <div class='card-body'>
                        <div class='d-flex jcsb'>
                          <h5 class='card-title'>{$row["username"]}</h5>
                          <div class='d-flex'>
                            <div>
                              <form method='POST' action='../user/update_user.php'>
                                <input type='hidden' value='{$user_id}' name='user_id'>
                                <button type='submit' class='btn btn-outline-success' name='submit'><i class='fa-solid fa-pen-to-square'></i></button>
                              </form>
                            </div>
                          </div>
                        </div>
                        <p class='card-text mt-3'>First name: {$row["first_name"]}</p>
                        <p class='card-text mt-3'>Last name: {$row["last_name"]}</p>
                        <p class='card-text'>Email: {$row["email"]}</p>
                        <p class='card-text'>Description: {$row["description"]}</p>
                      </div>
                    </div>
                  </div>
                </div></div>";

?>

<?php include "../components/header.php" ?>

  <h2>Profile</h2>
  <div class="details-trip-div">
    <?= $display ?>
  </div>


<?php include "../components/footer.php" ?>
