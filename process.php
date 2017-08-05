<?php

use \PowerZAP\Api\Client;

class Process
{

    const INTENT_REQUEST_PIZZA = 'request_pizza';

    public static function main($intent, $entities)
    {
        switch ($intent) {
            case self::INTENT_REQUEST_PIZZA:
                $txt = 'Beleza eu já sei que o sabor[es] [e] ';
                foreach ($entities as $entity) {
                    if($entity['entity'] == 'sabor') {
                        $txt = $entity['value'] . ', ';
                    }
                }
                $txt = rtrim($txt, ', ');
                if(count($entities) > 1) {
                    $txt = str_replace(['[es]', '[e]'], ['es', 'são'], $txt);
                } else {
                    $txt = str_replace(['[es]', '[e]'], ['', 'é'], $txt);
                }
                $client = new Client();
                $client->setMethod(Client::HTTP_POST);
                $client->setEndpoint('/chats/' . PizzaBot::getContextId() . '/messages');
                $client->send([
                    'text' => $txt
                ]);
            break;
            default:
                $client = new Client();
                $client->setMethod(Client::HTTP_POST);
                $client->setEndpoint('/chats/' . PizzaBot::getContextId() . '/messages');
                $client->send([
                    'text' => 'não te entendi...'
                ]);
        }

    }

}