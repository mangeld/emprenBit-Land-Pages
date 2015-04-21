<?php

class ThreeColumnCardTest extends PHPUnit_Framework_TestCase
{

  public function testThreeColumnCardIterator()
  {
    $card3column = new \mangeld\obj\ThreeColumnCard();
    $card3column->setTitle('testTitle1', 1);
    $card3column->setBody('aksdjfhalksdhfkajhsdf', 2);
    $datas = array();
    $it = $card3column->getIterator();
    foreach( $it as $key => $field )
    {
      var_dump($key);
      $datas[] = $field;
    }

    $this->assertEquals(2, count($datas));
  }

}