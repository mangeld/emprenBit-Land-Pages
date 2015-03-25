<?php

namespace mangeld\obj;

class User
{
  private $name = '';
  private $uid = '';
  private $isAdmin = false;
  private $email = '';
  private $passWordHash = '';
  private $registrationTimestamp;

  public function __construct()
  {

  }

  private function checkIfAttributeIsSet($attr, $msg = '', $cod = 1)
  {
    if(!$attr)
      throw new \mangeld\exceptions\AttributeNotSetException($msg, $cod);
  }

  private function checkValidType($value, $expected, $msg = '', $cod = 1)
  {
    if( gettype($value) != $expected )
      throw new \mangeld\exceptions\InvalidArgumentTypeException($msg, $cod);
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

  public function setPasswordHash($hash) { $this->passWordHash = $hash; }

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
}