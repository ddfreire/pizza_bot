<?php

namespace PowerZAP\Api;

use Exception;
use PowerZAP\Constants;

class Client
{
    private static $apiKey;
    private static $apiVersion = 'v2';

    const HTTP_GET = 1;
    const HTTP_POST = 3;
    const HTTP_PUT = 4;
    const HTTP_DELETE = 5;

    private $ch;
    private $requestHeaders;
    private $timeout = 30;

    /**
     * Client constructor.
     */
    function __construct($url = null)
    {
        $this->requestHeaders = [
            'X-Authorization: Bearer ' . self::$apiKey,
            'Cache-Control: no-cache',
            'Content-Type: application/json'
        ];

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
    }

    /**
     * Switches between all available methods on this API protocol.
     * @param $method
     * @return $this
     * @throws Exception
     */
    public function setMethod($method)
    {
        switch ($method) {
            case self::HTTP_GET: curl_setopt($this->ch, CURLOPT_HTTPGET, true); break;
            case self::HTTP_POST: curl_setopt($this->ch, CURLOPT_POST, true); break;
            case self::HTTP_PUT: curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT'); break;
            case self::HTTP_DELETE: curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); break;
            default: throw new Exception('Usage of invalid HTTP method "' . $method . '"');
        }

        return $this;
    }

    /**
     * Sets an endpoint according to the API documentation.
     * @param $fragment
     * @param null $id
     * @return $this
     */
    public function setEndpoint($fragment, $id = null)
    {
        curl_setopt($this->ch, CURLOPT_URL, Constants::API_URL . '/' . self::$apiVersion . '/' . $fragment . (!is_null($id) ? '/' . $id : ''));
        return $this;
    }

    /**
     * Sets the request body
     * @param array $body
     * @return $this
     */
    public function setBody(array $body)
    {
        $data = json_encode($body);
        $this->requestHeaders[] = 'Content-Length: ' . strlen($data);

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    /**
     * Performs the prepared request.
     * @return \stdClass
     * @throws Exception
     */
    public function send()
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->requestHeaders);

        $output = curl_exec($this->ch);
        $headerSize = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $curlError  = curl_error($this->ch);

        $header = substr($output, 0, $headerSize);
        $body = substr($output, $headerSize);

        curl_close($this->ch);

        if($httpCode == 0) {
            throw new ResponseException($curlError);
        }

        if ($httpCode >= 400) {
            throw new ResponseException('Unexpected HTTP response: ' . $httpCode . ' => ' . $body, $body, $httpCode);
        }

        $preHeaders = array_filter(array_map('trim', explode("\n", $header)));
        array_shift($preHeaders);

        $headers = [];
        array_map(function ($el) use (&$headers) {
            $exp = explode(': ', $el, 2);
            $headers[$exp[0]] = $exp[1];
        }, $preHeaders);

        $return = new \stdClass;
        $return->body = json_decode($body);
        $return->statusCode = $httpCode;
        $return->headers = $headers;

        return $return;
    }

    /**
     * This key will be use to authorize all requests.
     * @param $key
     */
    public static function setAccessKey($key)
    {
        self::$apiKey = $key;
    }

    /**
     * Sets the API version.
     * @param $version
     */
    public static function setApiVersion($version)
    {
        self::$apiVersion = trim($version, '/');
    }
}