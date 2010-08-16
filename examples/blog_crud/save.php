<?php

include('config.php');
include('blog_config.php');
$tp = new TypePad();

$saved = 0;
$error = false;

try {
	if (array_key_exists('id', $_POST) && $_POST['id']) {
		// updating an existing post
		$post = $tp->assets->put(array(
			'id' => $_POST['id'],
			'payload' => array(
				'title' => $_POST['title'],
				'content' => $_POST['content']
			)
		));
	} else {
		// new post
		$post = $tp->blogs->postToPostAssets(array(
			'id' => BLOG_ID,
			'payload' => array(
				'title' => $_POST['title'],
				'content' => $_POST['content'],
			)
		));
	}
	$saved = 1;
} catch(TPException $e) {
    $error = $e->getCode() . ' ' . $e->getMessage();
}

header("Location: edit.php?id={$post->urlId}&saved=$saved" . ($error ? "&error=$error" : ''));
