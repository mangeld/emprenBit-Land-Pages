<?php

namespace mangeld\obj;

class Page extends DataStore
{
  /**
   * Name for displaying the Page
   * internally.
   * @var string
   */
  private $name = '';
  /**
   * Id in uuid v.4 format
   * @var string
   */
  private $uuid = '';
  /**
   * Creation timestamp in UTC
   * @var float
   */
  private $creationTimestamp = 0.0;
  /**
   * The image id of the landing page logo
   * @var string
   */
  private $logoId = '';
  /**
   * Short title that describes the landing page,
   * it is shown to the public.
   * @var string
   */
  private $title = '';
  /**
   * Short description that is shown to te public.
   * @var string
   */
  private $description = '';
  /**
   * @var \mangeld\obj\User
   */
  private $owner = null; //TODO: Load here the user object that represents the owner

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

  public static function createPageWithNewUser()
  {
    $val = new \mangeld\lib\StringValidator();
    $uuid4 = \Rhumsaa\Uuid\Uuid::uuid4();
    $page = new \mangeld\obj\Page($val);
    $page->setCreationTimestamp(microtime(true));
    $page->owner = User::createUser();
    $page->uuid = $uuid4->toString();
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

  public function setLogoId($id)
  {
    $this->validateArgumentType($id, 'string');
    $this->validateUuid($id);
    $this->logoId = $id;
  }

  public function getLogoId()
  {
    return $this->logoId;
  }

  public function setDescription($desc)
  {
    $this->description = $desc;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function setOwner(\mangeld\obj\User $owner)
  {
    $this->owner = $owner;
  }

  public function getOwner()
  {
    return $this->owner;
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