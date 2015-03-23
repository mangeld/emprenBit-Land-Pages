<?php

class AppTest extends PHPUnit_Framework_TestCase
{
	public function testGetName()
	{
		$app = new \mangeld\App();
		$app->setName("test");
		$this->assertEquals('test', $app->getName());
	}
}