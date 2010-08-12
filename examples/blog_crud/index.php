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
$param = array();
$param['id'] = BLOG_ID;
$param['limit'] = 3;
$param['month'] = '2010-07';
$posts = $tp->blogs->getPostAssetsByMonth($param);
foreach ($posts->entries as $post) {
    $post_list .= <<<EOH
<li><a href="edit.php?id={$post->urlId}">{$post->title}</a></li>
EOH;
}

?>

</head>
<body>

<?php echo $welcome; ?>

<ul>
<?php echo $post_list; ?>
</ul>

</body>
</html>
