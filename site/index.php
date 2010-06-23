<?php

// These two lines are all that's required for a page to use the TypePad
// API library. config.php contains some settings that must be filled in
// in order to use certain features.

include('config.php');
$tp = new TypePad();

?>

<head>
<?php

// This function is provided for use on sites that allow login via TypePad. 
// it writes a <script> tag to the page that calls in a script file from 
// typepad.com, allowing TypePad to recognize that the user is logged in
// to TypePad and, if so, provide the user's information to your PHP application.
// See http://www.typepad.com/services/apidocs/authentication for more details,
// although you shouldn't have to worry about them beyond including this
// function call in your page's <head>.

$tp->sessionSyncScriptTag();

?>

<?php

// This tells TypePad to begin queuing requests for batch retrieval.
$tp->openBatch();

// Any calls to API endpoint functions that come after an openBatch()
// will not call the API yet, but will add them to the batch; when you've
// queued all the subrequests you need to render the page, call 
// $tp->runBatch().

// Note that we don't need to wrap this in a try/catch block, because
// no HTTP error will be thrown until the batch is run; if we were not
// using a batch, we'd want to check for an exception on this get() call
// itself.
$user = $tp->users->get('@self');

?>

</head>

<body>
<?php
try {

	// This will throw a TPException if any of the batch's subrequests 
	// returned a non-success HTTP status. In this case, since we made a
	// request for the '@self' user, which will only succeed if the user is
	// logged in, we want to catch a 404 Not Found response and present 
	// the user with the option to log in.
	$tp->runBatch();
	print "Welcome, " . $user->displayName . "!";

} catch (TPException $e) {

	if ($e->getCode() == 404) {
	    print <<<HTML
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
</body>