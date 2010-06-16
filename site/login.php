<?php
include('config.php');

$tp = new TypePad();
$tp->userSession()->doLogin();

?><?php
   // FYI: HTML is forbidden in this file, since the redirect's headers cannot be written!!
?>
