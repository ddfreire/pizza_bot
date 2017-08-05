<?php

require '../vendor/autoload.php';

use PowerZAP\Webhook\Request;
use PowerZAP\Api\Client;

class ExampleWebhook {

    const API_TOKEN = '2a807eb2c319aebcbb072a1d4e9aeaf1';
    const WEBHOOK_TOKEN = '68fe7828025d4f613c34b37ff48f4e71';

    public static function main()
    {
        // Webhook token
        Request::setValidationToken(self::WEBHOOK_TOKEN);

        //Api token
        Client::setAccessKey(self::API_TOKEN);

        Request::parseRun(function ($data) {

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
                            self::$key($data);
                        }
                    }
                }
            }

        });
    }

    protected static function receivedMessage($data)
    {
        if($data->body == 'powerzap') {

            //send text
            $client = new Client();
            $client->setMethod(Client::HTTP_POST);
            $client->setEndpoint('chats/' . $data->chat->id .'/messages');
            $client->setBody([
                'text' => 'Webhook, its working...'
            ]);
            $client->send();

            //send file
            $client = new Client();
            $client->setMethod(Client::HTTP_POST);
            $client->setEndpoint('chats/' . $data->chat->id .'/messages');
            $client->setBody([
                'file' => 'https://app.powerzap.com.br/img/photo-company/5679fb39fbbab926567d99287f7b049a.png'
            ]);
            $client->send();

        } else {
            echo $data->body;
        }
    }

}

ExampleWebhook::main();