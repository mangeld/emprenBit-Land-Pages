<?php

class UserTest extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		$validator = new \mangeld\lib\StringValidator();
		$this->user = new \mangeld\obj\User($validator);
	}

	public function testUserFactoryIsCreated()
	{
		$usr = \mangeld\obj\User::factoryUser();

		$this->assertInstanceOf('\mangeld\obj\User', $usr);
		$this->assertInstanceOf('\mangeld\lib\StringValidator', $usr->getValidator());
	}

	public function testGetterSetterValidator()
	{
		$validator = new \mangeld\lib\StringValidator();
		$this->user->setValidator($validator);
		$result = $this->user->getValidator();

		$this->assertEquals($validator, $result);
	}

	/**
	 * @expectedException \mangeld\exceptions\DependencyNotGivenException
	 */
/*	public function testDependencyNotGivenExceptionIfNoStringValidatorWhenSetId()
	{
		$this->user = new \mangeld\obj\User();
		$this->user->setUuid('3e85276f-8777-4d5b-9589-7d1ff14dbf75');
	}*/

	/**
	 * @expectedException \mangeld\exceptions\DependencyNotGivenException
	 */
	public function testDependencyNotGivenExceptionIfNoStringValidatorWhenSetEmail()
	{
		$this->user = new \mangeld\obj\User();
		$this->user->setEmail('email@email.com');
	}

	/**
	 * @expectedException \mangeld\exceptions\MalformatedStringException
	 */
/*	public function testMalformatedUuidException()
	{
		$falseUuid4 = 'asdasdasd';
		$validator = new \mangeld\lib\StringValidator();
		$this->user = new \mangeld\obj\User($validator);

		$this->user->setUuid($falseUuid4);
	}*/

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

	/**
	 * @expectedException \mangeld\exceptions\MalformatedStringException
	 */
	public function testMalformatedMailException()
	{
		$this->user->setEmail('thizzz is not a email');
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
		$validator = new \mangeld\lib\StringValidator();
		$this->user = \mangeld\obj\User::createUser();
		$result = $this->user->getUuid();
		$ok = $validator->validateUuid4($result);
		$this->assertTrue($ok, 'message');
	}

	/**
	 * @expectedException \mangeld\exceptions\AttributeNotSetException
	 */
	public function testNameAttributeNotSetException()
	{
		$this->user->getName();
	}

}