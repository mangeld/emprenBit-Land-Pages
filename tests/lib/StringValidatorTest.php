<?php

class StringValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
      $this->validator = new \mangeld\lib\StringValidator();
    }

    public function tearDown()
    {

    }

    public function testCorrectValidationUuid4()
    {
      $validUuid4 = 'de305d54-75b4-431b-adb2-eb6b9e546013';
      $result = $this->validator->validateUuid4($validUuid4);

      $this->assertTrue($result, 'Valid uuid4');
    }

    public function testNotValidUuid4()
    {
      $falseUuid4 = '3b342300-d2dc-11e4-b9d6-1681e6b88ec1';
      $result = $this->validator->validateUuid4($falseUuid4);

      $this->assertFalse($result, 'False uuid4 identified');
    }
}