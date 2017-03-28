<?php

namespace PowerZAP\Webhook;

class Request
{
    private static $validationToken = null;

    /**
     * Parses and run a callable if the current request is valid.
     * @param callable $callback Will be executed the current request is valid
     * @param bool $raise Defines if a caught exception will be rethrown.
     * @throws ParseException
     */
    public static function parseRun($callback, $raise = false)
    {
        try {
            $callback(self::parse());
        } catch (ParseException $e) {
            if ($raise) {
                throw $e;
            }
        }
    }

    /**
     * Simply returns the body of current PowerZAP's Webhook request if it's valid.
     * @return \stdClass
     * @throws ParseException
     */
    public static function parse()
    {
        if (is_null(self::$validationToken)) {
            throw new ParseException('You must set the Webhook validation token.');
        }

        $raw = file_get_contents('php://input');
        $content = json_decode($raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseException('Received invalid content: ' . $raw);
        }

        if (empty($content->token) || $content->token !== self::$validationToken) {
            throw new ParseException('Received invalid authorization token.');
        }

        return $content;
    }

    /**
     * Sets an validation token
     * @param $validationToken
     */
    public static function setValidationToken($validationToken)
    {
        self::$validationToken = $validationToken;
    }
}