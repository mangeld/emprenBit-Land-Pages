<?php

use mangeld\obj\Form;

class FormTest extends PHPUnit_Framework_TestCase
{

  /** @var Form */
  private $form;

  public function setUp()
  {
    $this->form = Form::createForm();
  }

  public function testFactoryWorks()
  {
    $form = Form::createForm();

    $this->assertNotNull($form);
    $this->assertInstanceOf('\mangeld\obj\Form', $form);
  }

  public function testSanitize()
  {
    $form = Form::createForm();
    $name = 'asdfáeèáòó@sdf4_-rw<br><p>cacacaca</p>';
    $form->setName($name);

    $this->assertNotEquals($name, $form->getName());

  }

}
