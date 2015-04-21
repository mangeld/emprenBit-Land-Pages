<?php

class CardFieldTest extends PHPUnit_Framework_TestCase
{
  public function testCardFieldIsCreatedWithValidUuid()
  {
    $cardField = \mangeld\obj\CardField::createEmptyField();
    $id = $cardField->getId();
    $validator = new \mangeld\lib\StringValidator();

    $this->assertInstanceOf('\mangeld\obj\CardField', $cardField);
    $this->assertNotEmpty( $id );
    $this->assertTrue( $validator->validateUuid4($id) );
  }

  public function testCardFieldReferencesToCard()
  {
    $card = \mangeld\obj\Card::createEmptyCard();
    $field = \mangeld\obj\CardField::createEmptyField();
    $field->setCard( $card );

    $this->assertEquals( $card, $field->getCard() );
    $this->assertEquals( $field, $card->getField($field->getId()) );
  }

}