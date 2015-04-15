<?php

namespace mangeld\obj;

abstract class DataStore
{
  protected $validator;

  public function __construct()
  {
      //code...
  }

  protected function validateArgumentType($argument, $type)
  {
    if( gettype($argument) != $type )
      throw new \mangeld\exceptions\InvalidArgumentTypeException();
  }

  protected function attrIsSet($arg)
  {
    if( !$arg )
      throw new \mangeld\exceptions\AttributeNotSetException();
  }

  protected function validateEmail($email)
  {
    if( !$this->validator )
      throw new \mangeld\exceptions\DependencyNotGivenException();

    $ok = $this->validator->validateEmail($email);

    if( !$ok )
      throw new \mangeld\exceptions\MalformatedStringException();
  }

  protected function validateUuid($id)
  {
    if( !$this->validator )
      throw new \mangeld\exceptions\DependencyNotGivenException();

    $ok = $this->validator->validateUuid4($id);
    
    if( !$ok )
      throw new \mangeld\exceptions\MalformatedStringException();
  }
}