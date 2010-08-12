<?php
$welcome = '';
try {

    $user = $tp->users->get('@self');
    $welcome = "<p>Welcome, " . $user->displayName . "!</p>"
    . '<p><a href="logout.php">Log out</a></p>';

} catch (TPException $e) {

    if ($e->getCode() == 404) {
        $welcome = <<<HTML
You are not logged in. <a href="login.php">Click here</a> to log in.
HTML;
    } else {
        // We're not expecting an error other than a 404, so just tell
        // the user about it.
        print "An error occurred: "
            . $e->getCode() .' '. $e->getMessage()
            . " (request was " . $e->getRequest()->getUri() . ")";
    }
}
?>