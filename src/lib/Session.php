<?php

namespace mangeld\lib;

class Session
{

  private $creationTimestamp;

  public function __construct()
  {
      //code...
  }

  public static function factoryNewSession()
  {
    $session = new \mangeld\lib\Session();
    $session->creationTimestamp = microtime(true);
    return $session;
  }

  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
}