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
}
