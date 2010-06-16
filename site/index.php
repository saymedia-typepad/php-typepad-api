<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

include('config.php');

$tp = new TypePad();
?>

<head>
<?php $tp->sessionSyncScriptTag(); ?>
</head>

<?php

if (0) {
$tp->openBatch();
}
if (1) {
//$user = $tp->users->get('6p011e7c340484a5f8');
$user = $tp->users->get('@self');
print_r($user);
exit;
}
//$events = $tp->groups->getEvents('6p01229915440b12ef');
//$user_group_events = $tp->users->getEventsByGroup(array('id' => '6p011e7c3404afa5f8', 'groupId' => '6p01229915440b12ef', 'limit' => 1));

if (0) {
$postme = new TPComment;
$postme->content = 'Find This';
$comment = $tp->assets->postToComments(array('id' => '6a011e7c3404afa5f8012fe0febfc6033a', 'payload' => $postme));
}

if (1) {
$comment = $tp->assets->get('6a011e7c3404afa5f80137f3d74045033d');
}

if (0) {
try {
$result = $tp->assets->delete('6a011e7c3404afa5f80137f3d74045033d');
} catch (TPException $e) {
print "AN ERROR OCCURRED: " . $e->getCode() .' '. $e->getMessage();
}
print_r($tp->lastResponse());
}

if (1) {
try {
	$tp->runBatch();
} catch (TPException $e) {
	print "AN ERROR OCCURRED: " . $e->getCode() .' '. $e->getMessage() . " REQUEST WAS " . $e->getRequest()->getUri();
	exit;
}
$comment = $comment->reclass();
}
if (1) {
print_r($comment);
}

?>
