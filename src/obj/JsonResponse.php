<?php

namespace mangeld\obj;

class JsonResponse
{
  private $meta;
  private $data;

  public function __construct()
  {
    $this->meta = new \StdClass();
    $this->meta->code = 200;
    $this->meta->errorType = '';
    $this->meta->errorMessage = '';

    $this->data = new \StdClass();
  }

  public static function badRequestFactory()
  {
    $jsonObj = new JsonResponse();
    $jsonObj->meta->code = 400;
    $jsonObj->meta->errorType = 'Invalid request';

    return $jsonObj;
  }

  public static function resourceCreatedFactory()
  {
    $jsonObj = new JsonResponse();
    $jsonObj->meta->code = 201;

    return $jsonObj;
  }

  public function getMeta()
  {
    return $this->meta;
  }

  public function getData()
  {
    return $this->data;
  }

  public function setData($data)
  {
    $this->data = $data;
  }

  public function jsonSerialize()
  {
    return json_encode(get_object_vars($this));
  }
}