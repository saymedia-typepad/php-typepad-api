<?php

class HttpRequest {

    protected $method;
    protected $uri;
    protected $headers;
    protected $content;
    protected $curlopts;
    protected $raw_response;
    protected $response;

    function __construct($method = 'GET', $uri = NULL, $headers = array(), $content = NULL) {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->content = $content;
        $this->curlopts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => true
        );
        if ($method == 'DELETE') {
            $this->curlopts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        } elseif ($method != 'GET') {
            $this->curlopts[constant("CURLOPT_$method")] = true;
        }
    }
    
    function setMethod($method) {
        $this->method = $method;
    }
    
    function setUri($uri) {
        $this->uri = $uri;
    }
    
    function setHeaders($headers) {
        $this->headers = $headers;
    }
    
    function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    function setContent($content) {
        $this->content = $content;
    }

    function setCurlopts($curlopts) {
        foreach ($curlopts as $key => $value) {
            $this->curlopts[$key] = $value;
        }
    }

    function getMethod() {
        return $this->method;
    }

    function getUri() {
        return $this->uri;
    }

    function getHeaders() {
        return $this->headers;
    }
    
    function getContent() {
        return $this->content;
    }
    
    function message() {
        $msg = "{$this->method} {$this->uri} HTTP/1.0\n";
        foreach ($this->headerArray() as $header) {
            $msg .= "$header\n";
        }
        if ($this->content) {
            $msg .= "\n\n{$this->content}";
        }
        return $msg;
    }

    function headerArray() {
        $ha = array();
        foreach ($this->headers as $name => $value) {
            array_push($ha, "$name: $value");
        }
        return $ha;
    }

    function send() {
        $ch = curl_init($this->uri);
        foreach ($this->curlopts as $key => $value) {
            curl_setopt($ch, $key, $value);
        }
        if (($this->method == 'POST') || ($this->method == 'PUT')) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getContent());
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headerArray());
        $this->raw_response = curl_exec($ch);
        curl_close($ch);
    }
    
    function getResponse() {
        if ($this->response) return $this->response;
        if (!$this->raw_response) $this->send();
        $this->response = new HttpResponse($this->raw_response);
        return $this->response;
    }

}

class BatchRequest extends HttpRequest {

    protected $requests;
    protected $default_headers;
    protected $encoding;
    protected $boundary;
    
    function __construct($method = 'GET', $uri = NULL, $headers = array(), $content = NULL) {
        parent::__construct($method, $uri, $headers, $content);
        $this->requests = array();
        $this->default_headers = array();
        $this->encoding = 'quoted-printable';
        $this->boundary = "------------=_" . uniqid(time());
        $this->setHeader('Content-Type', "multipart/parallel; boundary=\"{$this->boundary}\"");
    }
    
    function setEncoding($encoding) {
        $this->encoding = $encoding;
    }

    function addRequest($method = 'GET', $uri = NULL, $headers = NULL, $content = NULL) {
        if (!$headers) $headers = array();
        foreach ($this->default_headers as $name => $value) {
            $headers[$name] = $value;
        }
        $request = new HttpRequest($method, $uri, $headers, $content);
        array_push($this->requests, $request);
        # this will end up being used as the Multipart-Request-ID
        return count($this->requests);
    }
    
    function getRequest($index) {
        return $this->requests[$index];
    }
    
    function setDefaultHeader($name, $value) {
        $this->default_headers[$name] = $value;
    }
    
    function applyHeader($name, $value) {
        foreach ($this->requests as $request) {
            $request->setHeader($name, $value);
        }
    }
    
    function getContent() {
        $content = "This is a multi-part message in MIME format...\n\n";
        $i = 0;
        foreach ($this->requests as $request) {
            $i++;
            $msg = $request->message();
            if ($this->encoding == 'base64') {
                $msg = base64_encode($msg);
            } elseif ($this->encoding == 'quoted-printable') {
                $msg = quoted_printable_encode($msg);
            }
            $content .= <<<EOB
--{$this->boundary}
Content-Type: application/http-request
Content-Disposition: inline
Content-Transfer-Encoding: {$this->encoding}
MIME-Version: 1.0
Multipart-Request-ID: $i

$msg

EOB;
        }
        $content .= "--{$this->boundary}--\n";
        return $content;
    }
    
    function getResponse() {
        if ($this->response) return $this->response;
        if (!$this->raw_response) $this->send();
        $this->response = new BatchResponse($this->raw_response);
        return $this->response;
    }

    function getResponses() {
        if ($this->response) return $this->response->getResponses();
        if (!$this->raw_response) $this->send();
        $this->response = new BatchResponse($this->raw_response);
        return $this->response->getResponses();
    }
}

class HttpResponse {

    protected $headers;
    protected $content;
    protected $code;
    protected $message;
    
    function __construct($raw_response) {
        $headers = array();
        list($head, $content) = preg_split("/\r?\n\r?\n/", $raw_response, 2);
        $i = 0;

        foreach (preg_split("/\r?\n/", $head) as $header) {
            if (++$i == 1) {
                $header = preg_replace('/^HTTP\/1.0 /', '', $header);
                list($code, $message) = explode(' ', $header, 2);
                $this->code = $code;
                $this->message = $message;
                continue;
            }
            list($name, $value) = explode(': ', $header);
            $headers[$name] = $value;
        }
        $this->headers = $headers;
        $this->content = $content;
    }
    
    function getCode() {
        return $this->code;
    }
    
    function isSuccess() {
        return preg_match('/^2\d\d$/', $this->code) ? true : false;
    }

    function isRedirect() {
        return preg_match('/^3\d\d$/', $this->code) ? true : false;
    }
    
    function isClientError() {
        return preg_match('/^4\d\d$/', $this->code) ? true : false;
    }
    
    function isServerError() {
        return preg_match('/^5\d\d$/', $this->code) ? true : false;
    }
    
    function isError() {
        return ($this->isClientError() || $this->isServerError()) ? true : false; 
    }
    
    function getMessage() {
        return $this->message;
    }
    
    function getHeaders() {
        return $this->headers;
    }
    
    function getHeader($name) {
        return $this->headers[$name];
    }
    
    function getContent() {
        return $this->content;
    }
    
    function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

}

class MimePart {

    protected $headers;
    protected $body;
    
    function __construct($raw_part) {
        $headers = array();
        list($head, $body) = explode("\n\n", $raw_part, 2);
        foreach (explode("\n", $head) as $header) {
            if (!$header) continue;
            list($name, $value) = explode(': ', $header);
            $headers[$name] = $value;
        }
        $this->headers = $headers;
        $this->body = $body;
    }
    
    function getHeaders() {
        return $this->headers;
    }
    
    function getHeader($name) {
        return $this->headers[$name];
    }
    
    function getBody() {
        return $this->body;
    }

}

class BatchResponse extends HttpResponse {

    protected $responses;
    protected $boundary;
    
    function __construct($raw_response) {
        parent::__construct($raw_response);
        if (!preg_match('/boundary="([^"]+)"/', $this->headers['Content-Type'], $matches)) {
            return;
        }
        $this->boundary = $matches[1];
        $i = 0;
        $this->responses = array();
        foreach (explode("--{$this->boundary}", $this->content) as $raw_part) {
            if (++$i == 1) continue;
            if (preg_match('/^--/', $raw_part)) continue;
            $part = new MimePart($raw_part);
            $encoding = $part->getHeader('Content-Transfer-Encoding');
            $raw_subresponse = $part->getBody();
            if ($encoding == 'base64') {
                $raw_subresponse = base64_decode($raw_subresponse);
            } elseif ($encoding == 'quoted-printable') {
                $raw_subresponse = quoted_printable_decode($raw_subresponse);
            }
            $response = new HttpResponse($raw_subresponse, 1);
            array_push($this->responses, $response);
            $response->setHeader('Multipart-Request-ID', $part->getHeader('Multipart-Request-ID'));
        }
    }
    
    function getResponses() {
        return $this->responses;
    }
}

?>
