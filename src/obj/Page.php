<?php

namespace mangeld\obj;

class Page
{
  private $name = '';
  private $uuid = '';
  private $creationTimestamp = 0.0;
  private $validator;

  public function __construct(
    \mangeld\lib\StringValidator $validator = null)
  {
      $this->validator = $validator;
  }

  public static function createPage()
  {
    $val = new \mangeld\lib\StringValidator();
    return new \mangeld\obj\Page($val);
  }

  private function validateUuid($id)
  {
    if( !$this->validator )
      throw new \mangeld\exceptions\DependencyNotGivenException();

    $ok = $this->validator->validateUuid4($id);
    if( !$ok )
      throw new \mangeld\exceptions\MalformatedStringException();
  }

  private function attrIsSet($arg)
  {
    if( !$arg )
      throw new \mangeld\exceptions\AttributeNotSetException();
  }

  private function validateArgumentType($argument, $type)
  {
    if( gettype($argument) != $type )
      throw new \mangeld\exceptions\InvalidArgumentTypeException();
  }

  public function setCreationTimestamp($timestamp)
  {
    $this->validateArgumentType($timestamp, 'double');
    $this->creationTimestamp = $timestamp;
  }

  public function getCreationTimestamp()
  {
    $this->attrIsSet( $this->creationTimestamp );
    return $this->creationTimestamp;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    $this->attrIsSet( $this->name );
    return $this->name;
  }

  public function setId($id)
  {
    $this->validateArgumentType($id, 'string');
    $this->validateUuid($id);
    $this->uuid = $id;
  }

  public function getId()
  {
    $this->attrIsSet( $this->uuid );
    return $this->uuid;
  }
}