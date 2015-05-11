<?php

use mangeld\lib\Logger;

class LoggerTest extends PHPUnit_Framework_TestCase
{

  public function testSingleton()
  {
    $logger = Logger::instance();
    $logger->name = 'test';
    $logger2 = Logger::instance();

    $this->assertEquals($logger->name, $logger2->name);
    Logger::close();
  }

  public function testWarning()
  {
    $logger = Logger::instance();
    $logger->warning('akjshdkajshdkaj');
    Logger::close();
  }

  public function testFormatException()
  {
    $e = new \Exception("Test Exception");
    Logger::instance()->error($e);
    Logger::close();
  }

}
