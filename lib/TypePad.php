<?php
/**
 * Interface to the TypePad API.
 *
 * @package TypePad-API
 */

/*
 * Copyright (c) 2010 Six Apart Ltd.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the name of Six Apart Ltd. nor the names of its contributors may
 *   be used to endorse or promote products derived from this software without
 *   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 */

/**
 * Object class allowing interaction with the TypePad API.
 *
 * @package TypePad-API
 * @subpackage TypePad
 */

class TypePad {

    protected $user_session;
    protected $batch;
    protected $requests;
    protected $result_types;
    protected $last_response;
    protected $nouns;
    protected static $noun_classes;

    /**
     * Magic to enable calling methods on nouns.
     *
     * This is what allows you to make API calls with the syntax
     * $tp->noun->method(), where $tp is an instance of the TypePad class.
     * <code>$tp->users->get($id)</code>
     * <code>$tp->blogs->getPostAssetsByCategory($params)</code>
     *
     * @param string $name The name of the noun (without 'TP')
     * @return string The name of the TPNoun subclass
     */
    function __get($name) {
        if (isset($this->nouns[$name])) return $this->nouns[$name];
        if (isset(self::$noun_classes[$name])) {
            $class = self::$noun_classes[$name];
            $this->nouns[$name] = new $class($this);
            return $this->nouns[$name];
        }
        TypePad::throwPropertyNotice($name);
    }

    /**
     * Tell the TypePad library about a noun class.
     *
     * This is called from the auto-generated libraries, and you should not
     * need to call it directly.
     *
     * @param string $noun The name of the noun (without 'TP')
     */
    static function addNoun($noun, $class = null) {
        if (!$class) {
            $class = 'TP' . ucfirst($noun);
        }
        if (!isset(self::$noun_classes)) self::$noun_classes = array();
        if (isset(self::$noun_classes[$noun])) return;
        self::$noun_classes[$noun] = $class;
    }

    /**
     * The base URL for non-secure TypePad API endpoints.
     *
     * Returns the TP_API_BASE defined in your config.php, but always with a
     * trailing slash whether or not the constant in the config has one.
     *
     * @return string The URL.
     */
    static function base() {
        return preg_match('/\/$/', TP_API_BASE)
            ? TP_API_BASE
            : TP_API_BASE . '/';
    }

    /**
     * The base URL for secure TypePad API endpoints.
     *
     * Returns the TP_API_BASE_SECURE defined in your config.php, but always
     * with a trailing slash whether or not the constant in the config has one.
     *
     * @return string The URL.
     */
    static function baseSecure() {
        return preg_match('/\/$/', TP_API_BASE_SECURE)
            ? TP_API_BASE_SECURE
            : TP_API_BASE_SECURE . '/';
    }

    /**
     * Apply the default cURL option settings for an API request.
     *
     * @param HttpRequest $request
     */
    static function setCurlopts($request) {
        if (TP_INSECURE) {
            $request->setCurlopts(array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ));
        }
    }

    private function _prepRequest($request) {
        self::setCurlopts($request);
        $request->setHeader('Authorization',
            $this->userSession()->authString());
    }

    /**
     * Get the current user's session.
     *
     * Returns the $user_session for this instance of TypePad if already;
     * populated; if not, instantiates a new TPSession object.
     *
     * If no user is logged in, a TPSession object will still be returned,
     * which can be used to generate an auth header with the application's
     * anonymous access keys.
     *
     * @return TPSession The session object.
     */
    function userSession() {
        if ($this->user_session) return $this->user_session;
        $this->user_session = new TPSession($this);
        return $this->user_session;
    }

    /**
    * Get the urlId of the currently logged-in user.
    *
    * @return string
    */
    function userUrlId() {
        return $this->userSession()->userUrlId();
    }

    /**
     * Set the user session for this instance of TypePad.
     *
     * @param TPSession $session
     */
    function setUserSession($session) {
        $this->user_session = $session;
    }

    /**
     * Output a script tag for session synchronization.
     *
     * This method is provided for use on sites that allow login via TypePad.
     * it writes a &lt;script&gt; tag to the page that calls in a script file from
     * typepad.com, allowing TypePad to recognize that the user is logged in
     * to TypePad and, if so, provide the user's information to your PHP application.
     * To use this session synchronization mechanism, simply include this in your
     * page's &lt;head&gt;:
     *
     * <code><?php $tp->sessionSyncScriptTag(); ?></code>
     *
     * @link http://www.typepad.com/services/apidocs/authentication#session_synchronization
     * @param string $callback_url (Optional)
     */
    function sessionSyncScriptTag($callback_url = TP_SYNC_URL) {
        $url = $this->userSession()->sessionSyncScriptUrl($callback_url);
        if (!$url) return;
        echo <<<EOT
<script type="text/javascript" src="$url"></script>
EOT;
    }

    /**
     * Synchronize the user's session with TypePad.
     *
     * This method should be called from a page that does nothing except
     * load config.php, create a new instance of TypePad, and call syncSession().
     * After attempting to synchronize the session, it will redirect to the
     * callback URL passed through by TypePad.
     */
    function syncSession() {
        $this->userSession()->syncSession();
    }

    /**
     * Begin queuing requests for batch retrieval.
     *
     * Any calls to API endpoint functions that come after an openBatch()
     * will not call the API yet, but will add them to the batch; when you've
     * queued all the subrequests you need to render the page, call
     * $tp->runBatch().
     *
     * Each instance of the TypePad class has its own batch (or no batch),
     * so you could build multiple batches at the same time, or run some
     * requests synchronously while building a batch for others, by
     * creating multiple TypePad instances.
     */
    function openBatch() {
        if ($this->batch) return $this->batch;
        $this->requests = array();
        $this->result_types = array();
        $auth_string = self::userSession()->authString();
        $this->batch = new BatchRequest('POST', self::baseSecure() . '/batch-processor');
        $this->batch->setHeader('Authorization', $auth_string);
        $this->batch->setDefaultHeader('Authorization', $auth_string);
        self::setCurlopts($this->batch);
    }

    /**
     * Returns the currently open batch, if any, for this instance of TypePad.
     *
     * @return BatchRequest|NULL
     */
    function currentBatch() {
        return $this->batch;
    }

    private static function _makeUrl($path_chunks, $params = array()) {
        $url = (TP_CONSUMER_KEY ? self::baseSecure() : self::base())
            . implode('/', $path_chunks) . '.json';
        if ($params) {
            $pairs = array();
            foreach ($params as $name => $value) {
                array_push($pairs, urlencode($name) . '=' . urlencode($value));
            }
            $url .= '?' . implode('&', $pairs);
        }
        return $url;
    }

    private static function _objectFromType($result_type, $request_id = NULL, $fulfill_with = NULL) {
        $of_class = NULL;
        if ($fulfill_with) {
            $fulfill_with = TypePad::_json_decode($fulfill_with);
        }
        if (preg_match('/:/', $result_type)) {
            // an action endpoint will return a hash where the object
            // we're after is in one of its properties, rather than
            // returning the object directly
            list($property, $class) = explode(':', $result_type);
        } elseif (preg_match('/(.*)<([^>]+)>/', $result_type, $matches)) {
            $class = $matches[1];
            $of_class = $matches[2];
        } elseif ($fulfill_with && isset($fulfill_with->objectType)) {
            $class = $fulfill_with->objectType;
        } else {
            $class = $result_type;
        }
        $class = "TP$class";
        $promise = new $class($request_id, $of_class);
        if ($fulfill_with) {
            $promise->fulfill($fulfill_with);
        }
        return $promise;
    }

    /**
     * Add a subrequest to the open batch.
     *
     * Normally you should have no need to call this method directly;
     * it's called by get(), post(), put(), and delete(), which in turn
     * are called by the functions for specific API endpoints.
     *
     * The returned object will be an instance of the appropriate class,
     * but will not be populated until the batch is run.
     *
     * @param string $method      The request's HTTP method.
     * @param array  $path_chunks The pieces of the URL after the base.
     * @param array  $params      URL query parameters, if any.
     * @param string $content     The payload of a POST or PUT request.
     * @param string $result_type The expected object type the request will return.
     *
     * @return TPObject An instance of the subclass corresponding to the $result_type.
     */
    function addRequest($method, $path_chunks, $params, $content, $result_type) {
        if (!$this->batch) {
            trigger_error(
                "addRequest() called with no batch open",
                E_USER_NOTICE
            );
            return;
        }
        $url = self::_makeUrl($path_chunks, $params);
        $request_id = $this->batch->addRequest($method, $url, NULL, $content);
        $promise = self::_objectFromType($result_type, $request_id);
        array_push($this->requests, $promise);
        array_push($this->result_types, $result_type);
        return $promise;
    }

    /**
     * Run a batch of subrequests.
     *
     * Constructs a multi-part request from all the subrequests in the current
     * batch, sends it to the TypePad API, and parses the batch response into
     * subresponses; then fulfills each of the objects returned by addRequest()
     * with the corresponding response.
     *
     * This should always be used within a try/catch block, because it will
     * throw an exception if the API returns an error for any subrequest, or
     * for the batch request as a whole.
     */
    function runBatch() {
        if (!$this->batch) {
            trigger_error(
                'runBatch() was called, but no batch was open',
                E_USER_NOTICE
            );
            return;
        }
        $response = $this->batch->getResponse();
        if ($response->isError()) {
            throw new TPException($response);
        }
        $responses = $this->batch->getResponses();
        if (!$responses) {
            throw new TPException($this->batch->getResponse());
        }
        foreach ($responses as $response) {
            $index = $response->getHeader('Multipart-Request-ID')-1;
            if ($response->isError()) {
                throw new TPException($response, $this->batch->getRequest($index));
            }
            $this->requests[$index]->fulfill(TypePad::_json_decode($response->getContent()), $this->result_types[$index]);
        }
        $this->batch = NULL;
    }

    /**
     * Execute or batch a GET request to the API.
     *
     * Normally you shouldn't need to call this directly; it's called by the
     * handlers for the individual API endpoints.
     *
     * @param array  $path_chunks The pieces of the URL after the base.
     * @param array  $params      URL query parameters, if any.
     * @param string $result_type The expected object type the request will return.
     */
    function get($path_chunks, $params, $result_type) {
        if ($this->batch) {
            return $this->addRequest('GET', $path_chunks, $params, NULL, $result_type);
        }
        $request = new HttpRequest('GET', self::_makeUrl($path_chunks, $params));
        if (TP_CONSUMER_KEY) {
            self::_prepRequest($request);
        }
        $response = $request->getResponse();
        try {
            $result = $this->_resultOrError($request, $response, $result_type);
        } catch (TPException $e) {
            throw $e;
        }
        return $result;
    }

    /**
     * Execute or batch a POST request to the API.
     *
     * Normally you shouldn't need to call this directly; it's called by the
     * handlers for the individual API endpoints.
     *
     * @param array  $path_chunks The pieces of the URL after the base.
     * @param string $content     The payload of a POST or PUT request.
     * @param string $result_type The expected object type the request will return.
     */
    function post($path_chunks, $content, $result_type) {
        return $this->_postPut('POST', $path_chunks, $content, $result_type);
    }

    /**
     * Execute or batch a PUT request to the API.
     *
     * Normally you shouldn't need to call this directly; it's called by the
     * handlers for the individual API endpoints.
     *
     * @param array  $path_chunks The pieces of the URL after the base.
     * @param string $content     The payload of a POST or PUT request.
     * @param string $result_type The expected object type the request will return.
     */
    function put($path_chunks, $content, $result_type) {
        return $this->_postPut('PUT', $path_chunks, $content, $result_type);
    }

    private function _postPut($method, $path_chunks, $content, $result_type) {
        if (is_a($content, 'TPObject')) {
            $content = $content->asPayload();
        } elseif (is_object($content) || is_array($content)) {
            $content = TypePad::_json_encode($content);
        }
        if ($this->batch) {
            return $this->addRequest($method, $path_chunks, NULL, $content, $result_type);
        }
        $request = new HttpRequest($method, self::_makeUrl($path_chunks));
        $request->setContent($content);
        $request->setHeader('Content-type', 'application/json');
        self::_prepRequest($request);
        $response = $request->getResponse();
        try {
            $result = $this->_resultOrError($request, $response, $result_type);
        } catch (TPException $e) {
            throw $e;
        }
        return $result;
    }

    /**
     * Execute or batch a DELETE request to the API.
     *
     * Normally you shouldn't need to call this directly; it's called by the
     * handlers for the individual API endpoints.
     *
     * @param array  $path_chunks The pieces of the URL after the base.
     * @param string $result_type The expected object type the request will return.
     */
    function delete($path_chunks, $result_type) {
        if ($this->batch) {
            return $this->addRequest('DELETE', $path_chunks, NULL, NULL, $result_type);
        }
        $request = new HttpRequest('DELETE', self::_makeUrl($path_chunks));
        self::_prepRequest($request);
        $response = $request->getResponse();
        try {
            $result = $this->_resultOrError($request, $response, $result_type);
        } catch (TPException $e) {
            throw $e;
        }
        return $result;
    }

    /**
     * Return the API response last encountered by this instance of TypePad.
     *
     * @return HttpResponse
     */
    function lastResponse() {
        return $this->last_response;
    }

    private function _resultOrError($request, $response, $result_type) {
        $this->last_response = $response;
        if (!$response->isError()) {
            return self::_objectFromType($result_type, NULL, $response->getContent());
        } else {
            throw new TPException($response, $request);
        }
    }

    /**
     * Throw a user notice when an attempt is made to access an object property
     * that's not in the object class's dictionary of properties.
     */
    static function throwPropertyNotice($name) {
        $trace = debug_backtrace();
        trigger_error(
            "Undefined property: TypePad::\$$name in {$trace[1]['file']} on line {$trace[1]['line']}",
            E_USER_NOTICE
        );
    }

    /**
     * Decode JSON, degrading to a pure-PHP library if the compiled function
     * is not available
     *
     * @param string $str The JSON to decode
     * @return stdClass
     */
    static function _json_decode($str) {
        $str = utf8_encode($str);
        if (!function_exists('json_decode')) {
            function json_decode($doc) {
                require_once 'json_lib.php';
                $json = new Services_JSON(0);
                $result = $json->decode($doc);
                return $result;
            }
        }

        return json_decode($str);
    }

    /**
     * Encode JSON, degrading to a pure-PHP library if the compiled function
     * is not available
     *
     * @param array $data The object to decode
     * @return string
     */
    static function _json_encode($data) {
        if (!function_exists('json_encode')) {
            function json_encode($obj) {
                require_once 'json_lib.php';
                $json = new Services_JSON(0);
                $result = $json->encode($obj);
                return $result;
            }
        }

        return json_encode($data);
    }


}

/**
 * Allow deferred object population for batch requests.
 *
 * Every TPObject is actually a TPPromise; it won't have any content until
 * it's fulfill()ed with some data. When a non-batch request is made, the
 * result object is simply fulfill()ed immediately with the response data.
 *
 * If a TPObject subclass has properties that are themselves TPObjects,
 * it will override the fulfill() method in order to make the properties
 * instances of the appropriate classes.
 *
 * @package TypePad-API
 * @subpackage TPPromise
 */
class TPPromise {

    public $request_id;
    protected $data;
    protected $fulfilled;

    function __construct($data = NULL) {
        if (!$data) {
            // called manually to make an object to post back to the API
            $this->data = new stdClass();
        } elseif (is_array($data)) {
            // called manually to make an object to post back to the API
            $obj = (object) $data;
            $this->fulfill($obj);
        } elseif (is_object($data)) {
            $this->fulfill($data);
        } else {
            $this->request_id = $data;
        }
    }

    /**
     * Populate a promise object with content and mark it as fulfilled.
     *
     * @param array $data The content with which to fulfill the promise.
     * @param string $result_type (Optional) The expected type of the data.
     */
    function fulfill($data, $result_type = NULL) {
        if ($result_type && preg_match('/:/', $result_type)) {
            list($property, $result_type) = explode(':', $result_type);
            $data = $data[$property];
        }
        $this->data = $data;
        $this->fulfilled = true;
    }

    /**
     * Return whether this promise has been fulfilled.
     *
     * @return boolean
     */
    function isFulfilled() {
        return $this->fulfilled ? true : false;
    }

}

/**
 * Base class for objects requested from the TypePad API.
 *
 * @package TypePad-API
 * @subpackage TPObject
 */
class TPObject extends TPPromise {

    protected static $properties;

    /**
     * Property getter method. PHP's inheritance model is rather, um,
     * interesting, so in order for each subclass to define its own set of
     * properties, we end up having to define the magic methods __get() and
     * __set() in every subclass, which in turn call get() and set() in the
     * base class.
     */
    function get($name, $properties) {
        if (TP_STRICT_PROPERTIES) {
            if (!isset($properties[$name])) return TypePad::throwPropertyNotice($name);
        }
        return isset($this->data->$name) ? $this->data->$name : NULL;
    }

    /**
     * Property setter method.
     */
    function set($name, $value, $properties) {
        if (TP_STRICT_PROPERTIES) {
            if (!isset($properties[$name])) return TypePad::throwPropertyNotice($name);
        }
        $this->data->$name = $value;
        return $this->data->$name;
    }

    /**
     * Translate a member of an object into a JSON payload.
     *
     * @param TPObject $member
     * @return string
     */
    static function memberAsPayload($member) {
        if (is_object($member) && is_a($member, 'TPObject')) {
            return $member->asPayload(NULL, 0);
        } else {
            return $member;
        }
    }

    /**
     * Translate a TPObject into a simple stdClass object, and optionally
     * a JSON representation of that object.
     *
     * @param array $properties  The object class's properties, which we
     *                           need to pass around because PHP is sort of
     *                           broken with regard to inheritance.
     * @param boolean $want_json Return a JSON string, or an object?
     * @return string|stdClass
     */
    function asPayload($properties, $want_json = 1) {
        $obj = new stdClass();
        foreach (array_keys($properties) as $key) {
            if (!$this->$key) continue;
            if (is_array($this->$key)) {
                $obj->$key = array();
                foreach ($this->$key as $elem) {
                    array_push($obj->$key, self::memberAsPayload($elem));
                }
            } else {
                $obj->$key = self::memberAsPayload($this->$key);
            }
        }
        return $want_json ? _json_encode($obj) : $obj;
    }

    /**
     * Called on a TPObject, return an instance of the appropriate subclass
     * based on the object's type. This is necessary in cases when a
     * batched subrequest returns an object whose type is not known
     * until the batch is run--for example, an endpoint that returns a
     * TPAsset which may turn out to be a TPPost, TPComment, etc.
     */
    function reclass() {
        if ($this->data->objectType) {
            $class = 'TP' . $this->data->objectType;
            if (call_user_func(array($class, 'isAbstract'))) {
                return new $class($this->data);
            }
        }
        return $this;
    }

}

/**
 * Object class representing a list of items of a given type.
 *
 * @package TypePad-API
 * @subpackage TPList
 */
class TPList extends TPObject {

    protected $of_class;

    protected static $properties = array(
        'totalResults' => array('The total number of items in the whole list of which this list object is a paginated view.', 'integer'),
        'entries' => array('The items within the selected slice of the list.', 'array')
    );

    function __construct($data = NULL, $of_class = NULL) {
        parent::__construct($data);
        $this->of_class = $of_class;
    }

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { return $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }

    function fulfill($data) {
        parent::fulfill($data);
        if (!isset($this->data->entries)) $this->data->entries = array();
        for ($i = 0; $i < count($this->data->entries); $i++) {
            $of_class = 'TP' . $this->of_class;
            $this->data->entries[$i] = new $of_class($this->data->entries[$i]);
        }
    }
}

/**
 * Object class representing a stream of items.
 *
 * @package TypePad-API
 * @subpackage TPStream
 */
class TPStream extends TPList {

    protected static $properties = array(
        'totalResults' => array('The total number of items in the whole stream of which this response contains a subset. null if an exact count cannot be determined.', 'integer'),
        'estimatedTotalResults' => array('An estimate of the total number of items in the whole list of which this response contains a subset. null if a count cannot be determined at all, or if an exact count is returned in totalResults.', 'integer'),
        'moreResultsToken' => array('An opaque token that can be used as the start-token parameter of a followup request to retrieve additional results. null if there are no more results to retrieve, but the presence of this token does not guarantee that the response to a followup request will actually contain results.', 'string'),
        'entries' => array('A selection of items from the underlying stream.', 'array')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { return $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }


}

/**
 * Base class for nouns in the TypePad API.
 *
 * @package TypePad-API
 * @subpackage TPNoun
 */
class TPNoun {

    protected $typepad;

    function __construct($typepad) {
        $this->typepad = $typepad ? $typepad : new TypePad;
    }
}

/**
 * Exception class for HTTP error responses returned by the TypePad API.
 *
 * @package TypePad-API
 * @subpackage TPException
 */
class TPException extends Exception {

    protected $response;
    protected $request;

    function __construct($response, $request = NULL) {
        parent::__construct($response->getMessage(), $response->getCode());
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Return the HTTP response that caused the exception to be thrown.
     *
     * @return HttpResponse
     */
    function getResponse() {
        return $this->response;
    }

    /**
     * Return the HTTP request in response to which the error was received.
     *
     * @return HttpRequest
     */
    function getRequest() {
        return $this->request;
    }
}
$dir = preg_replace('/TypePad.php$/', '', __FILE__);
$extdir = preg_replace('/lib\/$/', 'extlib/', $dir);
set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), $dir, $extdir)));

require_once('BatchRequest.php');
require_once('TypePad/Auth.php');
require_once('TypePad/Nouns.php');
require_once('TypePad/ObjectTypes.php');
// Transparently replace the curl_* functions with command-line or pure-PHP
// implementations if they're not compiled into the PHP we're running under.
require_once('libcurlemu/libcurlemu.inc.php');

