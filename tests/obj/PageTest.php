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

  public function testPageFactoryWithUserReturnsValidTypes()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');

    $this->assertInstanceOf('\mangeld\obj\Page', $page);
    $this->assertAttributeInstanceOf(
      '\mangeld\lib\StringValidator',
      'validator',
      $page);
    $this->assertAttributeInstanceOf(
      '\mangeld\obj\User',
      'owner',
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

  public function testGetterSetterTitle()
  {
    $title = 'cool title';
    $this->page->setTitle($title);
    $result = $this->page->getTitle();

    $this->assertEquals($title, $result);
  }

  public function testGetterSetterLogoId()
  {
    $id = 'e5de8b5c-080b-4609-a749-754a45284dba';
    $this->page->setLogoId($id);
    $result = $this->page->getLogoId();

    $this->assertEquals($id, $result);
  }

  /**
   * @expectedException \mangeld\exceptions\MalformatedStringException
   */
  public function testUuidMalformedStringExceptionOnSetLogoId()
  {
    $id = 'alskjdalsdjalsjd';
    $this->page->setLogoId($id);
  }

  public function testGetterSetterDescription()
  {
    $description = 'cool description';
    $this->page->setDescription($description);
    $result = $this->page->getDescription();

    $this->assertEquals($description, $result);
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

  public function testPageReferences()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('testPageReference@reference.io');
    $card = \mangeld\obj\Card::createEmptyCard();
    $page->addCard( $card );

    $this->assertEquals( $card->getPage(), $page);
  }

  public function testCardIsAddedAndRetrieved()
  {
    $card = \mangeld\obj\Card::createEmptyCard();
    $this->page->addCard( $card );
    $result = $this->page->getCard( $card->getId() );

    $this->assertEquals( $card, $result );
  }
}
