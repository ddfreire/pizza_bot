<?php

    use \PowerZAP\Api\Client;

    class Events
    {

        public static function receivedMessage($data)
        {

            PizzaBot::setContextId($data->chat->id);
            $state = State::getState();

            $result = PizzaBot::getWatson()->sendMessage($data->body, (empty($state['state_context']) ? null : json_decode($state['state_context'], 1)));

            if(count($result['intents']) < 1
                || $result['intents'][0]['confidence'] <= 0.5
                || !Process::main($result['intents'][0]['intent'], $result['entities'])) {
                if(count($result['output']) > 0 && !empty($result['output']['text'][0])) {
                    $client = new Client();
                    $client->setMethod(Client::HTTP_POST);
                    $client->setEndpoint('chats/' . PizzaBot::getContextId() . '/messages');
                    $client->setBody([
                        'text' => $result['output']['text'][0]
                    ]);
                    $client->send();
                } else {
                    Process::main('any_thing', []);
                }
            }

            State::save(PizzaBot::getContextId(), [
                'context' => json_encode($result['context'], 1)
            ]);
        }

    }