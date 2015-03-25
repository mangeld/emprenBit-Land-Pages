<?php

namespace mangeld\obj;

use mangeld\exceptions\AttributeNotSetException;
use mangeld\exceptions\InvalidArgumentTypeException;
use mangeld\exceptions\MalformatedStringException;
use mangeld\exceptions\DependencyNotGivenException;

class User
{
  private $name = '';
  private $uid = '';
  private $isAdmin = false;
  private $email = '';
  private $passWordHash = '';
  private $registrationTimestamp;

  private $strValidator;

  public function __construct(\mangeld\lib\StringValidator $validator = null)
  {
    $this->strValidator = $validator;
  }

  public static function factoryUser()
  {
    $user = new User(new \mangeld\lib\StringValidator);
    return $user;
  }

  private function checkValidUuid($uuid)
  {
    if( !$this->strValidator )
      throw new DependencyNotGivenException();

    $result = $this->strValidator->validateUuid4($uuid);

    if( !$result )
      throw new MalformatedStringException('String has to be uuid version 4 compilant', 1);
  }

  private function checkIfAttributeIsSet($attr, $msg = '', $cod = 1)
  {
    if(!$attr)
      throw new AttributeNotSetException($msg, $cod);
  }

  private function checkValidType($value, $expected, $msg = '', $cod = 1)
  {
    if( gettype($value) != $expected )
      throw new InvalidArgumentTypeException($msg, $cod);
  }

  public function setRegistrationDateTimestamp($timestamp)
  {
    $this->checkValidType($timestamp, 'double');
    $this->registrationTimestamp = $timestamp;
  }

  public function getResitrationDateTimestamp()
  {
    $this->checkIfAttributeIsSet($this->registrationTimestamp);
    return $this->registrationTimestamp;
  }

  public function setPasswordHash($hash)
    { $this->passWordHash = $hash; }

  public function getPasswordHash()
  {
    $this->checkIfAttributeIsSet($this->passWordHash, 'You have to provide a password hash first', 1);
    return $this->passWordHash;
  }

  public function getEmail()
  {
    $this->checkIfAttributeIsSet($this->email, 'You have to provide an email first');
    return $this->email;
  }

  public function setEmail($email) { $this->email = $email; }

  public function setAdmin($admin)
  {
    $this->checkValidType($admin, 'boolean');
    $this->isAdmin = $admin;
  }

  public function isAdmin() { return $this->isAdmin; }

  public function setUuid($uid)
  {
    $this->checkValidUuid($uid);
    $this->uid = $uid;
  }

  public function getUuid()
  {
    return $this->uid;
  }

  public function getName()
  {
    $this->checkIfAttributeIsSet($this->name, 'You must set the name first');
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function setValidator(\mangeld\lib\StringValidator $validator)
  {
    $this->strValidator = $validator;
  }

  public function getValidator()
  {
    $this->checkIfAttributeIsSet($this->strValidator);
    return $this->strValidator;
  }
}