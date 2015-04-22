<?php

class ThreeColumnCardTest extends PHPUnit_Framework_TestCase
{

  public function testFieldsAreRetrieved()
  {
    $card3column = new \mangeld\obj\ThreeColumnCard();
    $card3column->setTitle('testTitle1', 1);
    $card3column->setBody('aksdjfhalksdhfkajhsdf', 2);
    $nFields = count( $card3column->getFields() );

    $this->assertEquals( 2, $nFields );
  }

  public function testFieldsAreInCorrectPos()
  {
    $card3column = new \mangeld\obj\ThreeColumnCard();
    $card3column->setTitle( 'testTitle111', 1 );
    $card3column->setBody( 'akfdjjh akjdfg halksdfjh a', 2);

    $this->assertEquals( 'testTitle111', $card3column->getTitle(1)->getText() );
    $this->assertEquals( 'akfdjjh akjdfg halksdfjh a', $card3column->getBody(2)->getText() );
    $this->assertEmpty( $card3column->getBody(1) );
  }

  public function testNothingDoneOnSetBadIndex()
  {
    $card3col = new \mangeld\obj\ThreeColumnCard();
    $card3col->setTitle('cool title', 3);
    $card3col->setTitle('asdasdasd', 4);
    $card3col->setBody('asdasdasd', 0);

    $this->assertEmpty( $card3col->getBody(0) );
    $this->assertEquals('cool title', $card3col->getTitle(3)->getText());
    $this->assertEmpty( $card3col->getTitle(4) );
  }

}