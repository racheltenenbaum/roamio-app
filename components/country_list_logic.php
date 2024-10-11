<?php

if (isset($_SESSION["country"])) {
  $country_selected = $_SESSION["country"];
} elseif (isset($_POST["country"])) {
  $country_selected = $_POST["country"];
}
$selected_country = (isset($country_selected)) ? "<option selected value='{$country_selected}'>{$country_selected}</option>":"<option selected value=''>Select a country</option>";

?>
