<?php

use mangeld\obj\Card;
use mangeld\obj\CardCarousel;
use mangeld\obj\DataTypes;
use Rhumsaa\Uuid\Uuid;

class CardCarouselTest extends PHPUnit_Framework_TestCase
{

  public function testImageSetAndRetrieved()
  {
    /** @var CardCarousel $card */
    $card = Card::createCard(DataTypes::cardCarousel);

    $id = Uuid::uuid4()->toString();
    $text = 'some text';

    $card->addImage($id, $text, 1);

    $this->assertEquals($id, $card->getImageId(1));
  }

  public function testImageTextSetAndRetrieved()
  {
    /** @var CardCarousel $card */
    $card = Card::createCard(DataTypes::cardCarousel);

    $id = Uuid::uuid4()->toString();
    $text = 'some text';

    $card->addImage($id, $text, 1);

    $this->assertEquals($text, $card->getImageText(1));
  }

}
