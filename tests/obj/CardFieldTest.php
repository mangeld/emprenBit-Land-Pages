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

  public function testFieldIsCreatedWithValidType()
  {
    $id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    $type = \mangeld\obj\DataTypes::fieldEmail;
    $field = \mangeld\obj\CardField::createField($type, $id);

    $this->assertEquals($field->getType(), $type);
    $this->assertEquals($field->getId(), $id);
  }

  public function testCardFieldReferencesToCard()
  {
    $card = \mangeld\obj\Card::createEmptyCard();
    $field = \mangeld\obj\CardField::createEmptyField();
    $card->addField( $field );

    $this->assertEquals( $card, $field->getCard() );
    $this->assertEquals( $field, $card->getField($field->getId()) );
  }

}
