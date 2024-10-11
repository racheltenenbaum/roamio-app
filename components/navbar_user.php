<?php

$user_nav = $_SESSION["user"];
$sql_nav = "SELECT * FROM user WHERE id = $user_nav";
$result_nav = mysqli_query($conn, $sql_nav);
$row_nav = mysqli_fetch_assoc($result_nav);

$username_nav = $row_nav["username"];
$image_nav = $row_nav["image"];

?>

<nav class="navbar fixed-top navbar-expand-lg bg-body-tertiary" style="position: sticky;
  top: 0;">
  <div class="container-fluid">
    <a class="navbar-brand navbar-logo" href="../user/home_user.php">
      <img src="../images/Roamio-icon-no-background.png" alt="Logo" width="30" class="d-inline-block align-text-top">
      Roamio
    </a>
    <div class='collapse navbar-collapse' id='navbarSupportedContent'>
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="../user/index_trips_user.php">My Trips</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../trip/create_trip.php">New Trip</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="../user/my_feed.php">Journal Feed</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../user/discover_journal.php">Discover Roamers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../user/my_journal.php?username=<?=$username_nav?>">My Journal</a>
        </li>
      </ul>
      <ul class="navbar-nav mb-auto">
        <a class="nav-user d-flex username-navbar" style="align-items: center;" href="../user/details_user.php">
          <div>
           <img src="<?=$image_nav?>" alt="<?=$username_nav?>" class="user-avatar fa-margin"></div>
            <?=$username_nav?>
          </a>
        <li class="nav-item">
          <a class="nav-link" href="../registration/logout.php?logout">Log Out</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
