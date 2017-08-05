<?php

namespace Watson;

require_once 'vendor/autoload.php';

use \GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Watson extends Client
{
    const BASE_URL = 'https://gateway.watsonplatform.net/conversation/api/v1/';
    const API_VERSION = '2017-05-26';

    private $username;
    private $password;
    private $workspace;

    /**
     * WatsonConnector constructor.
     * @inheritdoc
     */
    public function __construct(string $workspace, string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->workspace = $workspace;

        parent::__construct([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'version' => self::API_VERSION
            ],
            'auth' => [$this->username, $this->password]
        ]);
    }

    public function sendMessage($text, $context)
    {
        return $this->response($this->post('workspaces/' . $this->workspace . '/message', [
            'json' => [
                'input' => [
                    'text' => $text
                ],
                'context' => $context
            ]
        ]));
    }

    protected function response(ResponseInterface $responseInterface): array
    {
        return json_decode($responseInterface->getBody()->getContents(), true);
    }
}