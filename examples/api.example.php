<?php

require '../vendor/autoload.php';

use PowerZAP\Api\Client;

// Setup
Client::setAccessKey('904e1c1ec817b5f7837fd58c500d612c');

// Gets the entire collection
$client = new Client;
$client->setMethod(Client::HTTP_GET);
$client->setEndpoint('agents');
$response = $client->send();
var_dump($response->body, $response->statusCode); // Also we can get the response status code.

// Gets a single resource
$client = new Client;
$client->setMethod(Client::HTTP_GET);
$client->setEndpoint('agents', $response->body[0]->id);
$response = $client->send();
var_dump($response->body);

// Updates a single resource
$client = new Client;
$client->setMethod(Client::HTTP_PUT);
$client->setEndpoint('agents', $response->body->id);
$client->setBody([
    'name' => 'Morten Harket',
    'login' => 'morten'
]);
$client->send(); // Empty response

