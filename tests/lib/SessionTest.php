<?php

class SessionTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->session = new \mangeld\lib\Session();
  }

  public function tearDown()
  {

  }

  public function testNewSessionIsCreated()
  {
    $sess = \mangeld\lib\Session::factoryNewSession();

    $this->assertInstanceOf('\mangeld\lib\Session', $sess);
  }

  public function testNewSessionIsFilled()
  {
    $sess = \mangeld\lib\Session::factoryNewSession();

    $this->assertEquals('double', gettype($sess->getCreationTimestamp()));
  }
}