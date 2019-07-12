<?php

namespace Sdrockdev\Connections;

class ConnectEntry
{
    protected $data;
    protected $sourceID;
    protected $callbackUrl;

    public function __construct(array $data, int $sourceID, $callbackUrl=null)
    {
        if ( isset($callbackUrl) && ! filter_var($callbackUrl, FILTER_VALIDATE_URL) ) {
            throw new \InvalidArgumentException($callbackUrl . ' is not a valid url');
        }

        $this->data        = $data;
        $this->sourceID    = $sourceID;
        $this->callbackUrl = $callbackUrl;
    }

    public function build()
    {
        $result = [
            'data'      => json_encode($this->data),
            'source_id' => $this->sourceID,
        ];

        if ( ! is_null($this->callbackUrl) ) {
            $result['callback_url'] = $this->callbackUrl;
        }

        return (object)$result;
    }

}
