<?php

class UserTest extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		$this->user = new \mangeld\obj\User();
	}

	/**
	 * @expectedException \mangeld\exceptions\InvalidArgumentTypeException
	 */
	public function testSetRegistrationDateInvalidTypeException()
	{
		$this->user->setRegistrationDateTimestamp('asdasdasd');
	}

	/**
	 * @expectedException \mangeld\exceptions\AttributeNotSetException
	 */
	public function testRegistrationDateNotSetException()
	{
		$this->user->getResitrationDateTimestamp();
	}

	public function testGetterSetterUnixTimestampRegistrationDate()
	{
		$time = microtime(true);
		$this->user->setRegistrationDateTimestamp($time);
		$result = $this->user->getResitrationDateTimestamp();

		$this->assertEquals($time, $result);
	}

	public function testGetterSetterPasswordHash()
	{
		$passwordH = '$2y$11$vbwl3WlCAy3WwixoSPwYuOLGZ9GmssMosRuQQXZ.B8y.c4TyDV4U.';
		$this->user->setPasswordHash($passwordH);
		$result = $this->user->getPasswordHash();

		$this->assertEquals($passwordH, $result);
	}

	/**
	 * @expectedException \mangeld\exceptions\AttributeNotSetException
	 */
	public function testPasswordHashAttributeNotSetException()
	{
		$this->user->getPasswordHash();
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