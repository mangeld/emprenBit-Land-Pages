<?php

class UserTest extends PHPUnit_Framework_TestCase
{

	public function testName()
	{
		$user = new \mangeld\obj\User();
		$user->setName("Juan");
		$this->assertEquals("Juan", $user->getName());
	}

	/**
	 * @expectedException \mangeld\exceptions\NameNotSetException
	 */
	public function testNameNotSetException()
	{
		$user = new \mangeld\obj\User();
		$user->getName();
	}

}