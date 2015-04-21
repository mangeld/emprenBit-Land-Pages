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
    $card->id = \Rhumsaa\Uuid\Uuid::uuid4()->toString();
    return $card;
  }

  public static function createCard($cardId, $cardType)
  {
    $card = new Card();
    $card->id = $cardId;
    $card->cardType = $cardType;
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

  public function getId() { return $this->id; }

  public function setPage(Page $page)
  {
    $this->page = $page;
  }

  public function getPage()
    { return $this->page; }
}