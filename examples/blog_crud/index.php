<?php

include('config.php');
include('blog_config.php');
$tp = new TypePad();

?>

<head>
<?php

$tp->sessionSyncScriptTag();
include('load_user.inc');

$post_list = '';
$posts = $tp->blogs->getPostAssets(BLOG_ID);
foreach ($posts->entries as $post) {
    $post_list .= <<<EOH
<li><a href="edit.php?id={$post->urlId}">{$post->title}</a>&nbsp;&nbsp;<a href="javascript:deletePost('{$post->urlId}')">Delete</a></li>
EOH;
}

?>
<script type="text/javascript">
function deletePost(urlId) {
    if (confirm('Are you sure you want to delete this post?')) {
        document.getElementById('deletePostIdField').value = urlId;
        document.getElementById('deleteForm').submit();
    }
}
</script>
</head>
<body>

<?php echo $welcome; ?>

<?php
if (array_key_exists('error', $_GET) && $_GET['error']) {
    echo "<div>An error occurred: {$_GET['error']}</div>";
}
if (array_key_exists('deleted', $_GET) && $_GET['deleted']) {
    echo "<div>Your post has been deleted.</div>";
}
?>

<ul>
<?php echo $post_list; ?>
</ul>
<form action="delete.php" method="POST" id="deleteForm">
<input type="hidden" name="id" id="deletePostIdField" value="" />
</form>
</form>
</body>
</html>
