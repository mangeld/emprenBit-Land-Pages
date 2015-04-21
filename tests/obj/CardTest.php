<?php

class CardTests extends PHPUnit_Framework_TestCase
{
  public function testCardIsCreatedWithValidUuid()
  {
    $card = \mangeld\obj\Card::createEmptyCard();
    $id = $card->getId();
    $validator = new \mangeld\lib\StringValidator();

    $this->assertInstanceOf('\mangeld\obj\Card', $card);
    $this->assertNotEmpty( $id );
    $this->assertTrue( $validator->validateUuid4($id) );
  }

  public function testFieldIsAddedAndRetrieved()
  {
    $card = \mangeld\obj\Card::createEmptyCard();
    $field = \mangeld\obj\CardField::createEmptyField();
    $card->addField($field);
    $result = $card->getField( $field->getId() );

    $this->assertEquals($field, $result);
  }

}