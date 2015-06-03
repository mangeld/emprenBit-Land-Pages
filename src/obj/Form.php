<?php
namespace mangeld\obj;

use mangeld\Config;
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
    $form->sourceIp = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
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
  {
    $this->f_Name = $this->sanitizeName($name);
  }

  private function sanitizeName($name)
  {
    $filtered = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $filtered = filter_var($filtered, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP);
    return $filtered;
  }

  public function setEmail($email)
  {
    $this->validateEmail($email);
    $this->f_email = $email;
  }

	/**
	 * Get the form as an array.
	 *
	 * @var prettyDate Exports the date as human readable.
	 * By default its true, to get a unix timestamp set it to 'false'
   * @return array
	 */
  public function asArray($prettyDate = true)
  {
    $out = get_object_vars( $this );
    unset( $out['id'] );
    unset( $out['validator'] );
    unset( $out['page'] );
    unset( $out['sourceIp'] );

    if( $prettyDate )
    {
      $c = new Config();
      date_default_timezone_set('Europe/Madrid');
      $out['completionDate'] = date( $c->dateFormat(), $out['completionDate'] );
    }

    return $out;
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
