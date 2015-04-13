<?php

class JsonResponseTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->jsonResponse = new \mangeld\obj\JsonResponse();
  }

  public function tearDown()
  {

  }

  private function validJsonObjSchema($code)
  {
    $meta = $this->jsonResponse->getMeta();

    $this->assertInstanceOf('\mangeld\obj\JsonResponse', $this->jsonResponse);
    $this->assertInstanceOf('\StdClass', $meta);
    $this->assertInstanceOf('\StdClass', $this->jsonResponse->getData());
    $this->assertInternalType('int', $meta->code, 'Status code is not an int');
    $this->assertEquals($code, $meta->code);
    $this->assertInternalType('string', $meta->errorType, 'Error type is not an string');
    $this->assertInternalType('string', $meta->errorMessage, 'Error message is not an string');
  }

  public function testBasicResponseSchemaIsCreated()
  {
    $this->validJsonObjSchema(200);
  }

  public function testSetData()
  {
    $this->jsonResponse->setData(new \StdClass());
    $serialized = $this->jsonResponse->jsonSerialize();
    $jsonObj = json_decode($serialized);

    $this->assertInstanceOf('\StdClass', $jsonObj);
  }

  public function testBasicSchemaIsSerialized()
  {
    $json = $this->jsonResponse->jsonSerialize();
    $deserialized = json_decode($json);

    $this->assertInternalType('int', $deserialized->meta->code, var_export($deserialized, true));
    $this->assertInternalType('string', $deserialized->meta->errorType, var_export($deserialized, true));
    $this->assertInternalType('string', $deserialized->meta->errorMessage, var_export($deserialized, true));
    $this->assertInstanceOf('\StdClass', $deserialized->data, var_export($deserialized, true));
  }

  public function testBadResponseJsonSchemaIsCreated()
  {
    $this->jsonResponse = \mangeld\obj\JsonResponse::badRequestFactory();

    $this->validJsonObjSchema(400);
  }

  public function testResourceCreatedJsonSchemaIsCreated()
  {
    $this->jsonResponse = \mangeld\obj\JsonResponse::resourceCreatedFactory();

    $this->validJsonObjSchema(201);
  }

  public function testStringIsReturnedOnSerialize()
  {
    $json = $this->jsonResponse->jsonSerialize();

    $this->assertInternalType('string', $json, 'Serialized json is not a string');
  }
}