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
</head>

<?php
$result = $tp->assets->search(array('q' => 'ricardo montalban'));
print_r($result);
?>
