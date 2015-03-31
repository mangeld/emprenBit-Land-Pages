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
		$page->setName('testtt');
		$uid = $page->getId();
		$status = $this->db->savePage($page);

		$this->assertEquals(1, $status);
	}

	public function testPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Test');
		$uid = $page->getId();
		$this->db->savePage($page);
		$result = $this->db->fetchPages();

		$this->assertGreaterThan(0, count($result));
	}

	public function testSpecificPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Test');
		$uid = $page->getId();
		$this->db->savePage($page);
		$result = $this->db->fetchPage($uid);

		$this->assertInstanceOf('\mangeld\obj\Page', $result);
		$this->assertEquals($uid, $result->getId());
	}
}