<?php

// The name of the cookie that will hold the session ID for a logged-in user
define ('TP_COOKIE_NAME', 'tp-session');

// The access keys generated by signing up for an TypePad API key
// (http://www.typepad.com/account/access/developer when logged in to TypePad)
define ('TP_CONSUMER_KEY', '');
define ('TP_CONSUMER_SECRET', '');
define ('TP_GENERAL_PURPOSE_KEY', '');
define ('TP_GENERAL_PURPOSE_SECRET', '');

// The type of access that this application will request from TypePad on behalf
// of a user who signs up for it. 'app_full' will give the user access to 
// content owned by the application; 'typepad_full' will give the user access
// to TypePad content.
define ('TP_ACCESS_TYPE', 'typepad_full');

// the default URL to which TypePad should redirect the browser after login
// or logout. 
define ('TP_RETURN_URL', 'index.php');
// The default URL to which TypePad should redirect the browser in order to
// synchronize this application's session with TypePad's. This must be an empty
// page (nothing outside of a php block) that calls $tp->syncSession().
define ('TP_SYNC_URL', 'sync.php');

// MySQL database login information. Required if this application allows users
// to log in via TypePad; if the application only accesses the API anonymously,
// the TypePad library can work without a database.
define ('TP_DB_HOST', 'localhost');
define ('TP_DB_USERNAME', 'root');
define ('TP_DB_PASSWORD', '');
define ('TP_DB_NAME', 'typepad');

// The locations of the TypePad API endpoints. 
define ('TP_API_BASE', 'http://api.typepad.com');
define ('TP_API_BASE_SECURE', 'https://api.typepad.com');

// Set this to true if hitting dev API backends with no SSL certs.
define ('TP_INSECURE', true);

define ('TP_STRICT_PROPERTIES', false);

include_once('../../lib/TypePad.php');

?>
