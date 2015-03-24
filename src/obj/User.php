<?php

namespace mangeld\obj;

class User
{
  private $name = "";

  public function __construct()
  {

  }

  public function getName()
  {
    if( !$this->name )
      throw new \mangeld\exceptions\NameNotSetException("You must set the name first", 1);
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }
}