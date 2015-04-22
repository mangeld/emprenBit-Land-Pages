<?php

namespace mangeld\obj;

class ThreeColumnCard extends Card
{
  /** @var array Array of title keys */
  private $titles = array();
  /** @var array Array of body keys */
  private $bodies = array();
  /** @var array Array of image keys */
  private $images = array();

  public function setTitle($title, $column)
  {
    if ( !$this->checkBounds($column) ) return;
    $field = \mangeld\obj\CardField::createField(\mangeld\obj\DataTypes::fieldTitle);
    $field->setIndex($column);
    $field->setText($title);
    $this->fields[ $field->getId() ] = $field;
    $this->titles[$column] = $field->getId();
  }

  /**
   * @param $column
   * @return CardField
   */
  public function getTitle($column)
  {
    if( !$this->checkSet( $this->titles, $column ) )
      return null;
    return $this->fields[ $this->titles[$column] ];
  }

  public function setBody($body, $column)
  {
    if ( !$this->checkBounds($column) ) return;
    $field = \mangeld\obj\CardField::createField(\mangeld\obj\DataTypes::fieldText);
    $field->setIndex($column);
    $field->setText($body);
    $this->fields[ $field->getId() ] = $field;
    $this->bodies[$column] = $field->getId();
  }

  /**
   * @param $column
   * @return CardField
   */
  public function getBody($column)
  {
    if( !$this->checkSet( $this->bodies, $column ) )
      return null;
    return $this->fields[ $this->bodies[$column] ];
  }

  public function setImage($image, $column)
  {
    if( !$this->checkBounds($column) ) return;
    $field = CardField::createField(DataTypes::fieldImage);
    $field->setIndex($column);
    $field->setText($image);
    $this->fields[ $field->getId() ] = $field;
    $this->images[$column] = $field->getId();
  }

  /**
   * @param $column
   * @return CardField
   */
  public function getImage($column)
  {
    if( !$this->checkSet( $this->images, $column ) )
      return null;
    return $this->fields[ $this->images[$column] ];
  }

  private function checkBounds($n)
  {
    if( $n > 3 || $n < 1 )
      return false;
    else
      return true;
  }

  private function checkSet($list, $n)
  {
    return array_key_exists($n, $list);
  }

  /**
   * @deprecated
   */
  public function getIterator()
  {
    $iterator = new ThreeColumnCard();
    $iterator->columns = $this->columns;
    return $iterator;
  }
}

class ThreeColumnCardIterator implements \Iterator
{
  private $columns = array();
  private $size;
  private $position = 0;
  private $field = 0;

  /**
   * @param $pos
   * @param $field
   * @return CardField
   */
  private function getFieldOfIndex($pos, $field)
  {
    switch($field)
    {
      case 0:
        $title = $this->columns[$pos]->title;
        if( !$title )
          $this->forwardPos();
        else
          return $title;
      case 1:
        $body = $this->columns[$pos]->body;
        if( !$body )
          $this->forwardPos();
        else
          return $body;
      case 2:
        $image = $this->columns[$pos]->image;
        if( !$image )
          $this->forwardPos();
        else
          return $image;
      default:
        return null;
    }
  }

  private function forwardPos()
  {
    if( !$this->size ) $this->size = count($this->columns);

    if( $this->field > 2 && $this->position < 3 )
    {
      $this->field = 0;
      $this->position++;
    }
    else if ( $this->position < 3 )
      $this->field++;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current()
  {
    return $this->getFieldOfIndex($this->position, $this->field);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next()
  {
    $this->forwardPos();
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   */
  public function key()
  {
    return $this->getFieldOfIndex($this->position, $this->field)->getId();
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   */
  public function valid()
  {
    $field = $this->getFieldOfIndex($this->position, $this->field);
    if( $field == null )
      while( $this->position < $this->size && $field == null )
      {
        $this->position++;
        $field = $this->getFieldOfIndex($this->position, $this->field);
      }

    return $field != null;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind()
  {
    $this->position = 0;
    $this->field = 0;
  }
}