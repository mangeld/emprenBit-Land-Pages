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

  /**
   * @return CardField[]
   */
  public function getFields()
    { return $this->fields; }

  public function countFields()
    { return count($this->fields); }

  public function getType() { return $this->cardType; }

  public function getId() { return $this->id; }

  public function setPage(Page $page)
  {
    $this->page = $page;
  }

  public function getPage()
    { return $this->page; }
}
