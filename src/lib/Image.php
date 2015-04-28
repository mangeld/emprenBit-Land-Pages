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

  private function createBlurredBackground($image, $quantiy = 15)
  {
    /** @var $image \Imagick */

    $image->resizeImage(600, 600, \Imagick::FILTER_LANCZOS, 1, true);

    $biggerSide = max( $image->getImageWidth(), $image->getImageHeight() );
    $smallerSide = min( $image->getImageHeight(), $image->getImageWidth() );

    $background = clone $image;
    $background->resizeImage($biggerSide * $smallerSide, $biggerSide, \Imagick::FILTER_LANCZOS, 1, true);
    $background->blurImage(50, 25);

    $posTop = ($biggerSide / 2) - ($image->getImageHeight() / 2 );
    $posLeft = ( $background->getImageWidth() / 2 ) - ( $image->getImageWidth() / 2 );

    $res = $background->compositeImage($image, \Imagick::COMPOSITE_OVER, $posLeft, $posTop);

    $background->cropImage($biggerSide, $biggerSide * $smallerSide, ($smallerSide/2)+($biggerSide/2), 0 );

    $this->image = $background;
  }

  public function squarifyBlur($size)
  {
    $this->createBlurredBackground($this->image);
  }

  public function save($path = null)
  {
    $file = $path == null ? $this->file : File::newFile($path, true);

    $file->open();
    $this->image->writeImageFile( $file->getHandle() );
    $file->close();
  }

}
