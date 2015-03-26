<?php

class HasherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
      $this->hasher = new \mangeld\lib\Hasher();
    }

    public function tearDown()
    {

    }

    public function testPasswordHashIsCreated()
    {
      $password = '12as@#?asd.sd';
      $hash = $this->hasher->create_hash_blowfish($password);

      $this->assertNotEquals($password, $hash);
    }

    public function testEnteredPasswordEqualsHashedOne()
    {
      $password = '123@@#.12asdAAS?¿';
      $hash = $this->hasher->create_hash_blowfish($password);
      $result = $this->hasher->is_equal_blowfish($hash, $password);

      $this->assertTrue($result, 'Password checking does not equal: Expected true, got false');
    }

    public function testEnteredPasswordNotEqualsHashedOne()
    {
      $password = '123@@#.12asdAAS?¿';
      $hash = $this->hasher->create_hash_blowfish($password);
      $result = $this->hasher->is_equal_blowfish($hash, 'false|@23.21as');

      $this->assertFalse($result, 'Password checking should not be equal: Expected false, got true');
    }

    public function testHashLengthIsAlways60Char()
    {
      $password = '123@@#.12asdAAS?¿';
      $expectedSize = 60;
      $hash = $this->hasher->create_hash_blowfish($password);

      $this->assertEquals($expectedSize, strlen($hash));
    }
}