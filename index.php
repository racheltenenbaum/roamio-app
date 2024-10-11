<?php

session_start();

require_once "components/connection.php";

if (isset($_SESSION["admin"])) {
  header("Location: admin/dashboard_admin.php");
  exit();
}

if (isset($_SESSION["user"])) {
  header("Location: user/home_user.php");
  exit();
}

?>


<?php include "components/header.php" ?>

<h1 class="mt-5 mb-3">Roamio</h1>
<div class="animation-index-img">
  <img src="images/Roamio-icon.png" alt="roamio">
</div>
<div class="d-flex jcsb animation-index-div px-4">
  <div class="animation-index-text d-flex" style="flex-direction:column; align-items:center;">
    <div>
      <h2>Travel. Customize. Post. Repeat. </h2>
    </div>
    <a href="about.php" class="btn btn-success about-btn">What's Roamio about?</a>
  </div>
  <div class="animation-index-img-2">
    <img src="images/Roamio-icon.png" alt="roamio">
  </div>
</div>
<?php include "components/footer.php" ?>
