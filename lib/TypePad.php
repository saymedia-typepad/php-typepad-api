<?php

$lib_dir = preg_replace('/TypePad.php$/', '', __FILE__);
set_include_path(get_include_path() . PATH_SEPARATOR . $dir);

require_once('BatchRequest.php');
require_once('TypePad/Auth.php');
require_once('TypePad/Nouns.php');
require_once('TypePad/ObjectTypes.php');
// Transparently replace the curl_* functions with command-line or pure-PHP
// implementations if they're not compiled into the PHP we're running under. 
require_once('libcurlemu/libcurlemu.inc.php');

function throwPropertyNotice($name) {
    $trace = debug_backtrace();
    trigger_error(
        "Undefined property: TypePad::\$$name in {$trace[1]['file']} on line {$trace[1]['line']}",
        E_USER_NOTICE
    );
}

function _json_decode($str) {
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

class TypePad {
    
    protected $user_session;
    protected $batch;
    protected $requests;
    protected $result_types;
    protected $last_response;
    protected $nouns;
    protected static $noun_classes;
    
    // magic to enable calling methods on nouns, i.e. $tp->groups->postBlah(...)
    public function __get($name) {
        if (isset($this->nouns[$name])) return $this->nouns[$name];
        if (isset(self::$noun_classes[$name])) {
            $class = 'TP' . ucfirst($name);
            $this->nouns[$name] = new $class($this);
            return $this->nouns[$name];
        }
        throwPropertyNotice($name);
    }
    
    static function addNoun($noun) {
        $class = 'TP' . ucfirst($noun);
        if (!isset(self::$noun_classes)) self::$noun_classes = array();
        if (isset(self::$noun_classes[$class])) return;
        self::$noun_classes[$noun] = $class;
    }
    
    static function base() {
        return preg_match('/\/$/', TP_API_BASE)
            ? TP_API_BASE
            : TP_API_BASE . '/';
    }
    
    static function baseSecure() {
        return preg_match('/\/$/', TP_API_BASE_SECURE)
            ? TP_API_BASE_SECURE
            : TP_API_BASE_SECURE . '/';
    }
    
    static function setCurlopts($request) {
        if (TP_INSECURE) {
            $request->setCurlopts(array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ));
        }
    }
    
    function prepRequest($request) {
        self::setCurlopts($request);
        $request->setHeader('Authorization',
            $this->userSession()->authString());
    }
    
    function userSession() {
        if ($this->user_session) return $this->user_session;
        $this->user_session = new TPSession($this);
        return $this->user_session;
    }
    
    function setUserSession($session) {
        $this->user_session = $session;
    }
    
    function sessionSyncScriptTag($callback_url = TP_SYNC_URL) {
        $url = $this->userSession()->sessionSyncScriptUrl($callback_url);
        echo <<<EOT
<script type="text/javascript" src="$url"></script>
EOT;
    }
    
    function syncSession() {
        $this->userSession()->syncSession();
    }
    
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
    
    function currentBatch() {
        return $this->batch;
    }
    
    static function makeUrl($path_chunks, $params = array()) {
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
    
    static function objectFromType($result_type, $request_id = NULL, $fulfill_with = NULL) {
        $of_class = NULL;
        if ($fulfill_with) {
            $fulfill_with = _json_decode($fulfill_with);
        }
        if (preg_match('/:/', $result_type)) {
            // an action endpoint will return a hash where the object
            // we're after is in one of its properties, rather than
            // returning the object directly 
            list($property, $class) = explode(':', $result_type);
        } elseif (preg_match('/(.*)<([^>]+)>/', $result_type, $matches)) {
            $class = $matches[1];
            $of_class = $matches[2];
        } elseif ($fulfill_with && $fulfill_with->objectType) {
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
    
    function addRequest($method, $path_chunks, $params, $content, $result_type) {
        $url = self::makeUrl($path_chunks, $params);
        $request_id = $this->batch->addRequest($method, $url, NULL, $content);
        $promise = self::objectFromType($result_type, $request_id);
        array_push($this->requests, $promise);
        array_push($this->result_types, $result_type);
        return $promise;
    }
    
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
        foreach ($responses as $response) {
            $index = $response->getHeader('Multipart-Request-ID')-1;
            if (!$response->isSuccess()) {
                throw new TPException($response, $this->batch->getRequest($index));
            }
            $this->requests[$index]->fulfill(_json_decode($response->getContent()), $this->result_types[$index]);
        }
        $this->batch = NULL;
    }
    
    function get($path_chunks, $params, $result_type) {
        if ($this->batch) {
            return $this->addRequest('GET', $path_chunks, $params, NULL, $result_type);
        }
        $request = new HttpRequest('GET', self::makeUrl($path_chunks, $params));
        if (TP_CONSUMER_KEY) {
            self::prepRequest($request);
        }
        $response = $request->getResponse();
        try {
            $result = $this->resultOrError($request, $response, $result_type);
        } catch (TPException $e) {
            throw $e;
        }
        return $result;
    }
    
    function post($path_chunks, $content, $result_type) {
        return $this->_postPut('POST', $path_chunks, $content, $result_type);
    }

    function put($path_chunks, $content, $result_type) {
        return $this->_postPut('PUT', $path_chunks, $content, $result_type);
    }

    function _postPut($method, $path_chunks, $content, $result_type) {
        if (is_a($content, 'TPObject')) {
            $content = $content->asPayload();
        } elseif (is_object($content)) {
            $content = _json_decode($content);
        }
        if ($this->batch) {
            return $this->addRequest($method, $path_chunks, NULL, $content, $result_type);
        }
        $request = new HttpRequest($method, self::makeUrl($path_chunks));
        $request->setContent($content);
        $request->setHeader('Content-type', 'application/json');
        self::prepRequest($request);
        $response = $request->getResponse();
        try {
            $result = $this->resultOrError($request, $response, $result_type);
        } catch (TPException $e) {
            throw $e;
        }
        return $result;
    }
    
    function delete($path_chunks, $result_type) {
        if ($this->batch) {
            return $this->addRequest('DELETE', $path_chunks, NULL, NULL, $result_type);
        }
        $request = new HttpRequest('DELETE', self::makeUrl($path_chunks));
        self::prepRequest($request);
        $response = $request->getResponse();
        try {
            $result = $this->resultOrError($request, $response, $result_type);
        } catch (TPException $e) {
            throw $e;
        }
        return $result;
    }
    
    function lastResponse() {
        return $this->last_response;
    }
    
    function resultOrError($request, $response, $result_type) {
        $this->last_response = $response;
        if (!$response->isError()) {
            return self::objectFromType($result_type, NULL, $response->getContent());
        } else {
            throw new TPException($response, $request);
        }
    }
    
}

class TPPromise {

    public $request_id;
    protected $data;
    protected $fulfilled;
    
    function __construct($data = NULL) {
        if (!$data) {
            // called manually to make an object to post back to the API
            $this->data = new stdClass();
        } elseif (is_object($data)) {
            $this->fulfill($data);
        } else {
            $this->request_id = $data;
        }
    }
    
    function fulfill($data, $result_type = NULL) {
        if ($result_type && preg_match('/:/', $result_type)) {
            list($property, $result_type) = explode(':', $result_type);
            $data = $data[$property];
        }
        $this->data = $data;
        $this->fulfilled = true;
    }
    
    function isFulfilled() {
        return $this->fulfilled ? true : false;
    }
    
}

class TPObject extends TPPromise {

    protected static $properties;
    
    function get($name, $properties) {
        if (TP_STRICT_PROPERTIES) {
            if (!isset($properties[$name])) return throwPropertyNotice($name);
        }
        return isset($this->data->$name) ? $this->data->$name : NULL;
    }
    
    function set($name, $value, $properties) {
        if (TP_STRICT_PROPERTIES) {
            if (!isset($properties[$name])) return throwPropertyNotice($name);
        }
        $this->data->$name = $value;
        return $this->data->$name;
    }
    
    static function memberAsPayload($member) {
        if (is_object($member) && is_a($member, 'TPObject')) {
            return $member->asPayload(NULL, 0);
        } else {
            return $member;
        }
    }
    
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
        return $want_json ? json_encode($obj) : $obj;
    }
    
    function reclass() {
        if ($this->data->objectType) {
            $class = 'TP' . $this->data->objectType;
            return new $class($this->data);
        }
        return $this;
    }
    
}

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

class TPNoun {

    protected $typepad;
    
    function __construct($typepad) {
        $this->typepad = $typepad ? $typepad : new TypePad;
    }
}

class TPException extends Exception {

    protected $response;
    protected $request;
    
    function __construct($response, $request = NULL) {
        parent::__construct($response->getMessage(), $response->getCode());
        $this->response = $response;
        $this->request = $request;
    }
    
    function getResponse() {
        return $this->response;
    }

    function getRequest() {
        return $this->request;
    }
}

?>