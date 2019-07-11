<?php

namespace Sdrockdev\Connections;

use Illuminate\Support\Facades\Facade;

class ConnectionsFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'connections';
    }
}
