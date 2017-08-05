<?php

require_once 'vendor/autoload.php';

require_once 'configs.php';
require_once 'watson.php';
require_once 'events.php';
require_once 'state.php';
require_once 'process.php';

use PowerZAP\Webhook\Request;
use PowerZAP\Api\Client;
use \Watson\Watson;

class PizzaBot
{

    private static $watson;
    private static $db;
    private static $contextId;

    public static function main()
    {

        // define credentials powerzap webhook
        Request::setValidationToken(WEBHOOK_TOKEN);

        // define credentials powerzap api
        Client::setAccessKey(API_TOKEN);

        // instance Watson
        self::$watson = new Watson(WATSON_WORKSPACE, WATSON_USERNAME, WATSON_PASSWORD);

        // instance PDO
        self::$db = new PDO(('mysql:host=' . DB_HOST . ';dbname=' . DB_SCHEMA), DB_USER, DB_PASS,
            array(PDO::ATTR_PERSISTENT => true));

        Request::parseRun(function ($data) {

            file_put_contents((dirname(__FILE__) . '/log.txt'), (json_encode($data, 1) . "\r\n"), FILE_APPEND);

            //Valid token
            if(isset($data->validToken) && $data->validToken == true) {
                echo $data->token;
                return true;
            }

            // Do what you want.
            if(isset($data->messages)) {
                foreach($data->messages as $key => $message) {
                    if(method_exists(__CLASS__, $key)) {
                        foreach($message as $data) {
                            Events::$key($data);
                        }
                    }
                }
            }

        });

        $client = new Client();
        $client->setMethod(Client::HTTP_POST);
        $client->setEndpoint('/chats/686600/messages');
        $client->send([
            'text' => 'não te entendi...'
        ]);
    }

    public static function getWatson()
    {
        return self::$watson;
    }


    public static function getDb()
    {
        return self::$db;
    }

    public static function getContextId()
    {
        return self::$contextId;
    }

    public static function setContextId($contextId)
    {
        self::$contextId = $contextId;
    }

}

PizzaBot::main();