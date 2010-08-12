<?php

include('config.php');
include('blog_config.php');
$tp = new TypePad();

if ($_POST['id']) {
    $result = $tp->assets->put(array(
        'id' => $_POST['id'],
        'payload' => array(
            'title' => $_POST['title'],
            'content' => $_POST['content']
        )
    ));
} else {
    # new post
    
}

