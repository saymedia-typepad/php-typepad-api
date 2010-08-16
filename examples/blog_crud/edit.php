<?php

include('config.php');
include('blog_config.php');
$tp = new TypePad();

?>

<head>
<?php

$tp->sessionSyncScriptTag();
include('load_user.inc');

$post = false;
if (array_key_exists('id', $_GET)) {
    $tp->openBatch();
    $post = $tp->assets->get($_GET['id']);
    $extended = $tp->assets->getExtendedContent($_GET['id']);
    $tp->runBatch();
    $post = $post->reclass();
}

?>

</head>
<body>

<?php echo $welcome; ?>

<?php
if (array_key_exists('error', $_GET) && $_GET['error']) {
    echo "<div>An error occurred: {$_GET['error']}</div>";
}
if (array_key_exists('saved', $_GET) && $_GET['saved']) {
    echo "<div>Your post has been saved.</div>";
}
?>

<?php

$content = $post ? $post->content : '';
$title = $post ? $post->title : '';
$id = $post ? $post->urlId : '';

?>

<form action="save.php" method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="text" name="title" value="<?php echo $title; ?>" style="width:300px;" />
<br />
<textarea name="content" style="width:300px;height:200px;"><?php echo $content; ?></textarea>
<div><input type="submit" value="Save" /></div>
</form>

<div><a href="index.php">Return to list</a></div>

</body>
</html>
