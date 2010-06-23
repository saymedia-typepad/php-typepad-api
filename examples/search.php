<?php
include('config.php');
$tp = new TypePad();

if (array_key_exists('q', $_GET)) {
	$has_search = true;
	$param = array_key_exists('token', $_GET)
		? array('startToken' => $_GET['token'])
		: array('q' => $_GET['q']);
	$param['limit'] = 14;
	$search_results = $tp->assets->search($param);
	if ($search_results->entries) {
		$total = $search_results->totalResults
			? $search_results->totalResults
			: 'about ' . $search_results->estimatedTotalResults;
	} else {
		$total = '0';
	}
} else {
	$has_search = false;
}
$entry_divs = '';
?>
<html>

<head>
<title>TypePad API Search<?php if ($has_search) { echo "for '{$_GET['q']}'"; } ?></title>
<style type="text/css">
#all {
width:750px;
margin:0 auto;
font-family:Verdana,Arial,sans-serif;
}
.search-form {
text-align:center;
font-weight:bold;
}
.entry-hidden {
display:none;
}
#search-results {
float:left;
width:300px;
font-size:8pt;
}
#search-results-label {
font-size:10pt;
font-weight:bold;
padding-bottom:10px;
}
#search-results ul {
margin:0;
padding:0;
list-style-type:none;
}
#search-results li {
padding-bottom:5px;
}
#entry {
float:right;
width:430px;
padding-left:15px;
font-size:10pt;
}
.entry-title {
font-weight:bold;
font-size:11pt;
}
</style>
<script type="text/javascript">
var showing = -1;
function showEntry(i) {
	var ediv = document.getElementById('entry-' + i);
	document.getElementById('entry').innerHTML = ediv.innerHTML;
}
</script>
</head>

<body>
<div id="all">
<div class="search-form">
<form method="GET">
Enter a search term: 
<input type="text" name="q"<?php if ($has_search) { echo ' value="' . $_GET['q'] . '"'; } ?> />
<input type="submit" value="Search" />
</form>
</div>

<?php if ($has_search) { ?>
	<div id="search-results">
	<div id="search-results-label">Found <?php echo $total; ?> results for '<?php echo $_GET['q']; ?>'</div>
	<?php if ($search_results->moreResultsToken) { ?>
		<div id="search-results-more">
		<form method="GET">
		<input type="hidden" name="q" value="<?php echo $_GET['q']; ?>" />
		<input type="hidden" name="token" value="<?php echo $search_results->moreResultsToken; ?>" />
		<input type="submit" value="More" />
		</form>
		</div>
	<?php } ?>
	<ul>
	<?php
	$i = 0;
	if ($search_results->entries) {
		foreach ($search_results->entries as $entry) {
			$title = $entry->title ? $entry->title : '[untitled]';
			$ot = strtolower($entry->container->objectType);
			echo <<<EOH
<li><a href="{$entry->permalinkUrl}" onmouseover="showEntry($i);">$title</a> from $ot <a href="{$entry->container->homeUrl}">{$entry->container->displayName}</a></li>
EOH;
		$entry_divs .= <<<EOH
<div id="entry-$i" class="entry-hidden">
<div class="entry-title"><a href="{$entry->permalinkUrl}">$title</a> from $ot <a href="{$entry->container->homeUrl}">{$entry->container->displayName}</a></div>
<div class="entry-content">{$entry->content}</div>
</div>
EOH;
			$i++;
		}
	}
	?>
	</ul>
	</div>

<?php } ?>
	<div id="entry"></div>

<?php echo $entry_divs; ?>
</div>
</body>
</html>