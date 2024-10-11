<?php

  function fileUpload($image) {
    // error = 4 when nothing was selected. 0 when a file was uploaded.
    if ($image["error"] == 4) {
      $imageName = "user_icon.png";
      $message = "No image has been selected, but you can upload one later.";
    } else {
      $checkIfimage = getimagesize($image["tmp_name"]);
      $message = $checkIfimage ? "Ok":"error";
    }

    if ($message == "Ok") {
      // rename the file, so data cannot be epxosed via the url - for security reasons
      // we need the extension first, before changing the name
      $ext = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
      $imageName = uniqid("").".".$ext;
      $destination = "../images/{$imageName}";

      move_uploaded_file($image["tmp_name"], $destination);
      return [$imageName, $message];
    } else {
      return [$message];
    }

  }
?>
