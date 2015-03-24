<?php

namespace mangeld\obj;

class User
{
  private $name = '';
  private $uid;
  private $isAdmin = false;
  private $email = '';

  public function __construct()
  {

  }

  public function getEmail()
  {
    if( !$this->email )
      throw new \mangeld\exceptions\AttributeNotSetException("You have to provide an email first", 1);
      
    return $this->email;
  }

  public function setEmail($email) { $this->email = $email; }

  public function setAdmin($admin)
  {
    if( gettype($admin) != 'boolean' )
      throw new \mangeld\exceptions\InvalidArgumentTypeException();
      
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
    if( !$this->name )
      throw new \mangeld\exceptions\AttributeNotSetException("You must set the name first", 1);
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }
}