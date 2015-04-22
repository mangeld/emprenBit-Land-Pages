<?php

use \mangeld\obj\Card;
use \mangeld\obj\DataTypes;

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

  public function test3ColCardIsCreated()
  {
    $card = Card::createCard( DataTypes::cardThreeColumns );

    $this->assertInstanceOf('\mangeld\obj\ThreeColumnCard', $card);
  }

  public function testCardIsCreatedWithIdAndCardType()
  {
    $card = \mangeld\obj\Card::createCard(\mangeld\obj\DataTypes::cardThreeColumns);
    $validator = new \mangeld\lib\StringValidator();

    $this->assertTrue($validator->validateUuid4($card->getId()));
    $this->assertEquals(\mangeld\obj\DataTypes::cardThreeColumns, $card->getType());
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
