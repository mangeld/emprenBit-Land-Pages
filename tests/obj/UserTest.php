<?php

class UserTest extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		$this->user = new \mangeld\obj\User();
	}

	/**
	 * @expectedException \mangeld\exceptions\AttributeNotSetException
	 */
	public function testEmailAttributeNotSetException()
	{
		$this->user->getEmail();
	}

	public function testGetterSetterEmail()
	{
		$email = 'contact@mangel.me';
		$this->user->setEmail($email);
		$this->assertEquals($email, $this->user->getEmail());
	}

	public function testGetterSetterName()
	{
		$this->user->setName("Juan");
		$this->assertEquals("Juan", $this->user->getName());
	}

	/**
	 * @expectedException \mangeld\exceptions\InvalidArgumentTypeException
	 */
	public function testIsAdminGettersSettersOnlyBoolException()
	{
		$this->user->setAdmin('true');
	}

	public function testIsAdminGettersSetters()
	{
		$this->user->setAdmin(true);
		$this->assertEquals(true, $this->user->isAdmin() );
	}

	public function testIsAdminByDefault()
	{
		$this->assertEquals(false, $this->user->isAdmin());
	}

	public function testUuid()
	{
		$uuid = '3e85276f-8777-4d5b-9589-7d1ff14dbf75';
		$this->user->setUuid($uuid);
		$result = $this->user->getUuid();

		$this->assertEquals($uuid, $result);
	}

	/**
	 * @expectedException \mangeld\exceptions\AttributeNotSetException
	 */
	public function testNameAttributeNotSetException()
	{
		$this->user->getName();
	}

}