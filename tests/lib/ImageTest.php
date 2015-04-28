<?php

class ImageTest extends PHPUnit_Framework_TestCase
{

  /** @var mangeld\lib\Image */
  public $image;

  public function setUp()
  {
    $this->image = mangeld\lib\Image::fromFile(\mangeld\lib\filesystem\File::openFile('/vagrant/photo.png'));
  }

  public function testImage()
  {
    $image = mangeld\lib\Image::fromFile(\mangeld\lib\filesystem\File::openFile('/vagrant/photo.png'));
    $image->blur(1);
    $image->save('/vagrant/photo_blur.png');
  }

  public function testResize()
  {
    $image = mangeld\lib\Image::fromFile(\mangeld\lib\filesystem\File::openFile('/vagrant/photo.png'));
    $image->resize(400, 400);
    $image->save('/vagrant/photo_resize.png');
  }

  public function testSquarifyBlur()
  {
    $this->image->squarifyBlur(600);
    $this->image->save('/vagrant/photo_squarify.png');
  }

}
