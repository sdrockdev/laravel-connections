<?php

use PHPUnit\Framework\TestCase;
use Sdrockdev\Connections\ConnectEntry;

class ConnectEntryTest extends PHPUnit\Framework\TestCase
{
    /** @test */
    function it_can_be_instantiated_with_correct_arguments() {
        $connection = new ConnectEntry([], 1);
        $this->assertInstanceOf(ConnectEntry::class, $connection);

        $connection = new ConnectEntry([], 1, 'https://www.google.com');
        $this->assertInstanceOf(ConnectEntry::class, $connection);
    }

    /** @test */
    function it_throws_an_exception_when_instantiating_with_an_invalid_url() {
        $this->expectException(InvalidArgumentException::class);
        $connection = new ConnectEntry([], 1, 'thisisnotaurl');
    }

}
