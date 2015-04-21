<?php

namespace mangeld\obj;

class CardField extends DataStore
{
  private $id;
  private $type;
  /** @var Card */
  private $card;
  private $text;
  private $index;

  public static function createEmptyField()
  {
    $field = new CardField();
    $field->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    return $field;
  }

  public function getId()
    { return $this->id; }

  public function setCard(Card $card)
  {
    $card->addField( $this );
    $this->card = $card;
  }

  public function getCard()
    { return $this->card; }
}