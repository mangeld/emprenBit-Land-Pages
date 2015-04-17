<?php

class DBTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var \mangeld\db\DB
   */
  public $db;

	public function setUp()
	{
		$this->db = new \mangeld\db\DB();
	}

	public function testPageIsSaved()
	{
		$page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');
		$page->setName('Test Page Is Saved');
		$page->getId();
		$status = $this->db->savePage($page);

		$this->assertTrue($status);
	}

  public function testUserIsSaved()
  {
    $user = \mangeld\obj\User::createUser();
    $user->setEmail('test@test.com');
    $status = $this->db->saveUser($user);

    $this->assertNotFalse($status);
  }

  public function testUserIsFetched()
  {
    $user = \mangeld\obj\User::createUser();
    $user->setEmail('user@fetched.com');
    $id = $user->getUuid();
    $this->db->saveUser($user);
    $result = $this->db->fetchUser($id);

    $this->assertNotFalse($result);
    $this->assertEquals($id, $result->getUuid());
  }

  public function testPageWithNewUserIsSaved()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');
    $page->setName('Page with new user is saved');

    $this->db->savePage($page);
    $result = $this->db->fetchPage($page->getId());

    $this->assertAttributeInstanceOf('\mangeld\obj\User', 'owner', $result);
  }

	public function testPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');
		$page->setName('Test Page Is Fetched');
		$this->db->savePage($page);
		$result = $this->db->fetchPages();

		$this->assertGreaterThan(0, count($result));
	}

  public function testFullPageIsSavedAndRetrieved()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('juanito@juan.com');
    $page->setName("Nombre full");
    $page->setTitle('Title test');
    $page->setDescription('cool description');
    $page->setLogoId('4f65bb11-d1fb-41b9-8c53-55b05b3c7202');
    $this->db->savePage($page);
    $result = $this->db->fetchPage($page->getId());

    $this->assertEquals($page, $result, '', .0001);
  }

	public function testPageIsDeleted()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Page to delete');
		$uid = $page->getId();
		$this->db->savePage($page);
		$status = $this->db->deletePage($page);
		$result = $this->db->fetchPage($uid);

		$this->assertEquals(true, $status);
		$this->assertEquals(false, $result);
	}

	public function testPageIdIsDeleted()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Page to delete');
		$uid = $page->getId();
		$this->db->savePage($page);
		$status = $this->db->deletePage($page->getId());
		$result = $this->db->fetchPage($uid);

		$this->assertEquals(true, $status);
		$this->assertEquals(false, $result);
	}

	public function testSpecificPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('testSpecificPageIsFetched');
		$uid = $page->getId();
		$this->db->savePage($page);
		$result = $this->db->fetchPage($uid);

		$this->assertInstanceOf('\mangeld\obj\Page', $result);
		$this->assertEquals($uid, $result->getId());
	}
}