<?php

include('config.php');
include('blog_config.php');
$tp = new TypePad();

$error = false;
$deleted = 0;
if (array_key_exists('id', $_POST)) {
    try {
        $result = $tp->assets->delete($_POST['id']);
        $deleted = 1;
    } catch(TPException $e) {
        $error = urlencode($e->getCode() .' '. $e->getMessage());
    }
}
header("Location: index.php?deleted=$deleted" . ($error ? "&error=$error" : ''));
