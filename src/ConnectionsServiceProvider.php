<?php

namespace Sdrockdev\Connections;

use Illuminate\Support\ServiceProvider;

class ConnectionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/connections.php', 'connections');

        $this->publishes([
            __DIR__.'/../config/connections.php' => config_path('connections.php'),
        ]);
    }

    public function register()
    {
        $config = config('connections');

        $service   = $config['default'];
        $url       = $config['services'][$service]['url'] ?? 'http://localhost';
        $basicAuth = $config['services'][$service]['basic_authorization'] ?? null;

        $this->app->singleton(Connection::class, function() use ($url, $basicAuth) {
            $connection = new Connection( $url );
            if ( $basicAuth ) {
                $connection->setAuthorizationHeader( 'Basic ' . $basicAuth );
            }
            return $connection;
        });

        $this->app->alias(Connection::class, 'connections');
    }
}
