<?php

class DBTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->db = new \mangeld\db\DB();
	}

	public function testPageIsSaved()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Test Page Is Saved');
		$uid = $page->getId();
		$status = $this->db->savePage($page);

		$this->assertEquals(1, $status);
	}

	public function testPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Test Page Is Fetched');
		$uid = $page->getId();
		$this->db->savePage($page);
		$result = $this->db->fetchPages();

		$this->assertGreaterThan(0, count($result));
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