<?php

class AppTest extends PHPUnit_Framework_TestCase
{

  public function setUp()
  {
    $this->uuid4Obj = \Rhumsaa\Uuid\Uuid::uuid4();
    $this->app = new \mangeld\App();
  }

	public function testGetName()
	{
		$this->app = new \mangeld\App();
		$this->app->setName("test");

		$this->assertEquals('test', $this->app->getName());
	}

  public function testListOfPagesIsRequested()
  {
    $db = $this->getMockBuilder('DB')
      ->setMethods( array('fetchPages') )
      ->getMock();

    $db->expects($this->once())
      ->method('fetchPages');

    $this->app = new \mangeld\App($db, $this->uuid4Obj);
    $this->app->getPages();
  }

  public function testNewPageIsAdded()
  {
    $db = $this->getMockBuilder('DB')
      ->setMethods( array('savePage') )
      ->getMock();

    $db->expects($this->once())
      ->method('savePage')
      ->with($this->isInstanceOf('\mangeld\obj\Page'));

    $this->app = new \mangeld\App($db, $this->uuid4Obj);
    $obj = new \StdClass();
    $obj->name = 'name';
    $obj->email = 'test@test.com';
    $this->app->createPage($obj);
  }

  public function testPageIsRequested()
  {

  }

}