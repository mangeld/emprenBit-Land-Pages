<?php

namespace mangeld;

class App
{
  private $name = "";

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
}