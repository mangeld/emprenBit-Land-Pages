<?php

namespace mangeld\obj;

class Card extends DataStore
{
  protected $id;
  /** @var Page */
  protected $page;
  protected $cardType;
  /** @var CardField[] */
  protected $fields = array();
  protected $color;
  protected $backgroundColor;

  public static function createEmptyCard()
  {
    $card = new Card();
    $card->validator = new \mangeld\lib\StringValidator();
    $card->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    return $card;
  }

  public static function createCard($cardType, $cardId = '')
  {
    switch( $cardType )
    {
      case DataTypes::cardThreeColumns:
        $card = new ThreeColumnCard();
        break;
      case DataTypes::cardForm:
        //TODO: Create class cardForm
        $card = new Card();
        break;
      case DataTypes::cardCarousel:
        $card = new CardCarousel();
        break;
      default:
        $card = new Card();
        break;
    }

    $card->validator = new \mangeld\lib\StringValidator();
    $card->cardType = $cardType;

    if( $cardId )
    {
      $card->validateUuid( $cardId );
      $card->id = $cardId;
    }
    else
      $card->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();

    return $card;
  }

  public function addField(CardField $field)
  {
    $field->setCard( $this );
    $this->fields[$field->getId()] = $field;
  }

  public function getField($fieldId)
  {
    return $this->fields[ $fieldId ];
  }

  public function setColor($color)
  { $this->color = $color; }

  public function getColor()
  { return $this->color; }

  public function setBackgroundColor($color)
  { $this->backgroundColor = $color; }

  public function getBackgroundColor()
  { return $this->backgroundColor; }

  /**
   * @return CardField[]
   */
  public function getFields()
    { return $this->fields; }

  public function countFields()
    { return count($this->fields); }

  public function hasFields()
    { return count($this->fields) > 0; }

  public function getType() { return $this->cardType; }

  public function getId() { return $this->id; }

  public function setPage(Page $page)
  {
    $this->page = $page;
  }

  public function getPage()
    { return $this->page; }
}
