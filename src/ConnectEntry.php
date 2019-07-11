<?php

namespace Sdrockdev\Connections;

class ConnectEntry
{
    protected $data;
    protected $platformID;
    protected $callbackUrl;

    public function __construct(array $data, int $platformID, $callbackUrl=null)
    {
        if ( isset($callbackUrl) && ! filter_var($callbackUrl, FILTER_VALIDATE_URL) ) {
            throw new \InvalidArgumentException($callbackUrl . ' is not a valid url');
        }
        $this->data        = $data;
        $this->platformID  = $platformID;
        $this->callbackUrl = $callbackUrl;
    }

    public function build()
    {
        $data = $this->data;
        $data['platform_id']  = $this->platformID;
        $data['callback_url'] = $this->callbackUrl;
        return $data;
    }

}
