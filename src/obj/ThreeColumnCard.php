<?php

namespace mangeld\obj;

class ThreeColumnCard extends Card
{
  private $columns = array();

  public function __construct()
  {
    for($i = 0; $i < 3; $i++)
      $this->columns[$i] = new \StdClass();
  }

  public function setTitle($title, $column)
  {
    $field = \mangeld\obj\CardField::createField(\mangeld\obj\DataTypes::fieldTitle);
    $field->setIndex($column);
    $field->setText($title);
    $this->columns[$column]->title = $field;
  }

  public function setBody($body, $column)
  {
    $field = \mangeld\obj\CardField::createField(\mangeld\obj\DataTypes::fieldText);
    $field->setIndex($column);
    $field->setText($body);
    $this->columns[$column]->body = $field;
  }

  public function setImage($image, $column)
  {
    $field = \mangeld\obj\CardField::createField(\mangeld\obj\DataTypes::fieldImage);
    $field->setIndex($column);
    $field->setText($image);
    $this->columns[$column]->image = $field;
  }
}

class ThreeColumnCardIterator implements \Iterator
{
  private $columns = array();
  private $position;

  public function setData(array $columns)
    { $this->columns = $columns; }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current()
  {
    // TODO: Implement current() method.
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next()
  {
    // TODO: Implement next() method.
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   */
  public function key()
  {
    // TODO: Implement key() method.
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
    // TODO: Implement valid() method.
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind()
  {
    // TODO: Implement rewind() method.
  }
}