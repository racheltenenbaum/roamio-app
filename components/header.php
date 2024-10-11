<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/8ae3362ae0.js" crossorigin="anonymous"></script>
  <script src="https://app.simplefileupload.com/buckets/2162d58c3b587099910e8e32fc8e9ddd.js"></script>
  <link rel="stylesheet" href="../css/style.css">
  <title>Roamio</title>
</head>

<body>
  <div class="flex-wrapper">

  <?php
    if (isset($_SESSION["user"])) {
      include 'navbar_user.php';
    } elseif (isset($_SESSION["admin"])) {
      include 'navbar_admin.php';
    } elseif (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])){
      include 'navbar.php';
    }
  ?>
  <div class="container">
