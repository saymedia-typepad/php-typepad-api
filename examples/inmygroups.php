<?php
include('config.php');
$tp = new TypePad();

$show_max = array_key_exists('show_max', $_GET) ? $_GET['show_max'] : 30;
$group_filter = array();
foreach (preg_grep('/^group_filter_/', array_keys($_GET)) as $key) {
    $group_filter[preg_replace('/^group_filter_/', '', $key)] = 1;
}
$user_id = array_key_exists('user_id', $_GET) ? $_GET['user_id'] : '';
$groups_by_id = array();
$out = '';
if ($user_id) {
    $memberships = $tp->users->getMemberships($user_id);
    
    $all_events = array();
    if ($memberships->entries) {
        foreach ($memberships->entries as $entry) {
            $groups_by_id[$entry->source->urlId] = $entry->source;
        }
    }
    
    uasort($groups_by_id, "groupcmp");

	$all_results = array();
	$tp->openBatch();
    foreach (array_keys($groups_by_id) as $xid) {
        if (array_key_exists($xid, $group_filter) || (sizeof($group_filter) == 0)) {
        	array_push($all_results, $tp->groups->getEvents($xid));
        }
    }
    try {
        $tp->runBatch();
		foreach ($all_results as $result) {
			foreach ($result->entries as $event) {
				array_push($all_events, $event);
			}
		}

		usort($all_events, "datecmp");
		
		$out = '<ul>';
		for ($i = 0; $i < (sizeof($all_events) < $show_max ? sizeof($all_events) : $show_max); $i++) {
			$event = $all_events[$i];
			$group = $event->object->groups[0];
			$group = preg_replace('/tag:api.typepad.com,2009:/', '', $group);
			if ($event->object->title) {
				$title = $event->object->title;
			} elseif ($event->object->content) {
				$title = substr($event->object->content, 0, 30) . '...';
			} else {
				$title = 'a ' . strtolower(preg_replace('/^tag:api.typepad.com,2009:/', '', $event->object->objectTypes[0]));
			}
			$out .= '<li><a href="' . $event->actor->profilePageUrl . '">'
				. htmlentities($event->actor->displayName ? $event->actor->displayName : $event->actor->preferredUsername)
				. '</a> posted <a href="'
				. $event->object->permalinkUrl . '">'
				. htmlentities($title) . '</a>'
				. ' to <a href="' . $groups_by_id[$group]->siteUrl . '">'
				. $groups_by_id[$group]->displayName . '</a>'
				;
		}
    } catch (TPException $e) {
		print "An error occurred: "
			. $e->getCode() .' '. $e->getMessage();
	}

}

function datecmp($a, $b) {
    return strcmp($b->published, $a->published);
}

function groupcmp($a, $b) {
    return strcasecmp($a->displayName, $b->displayName);
}

function curl_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

?>
<html>
<head>
<style type="text/css">
body {
font-family: verdana,sans-serif;
}
h1 {
font-size:18pt;
margin-bottom:0;
}
#leftnav {
float: left;
width: 200px;
margin: 0;
padding: 1em;
font-size: 9pt;
}
#content {
margin-left: 240px;
padding: 1em;
font-size:10pt;
}
.error {
font-weight:bold;
color:red;
}
</style>
<title>Recent Posts To My Groups</title>
</head>
<body>
<h1>Recent Posts To My Groups</h1>
<div id="leftnav">
<form method="GET">
Profile alias or XID: <input type="text" name="user_id" value="<?php echo $user_id ?>" /><br />
<?php
if ($user_id && !$memberships->entries) {
    echo <<<EOH
<div class="error">User not found</div>
EOH;
}
?>
Show up to <input type="text" name="show_max" size="2" value="<?php echo $show_max ?>" /> posts
<input type="submit" />
</form>
<form method="GET">
<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
<input type="hidden" name="show_max" value="<?php echo $show_max ?>" />
<?php
if ($groups_by_id) {
    foreach ($groups_by_id as $xid => $group) {
        echo '<input type="checkbox" name="group_filter_' . $xid . '"'
        . ((sizeof($group_filter) == 0) || $group_filter[$xid] ? ' checked="checked"' : '') . ' /> '
        . $group->displayName . '<br />';
    }
    echo '<input type="submit" value="Filter" />';
}
?>
</form>
</div>

<div id="content">
<?php echo $out ?>
</div>

</body>
</html>
