<?php
namespace mangeld\obj;

use mangeld\lib\StringValidator;
use Rhumsaa\Uuid\Uuid;

class Form extends DataStore
{
  private $id;
  private $completionDate;
  private $sourceIp;
  /** @var Page */
  private $page;
  private $f_Name;
  private $f_email;

  public static function createForm()
  {
    $form = new Form();
    $form->validator = new StringValidator();
    $form->id = Uuid::uuid4()->toString();
    if( isset($_SERVER['REMOTE_ADDR']) )
      $form->sourceIp = htmlspecialchars($_SERVER['REMOTE_ADDR']);
    $form->completionDate = microtime(true);
    return $form;
  }

  public static function buildForm($row)
  {
    $form = new Form();
    $form->validator = new StringValidator();
    $form->id = $row->formId;
    $form->completionDate = $row->completionDate;
    $form->sourceIp = $row->sourceIp;
    $form->f_Name = $row->field_name;
    $form->f_email = $row->field_email;

    return $form;
  }

  public function setPage($page)
    { $this->page = $page; }

  public function setName($name)
    { $this->f_Name = $name; }

  public function setEmail($email)
  {
    $this->validateEmail($email);
    $this->f_email = $email;
  }

  public function getSourceIp()
    { return $this->sourceIp; }

  public function getCompletionDate()
    { return $this->completionDate; }

  public function getPage()
    { return $this->page; }

  public function getId()
    { return $this->id; }

  public function getEmail()
    { return $this->f_email; }

  public function getName()
    { return $this->f_Name; }
}
