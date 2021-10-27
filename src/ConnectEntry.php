<?php

namespace Sdrockdev\Connections;

class ConnectEntry
{
    protected $data;
    protected $sourceKey;
    protected $callbackUrl;

    public function __construct(array $data, string $sourceKey, $callbackUrl=null)
    {
        if ( isset($callbackUrl) && ! filter_var($callbackUrl, FILTER_VALIDATE_URL) ) {
            throw new \InvalidArgumentException($callbackUrl . ' is not a valid url');
        }

        $this->data        = $data;
        $this->sourceKey   = $sourceKey;
        $this->callbackUrl = $callbackUrl;
    }

    public function build()
    {
        $result = [
            'data'       => json_encode($this->data),
            'source_key' => $this->sourceKey,
        ];

        if ( ! is_null($this->callbackUrl) ) {
            $result['callback_url'] = $this->callbackUrl;
        }

        return (object)$result;
    }

}
