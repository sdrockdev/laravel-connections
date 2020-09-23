<?php

namespace Sdrockdev\Connections;

use GuzzleHttp\Client;

class Connection
{
    protected $url;
    protected $authorizationHeader;

    public function __construct($url)
    {
        if ( ! filter_var($url, FILTER_VALIDATE_URL) ) {
            throw new \InvalidArgumentException($url . ' is not a valid url');
        }

        $this->url = $url;
    }

    public function setAuthorizationHeader($authorizationHeader)
    {
        $this->authorizationHeader = $authorizationHeader;
    }

    public function record(ConnectEntry $entry)
    {
        $client = new Client();

        return $client->post($this->url, [
            'headers' => $this->_headers(),
            'json'    => $entry->build(),
        ]);
    }

    protected function _headers()
    {
        $result = [];

        if ( $this->authorizationHeader ) {
            $result['Authorization'] = $this->authorizationHeader;
        }

        return $result;
    }
}
