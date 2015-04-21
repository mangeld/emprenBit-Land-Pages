<?php

namespace mangeld\obj;

abstract class DataStore
{
  protected $validator;

  public function __construct()
  {
      //code...
  }

  protected function validateArgumentType($argument, $type, $message = '')
  {
    if( gettype($argument) != $type )
      throw new \mangeld\exceptions\InvalidArgumentTypeException($message);
  }

  protected function attrIsSet($arg, $message = '')
  {
    if( !$arg )
      throw new \mangeld\exceptions\AttributeNotSetException($message);
  }

  protected function validateEmail($email)
  {
    if( !$this->validator )
      throw new \mangeld\exceptions\DependencyNotGivenException('String validator was not set');

    $ok = $this->validator->validateEmail($email);

    if( !$ok )
      throw new \mangeld\exceptions\MalformatedStringException('Invalid email');
  }

  protected function validateUuid($id)
  {
    if( !$this->validator )
      throw new \mangeld\exceptions\DependencyNotGivenException('String validator was not set');

    $ok = $this->validator->validateUuid4($id);
    
    if( !$ok )
      throw new \mangeld\exceptions\MalformatedStringException('Invalid uuid (Must be uuid v.4)');
  }
}