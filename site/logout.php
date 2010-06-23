<?php
include('config.php');

$tp = new TypePad();
$tp->userSession()->doLogout(true);

# no HTML below this closing bracket, please!
?>