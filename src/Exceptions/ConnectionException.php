<?php

namespace Sdrockdev\Connections\Exceptions;

class ConnectionException extends \Exception
{
    public static function forRecord($url, $response, $code)
    {
        $result = $url . ' returned a ' . $code . ' response.';
        $json = $response->json();
        if ( ! $json ) {
            return new static($result);
        }
        $message = $response->json()['message'] ?? '';
        $errors  = implode(',', $response->json()['data']['errors'] ?? '');
        return new static($result . ' ' . $message . ' ' . $errors);
    }
}
