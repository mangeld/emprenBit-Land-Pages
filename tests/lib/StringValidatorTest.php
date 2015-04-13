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

      $this->assertTrue($result, 'Uuid 4 validation should be true');
    }

    public function testCorrectValidationEmail()
    {
      $validEmail = 'test@test.com';
      $ok = $this->validator->validateEmail($validEmail);

      $this->assertTrue($ok, 'Email validation should be true');
    }

    public function testNotValidEmail()
    {
      $falseMail = 'not an email';
      $notOk = $this->validator->validateEmail($falseMail);

      $this->assertFalse($notOk, 'Email validation should be false');
    }

    public function testNotValidUuid4()
    {
      $falseUuid4 = '3b342300-d2dc-11e4-b9d6-1681e6b88ec1';
      $result = $this->validator->validateUuid4($falseUuid4);

      $this->assertFalse($result, 'False uuid4 identified');
    }
}