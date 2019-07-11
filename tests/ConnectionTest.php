<?php

use PHPUnit\Framework\TestCase;
use Sdrockdev\Connections\Connection;
use Sdrockdev\Connections\ConnectEntry;
use Sdrockdev\Connections\Exceptions\ConnectionException400;
use Sdrockdev\Connections\Exceptions\ConnectionException500;

class ConnectionTest extends PHPUnit\Framework\TestCase
{
    static function setUpBeforeClass()
    {
        ConnectionServer::start();
    }

    function url($url)
    {
        return 'http://localhost:' . getenv('TEST_SERVER_PORT') . '/' . ltrim($url, '/');
    }

    /** @test */
    function it_can_be_instantiated_with_a_valid_url()
    {
        $connection = new Connection('https://www.google.com');
        $this->assertInstanceOf(Connection::class, $connection);
    }

    /** @test */
    function it_throws_an_exception_when_instantiating_with_an_invalid_url()
    {
        $this->expectException(InvalidArgumentException::class);
        $connection = new Connection('thisisnotanactualurl');
    }

    /** @test */
    function it_can_record_a_new_connect_entry()
    {
        $connection = new Connection($this->url('connect-success'));
        $response = $connection->record(new ConnectEntry([
            'name'   => 'Nick Turrietta',
            'emails' => 'nick.turrietta@sdrock.com',
        ], 1));
        $this->assertTrue($response->isSuccess());
        $json = $response->json();
        $this->assertEquals(201, $json['code']);
        $this->assertEquals('Nick Turrietta', $json['data']['item']['name']);
    }

    /** @test */
    function it_throws_a_404_exception_if_url_does_not_exist()
    {
        $this->expectException(ConnectionException400::class);
        $connection = new Connection($this->url('thisurldoesnotexist'));
        $connection->record(new ConnectEntry([
            'name'   => 'Nick Turrietta',
            'emails' => 'nick.turrietta@sdrock.com',
        ], 1));
    }

    /** @test */
    function it_throws_a_500_exception_if_there_is_an_error_on_the_api_server()
    {
        $this->expectException(ConnectionException500::class);
        $connection = new Connection($this->url('connect-server-will-fail'));
        $connection->record(new ConnectEntry([
            'name'   => 'Nick Turrietta',
            'emails' => 'nick.turrietta@sdrock.com',
        ], 1));
    }

    /** @test */
    function it_throws_an_exception_if_the_platform_id_does_not_exist()
    {
        $this->expectException(ConnectionException400::class);
        $connection = new Connection($this->url('connect-platform-id-does-not-exist'));
        $response = $connection->record(new ConnectEntry([
            'name'   => 'Nick Turrietta',
            'emails' => 'nick.turrietta@sdrock.com',
        ], 999999999));
        $json = $response->json();
        $this->assertEquals(422, $json['code']);
    }

    // This is needed because there is a problem with phpunit output buffering when
    // running the background process
    // https://stackoverflow.com/questions/6378845/phpunit-problem-no-error-messages
    protected function debug($text)
    {
        print_r($text);
        flush();
        ob_flush();
    }


}



class ConnectionServer
{
    static function start()
    {

        if ( static::platform() == 'Windows' ) {
            static::startOnWindows();
        }

        else {
            static::startOnLinux();
        }

    }

    static function startOnWindows() {

        $cmd = 'start /B php -S ' . 'localhost:' . getenv('TEST_SERVER_PORT') .
            ' -t ./tests/server/public > NUL 2>&1';

        pclose(popen($cmd, 'r'));

        register_shutdown_function(function() {
            exec('taskkill /F /IM "php.exe"');
        });

    }

    static function startOnLinux() {
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

    static function platform() {
        if ( strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ) {
            return 'Windows';
        }
        return 'Linux';
    }
}



