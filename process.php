<?php

class Process
{

    const INTENT_REQUEST_PIZZA = 'request_pizza';

    public static function main($intent, $entities)
    {
        print_r($intent);
        switch ($intent) {
            case self::INTENT_REQUEST_PIZZA:
                echo 'Ok já sei que você que uma pizza de: ';
                foreach ($entities as $entity) {
                    if($entity['entity'] == 'sabor') {
                        echo $entity['value'] . ', ';
                    }
                }
            break;

            default:
                echo 'Não te compreendi';
        }

    }

}