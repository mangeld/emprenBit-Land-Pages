<?php

namespace mangeld\lib;

use mangeld\lib\filesystem\File;

class Image
{

  /** @var \Imagick */
  private $image;

  public static function fromFile(\mangeld\lib\filesystem\File $file)
  {
    $image = new Image();
    $image->image = new \Imagick( $file->fullPath() );
    return $image;
  }

  public function blur($quantity)
  {
    $this->image->blurImage($quantity, $quantity / 2);
  }

  public function save()
  {
    $newFile = File::newFile('/vagrant/newFile.jpg', true);
    $newFile->open();
    $this->image->writeImageFile( $newFile->getHandle() );
    $newFile->close();
  }

}