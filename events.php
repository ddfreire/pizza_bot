<?php

    class Events
    {

        public static function receivedMessage($data)
        {
            PizzaBot::setContextId(1);
            $state = State::getState();

            $result = PizzaBot::getWatson()->sendMessage('Gostaria de pedir uma pizza de queijo, mussarela e calabresa', (empty($state['state_context']) ? null : json_decode($state['state_context'], 1)));

            if(count($result['intents']) > 0 && $result['intents'][0]['confidence'] >= 0.5) {
                Process::main($result['intents'][0]['intent'], $result['entities']);
            } else {
                Process::main('any_thing', []);
            }

            State::save(1, [
                'context' => json_encode($result['context'], 1)
            ]);
        }

    }