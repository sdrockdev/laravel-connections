<?php

use PHPUnit\Framework\TestCase;
use Sdrockdev\Connections\Connection;
use Sdrockdev\Connections\ConnectEntry;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
#use Sdrockdev\Connections\Exceptions\ConnectionException400;
#use Sdrockdev\Connections\Exceptions\ConnectionException500;

class ConnectionTest extends PHPUnit\Framework\TestCase
{
    static function setUpBeforeClass()
    {
        ConnectionServer::start();
    }

    public function url($url)
    {
        return 'http://localhost:' . getenv('TEST_SERVER_PORT') . '/' . ltrim($url, '/');
    }

    /** @test */
    public function it_can_be_instantiated_with_a_valid_url()
    {
        $connection = new Connection('https://www.google.com');
        $this->assertInstanceOf(Connection::class, $connection);
    }

    /** @test */
    public function it_throws_an_exception_when_instantiating_with_an_invalid_url()
    {
        $this->expectException(InvalidArgumentException::class);
        $connection = new Connection('thisisnotanactualurl');
    }

    /** @test */
    public function it_can_record_a_new_connect_entry()
    {
        $connection = new Connection($this->url('connect-success'));
        $response = $connection->record(new ConnectEntry([
            'name'  => 'Nick Turrietta',
            'email' => 'nick.turrietta@sdrock.com',
        ], 'source_key_here'));
        $body = $response->getBody();
        $json = json_decode($body, true);
        $this->assertEquals(201, $json['code']);
        $this->assertEquals('source_key_here', $json['data']['item']['source_key']);
        $this->assertEquals('Nick Turrietta', json_decode($json['data']['item']['data'], true)['name']);
    }


    /** @test */
    public function it_throws_a_client_exception_if_url_does_not_exist()
    {
        $this->expectException(ClientException::class);
        $connection = new Connection($this->url('thisurldoesnotexist'));
        $connection->record(new ConnectEntry([
            'name'  => 'Nick Turrietta',
            'email' => 'nick.turrietta@sdrock.com',
        ], 1));
    }

    /** @test */
    public function it_throws_a_server_exception_if_there_is_an_error_on_the_api_server()
    {
        $this->expectException(ServerException::class);
        $connection = new Connection($this->url('connect-server-will-fail'));
        $connection->record(new ConnectEntry([
            'name'  => 'Nick Turrietta',
            'email' => 'nick.turrietta@sdrock.com',
        ], 1));
    }

    /** @test */
    public function it_throws_a_client_exception_if_the_source_id_does_not_exist()
    {
        $this->expectException(ClientException::class);
        $connection = new Connection($this->url('connect-source-id-does-not-exist'));
        $response = $connection->record(new ConnectEntry([
            'name'  => 'Nick Turrietta',
            'email' => 'nick.turrietta@sdrock.com',
        ], 999999999));
        $body = $response->getBody();
        $json = json_decode($body, true);
        $this->assertEquals(422, $json['code']);
    }



    // This is needed because there is a problem with phpunit output buffering when
    // running the background process
    // https://stackoverflow.com/questions/6378845/phpunit-problem-no-error-messages
    protected function _debug($text)
    {
        print_r($text);
        flush();
        ob_flush();
    }


}



class ConnectionServer
{
    public static function start()
    {
        if ( static::platform() == 'Windows' ) {
            static::startOnWindows();
        }

        else {
            static::startOnLinux();
        }
    }

    public static function startOnWindows()
    {
        $cmd = 'start /B php -S ' . 'localhost:' . getenv('TEST_SERVER_PORT') .
            ' -t ./tests/server/public > NUL 2>&1';

        pclose(popen($cmd, 'r'));

        register_shutdown_function(function() {
            exec('taskkill /F /IM "php.exe"');
        });
    }

    public static function startOnLinux()
    {
        $cmd = 'php -S ' . 'localhost:' . getenv('TEST_SERVER_PORT') .
            ' -t ./tests/server/public > /dev/null' . ' 2>&1 & echo $!';

        $pid = exec($cmd);

        while (@file_get_contents('http://localhost:' . getenv('TEST_SERVER_PORT') . '/get') === false) {
            usleep(1000);
        }

        register_shutdown_function(function () use ($pid) {
            exec('kill ' . $pid);
        });
    }

    public static function platform()
    {
        if ( strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ) {
            return 'Windows';
        }

        return 'Linux';
    }
}



