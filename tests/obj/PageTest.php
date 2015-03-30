<?php

class PageTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $validator = new \mangeld\lib\StringValidator();
    $this->page = new \mangeld\obj\Page($validator);
  }

  public function tearDown()
  {

  }

  public function testPageFactoryReturnsValidTypes()
  {
    $page = \mangeld\obj\Page::createPage();

    $this->assertInstanceOf('\mangeld\obj\Page', $page);
    $this->assertAttributeInstanceOf(
      '\mangeld\lib\StringValidator',
      'validator',
      $page);
  }

  public function testGetterSetterName()
  {
    $name = 'Name';
    $this->page->setName($name);
    $result = $this->page->getName();

    $this->assertEquals($name, $result);
  }

  /**
   * @expectedException \mangeld\exceptions\InvalidArgumentTypeException
   */
  public function testInvalidTypeExceptionSetId()
  {
    $this->page->setId(1231);
  }

  /**
   * @expectedException \mangeld\exceptions\AttributeNotSetException
   */
  public function testGetNameNotSetException()
  {
    $this->page->getName();
  }

  /**
   * @expectedException \mangeld\exceptions\AttributeNotSetException
   */
  public function testGetIdNotSetException()
  {
    $this->page->getId();
  }

  /**
   * @expectedException \mangeld\exceptions\AttributeNotSetException
   */
  public function testGetCreationTimestampNotSetException()
  {
    $this->page->getCreationTimestamp();
  }

  /**
   * @expectedException \mangeld\exceptions\InvalidArgumentTypeException
   */
  public function testCreationDateExceptionIfNotDouble()
  {
    $this->page->setCreationTimestamp('asd');
  }

  public function testGetterSetterId()
  {
    $uuid = '5f86a97a-0160-44a5-a64b-d70b77104bc9';
    $this->page->setId($uuid);
    $result = $this->page->getId();

    $this->assertEquals($uuid, $result);
  }

  public function testGetterSetterTimestamp()
  {
    $timestamp = microtime(true);
    $this->page->setCreationTimestamp($timestamp);
    $result = $this->page->getCreationTimestamp();

    $this->assertEquals($timestamp, $result);
    $this->assertEquals('double', gettype($result));
  }

  /**
   * @expectedException \mangeld\exceptions\DependencyNotGivenException
   */
  public function testDependencyNotGivenExceptionIfNotValidator()
  {
    $badPage = new \mangeld\obj\Page();
    $badPage->setId('5f86a97a-0160-44a5-a64b-d70b77104bc9');
  }

  public function testValidUuidNotRaisesException()
  {
    $this->page->setId('5f86a97a-0160-44a5-a64b-d70b77104bc9');
  }

  /**
   * @expectedException \mangeld\exceptions\MalformatedStringException
   */
  public function testUuidMalformedStringException()
  {
    $this->page->setId('asdq3234');
  }
}
