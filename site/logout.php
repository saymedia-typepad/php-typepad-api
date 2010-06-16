<?php
include('config.php');

$tp = new TypePad();
$tp->userSession()->doLogout(true);

?>