<?php

namespace Sdrockdev\Connections;

use Sdrockdev\Connections\Exceptions\ConnectionException400;
use Sdrockdev\Connections\Exceptions\ConnectionException500;
use Zttp\Zttp;

class Connection
{
    protected $url;
    protected $authorizationHeader;

    public function __construct($url) {
        if ( ! filter_var($url, FILTER_VALIDATE_URL) ) {
            throw new \InvalidArgumentException($url . ' is not a valid url');
        }
        $this->url = $url;
    }

    public function setAuthorizationHeader($authorizationHeader) {
        $this->authorizationHeader = $authorizationHeader;
    }

    protected function getHeaders() {
        $result = [];
        if ( $this->authorizationHeader ) {
            $result[] = [
                'Authorization' => $this->authorizationHeader,
            ];
        }
        return $result;
    }

    public function record(ConnectEntry $entry) {
        $response = Zttp::withHeaders($this->getHeaders())
            ->post($this->url, $entry->build());

        $code = $this->getCode($response);

        if ( $code >= 400 && $code < 500 ) {
            throw new ConnectionException400($this->url . ' returned a ' . $code);
        }

        if ( $code >= 500 && $code < 600 ) {
            throw new ConnectionException500($this->url . ' returned a ' . $code);
        }

        return $response;
    }

    protected function getCode($response)
    {
       if ( ! $response ) {
           return null;
       }
       $json = $response->json();
       return $json['code'] ??
           $response->getStatusCode();
    }
}
