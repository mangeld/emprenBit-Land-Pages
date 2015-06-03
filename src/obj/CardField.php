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
  private $color;

  public static function createEmptyField()
  {
    $field = new CardField();
    $field->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    return $field;
  }

  public static function createField($type, $id = '')
  {
    $field = new CardField();
    $field->validator = new \mangeld\lib\StringValidator();
    $field->type = $type;
    if( $id )
    {
      $field->validateUuid($id);
      $field->id = $id;
    }
    else
      $field->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();

    return $field;
  }

  public function setColor($color)
    { $this->color = $color; }

  public function getColor()
    { return $this->color; }

  public function setText($text)
    { $this->text = $text; }

  public function getText()
    { return $this->text; }

  public function setIndex($index)
    { $this->index = $index; }

  public function getIndex()
    { return $this->index; }

  public function getType()
    { return $this->type; }

  public function getId()
    { return $this->id; }

  public function setCard(Card $card)
  {
    $this->card = $card;
  }

  public function getCard()
    { return $this->card; }
}
