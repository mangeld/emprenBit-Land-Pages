<?php

namespace mangeld\obj;

class Card extends DataStore
{
  private $id;
  /** @var Page */
  private $page;
  private $cardType;
  /** @var  CardField */
  private $fields;

  public static function createEmptyCard()
  {
    $card = new Card();
    $card->validator = new \mangeld\lib\StringValidator();
    $card->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    return $card;
  }

  public static function createCard($cardType, $cardId = '')
  {
    $card = new Card();
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
    $this->fields[$field->getId()] = $field;
  }

  public function getField($fieldId)
  {
    return $this->fields[ $fieldId ];
  }

  public function getType() { return $this->cardType; }

  public function getId() { return $this->id; }

  public function setPage(Page $page)
  {
    $this->page = $page;
  }

  public function getPage()
    { return $this->page; }
}