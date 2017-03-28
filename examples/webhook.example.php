<?php

require '../vendor/autoload.php';

use PowerZAP\Webhook\Request;

Request::setValidationToken('ec3e72a4ea17af530a3830cca942e519');

Request::parseRun(function ($data) {
    // Do what you want.
});