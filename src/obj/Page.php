<?php

namespace mangeld\obj;

class Page extends DataStore
{
  private $name = '';
  private $uuid = '';
  private $creationTimestamp = 0.0;

  public function __construct(
    \mangeld\lib\StringValidator $validator = null)
  {
      $this->validator = $validator;
  }

  public static function createPage()
  {
    $val = new \mangeld\lib\StringValidator();
    $uuid4 = \Rhumsaa\Uuid\Uuid::uuid4();
    $page = new \mangeld\obj\Page($val);
    $page->setCreationTimestamp(microtime(true));
    $page->setId($uuid4->toString());
    return $page;
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