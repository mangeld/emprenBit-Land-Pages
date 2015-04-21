<?php

namespace mangeld\obj;

class User extends DataStore
{
  private $name = '';
  private $uid = '';
  private $isAdmin = false;
  private $email = '';
  private $passWordHash = '';
  private $registrationTimestamp;

  public function __construct(\mangeld\lib\StringValidator $validator = null)
  {
    $this->validator = $validator;
  }

  public static function factoryUser()
  {
    $user = new User(new \mangeld\lib\StringValidator);
    return $user;
  }

  public static function createUserWithId($id)
  {
    $user = new User(new \mangeld\lib\StringValidator());
    $user->uid = $id;
    return $user;
  }

  public static function createUser()
  {
    $user = new User(new \mangeld\lib\StringValidator);
    $user->uid = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    $user->registrationTimestamp = microtime(true);
    return $user;
  }

  public function setRegistrationDateTimestamp($timestamp)
  {
    $this->validateArgumentType($timestamp, 'double', 'Timestamp must be double');
    $this->registrationTimestamp = $timestamp;
  }

  public function getResitrationDateTimestamp()
  {
    $this->attrIsSet($this->registrationTimestamp, 'You have to set the registration timestamp first.');
    return $this->registrationTimestamp;
  }

  public function setPasswordHash($hash)
    { $this->passWordHash = $hash; }

  public function getPasswordHash()
  {
    $this->attrIsSet($this->passWordHash, 'You have to provide a password hash first.', 1);
    return $this->passWordHash;
  }

  public function getEmail()
  {
    $this->attrIsSet($this->email, 'You have to provide an email first.');
    return $this->email;
  }

  public function setEmail($email)
  {
    $this->validateEmail($email);
    $this->email = $email;
  }

  public function setAdmin($admin)
  {
    $this->validateArgumentType($admin, 'boolean', 'Admin is not a boolean');
    $this->isAdmin = $admin;
  }

  public function isAdmin() { return $this->isAdmin; }

/*  public function setUuid($uid)
  {
    $this->validateUuid($uid);
    $this->uid = $uid;
  }*/

  public function getUuid()
  {
    return $this->uid;
  }

  public function getName()
  {
    $this->attrIsSet($this->name, 'You must set the name first');
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function setValidator(\mangeld\lib\StringValidator $validator)
  {
    $this->validator = $validator;
  }

  public function getValidator()
  {
    $this->attrIsSet($this->validator);
    return $this->validator;
  }
}