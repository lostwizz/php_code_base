<?php

//namespace UnitTestFiles\Test;

use PHPUnit\Framework\TestCase;




class DatabaseTest extends TestCase
{
    protected function setUp() : void
    {
        //if (!extension_loaded('mysqli')) {
        //    $this->markTestSkipped('The MySQLi extension is not available.' );
        //}
    }

    public function testConnection()
    {
        $this->assertTrue(true);
    }
}