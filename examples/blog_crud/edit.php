<?php

include('config.php');
include('blog_config.php');
$tp = new TypePad();

?>

<head>
<?php

$tp->sessionSyncScriptTag();
include('load_user.inc');

if ($_GET['id']) {
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

$content = $post ? $post->content : '';
$title = $post ? $post->title : '';
$id = $post ? $post->urlId : '';

?>

<form action="save.php" method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="text" name="title" value="<?php echo $title; ?>" />
<br />
<textarea name="content"><?php echo $content; ?></textarea>
<input type="submit" value="Save" />
</form>

</body>
</html>
