<?php

namespace mangeld\lib;

use mangeld\lib\filesystem\File;

class Image
{
  /** @var \mangeld\lib\filesystem\File */
  private $file;
  /** @var \Imagick */
  private $image;

  public static function fromFile(\mangeld\lib\filesystem\File $file)
  {
    $image = new Image();
    $image->image = new \Imagick( $file->fullPath() );
    $image->file = $file;
    return $image;
  }

  public function blur($quantity)
  {
    $this->image->blurImage($quantity, $quantity / 2);
  }

  public function resize($width, $height)
  {
    $this->image->resizeImage($width,$height, \Imagick::FILTER_CATROM, .8, true);
  }

  public function save($path = null)
  {
    $file = $path == null ? $this->file : File::newFile($path, true);

    $file->open();
    $this->image->writeImageFile( $file->getHandle() );
    $file->close();
  }

}
