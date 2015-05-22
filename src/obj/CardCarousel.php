<?php

namespace mangeld\obj;

use mangeld\obj\CardField;

class CardCarousel extends Card
{

  public function getImageId($index)
  {
    return $this->getFieldOfIndex($index, DataTypes::fieldImage)->getText();
  }

  public function getImageText($index)
  {
    return $this->getFieldOfIndex($index, DataTypes::fieldText)->getText();
  }

  private function getFieldOfIndex($index, $type)
  {
    foreach( $this->fields as $id => $field )
      if(
        $field->getType() == $type &&
        $field->getIndex() == $index )
        return $field;
  }

  public function countImages()
  {
    $max = 0;
    foreach( $this->fields as $field )
      $max = max( $max, $field->getIndex() );
    return $max;
  }

  public function addImage($imageId, $text, $index)
  {
    $imageField = CardField::createField(DataTypes::fieldImage);
    $imageField->setText($imageId);
    $imageField->setIndex($index);
    $textField = CardField::createField(DataTypes::fieldText);
    $textField->setText($text);
    $textField->setIndex($index);

    $this->addField($imageField);
    $this->addField($textField);
  }

  public function getImages()
  {
    $imageCount = $this->countImages();
    $images = array();
    for( $i = 0; $i <= $imageCount; $i++ )
      $images[$i] = new \StdClass();

    foreach ($this->getFields() as $id => $field)
    {
      if( $field->getType() == DataTypes::fieldImage )
        $images[ $field->getIndex() ]->src = $field->getText();
      elseif( $field->getType() == DataTypes::fieldText )
        $images[ $field->getIndex() ]->text = $field->getText();
    }

    return $images;
  }
}
