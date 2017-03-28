<?php

namespace PowerZAP\Api;

use Exception;

class ResponseException extends Exception
{
    private $response;

    public function __construct($message = '', $response, $code = 0)
    {
        parent::__construct($message, $code, null);
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}