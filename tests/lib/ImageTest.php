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
    $image = $this->image;
    $image->resize(600, 600);
    $image->blur(5);
    $image->save('/vagrant/photo_blur.jpg');
  }

  public function testGetFormat()
  {
    var_dump($this->image->getFormat());
  }

  public function testResize()
  {
    $image = $this->image;
    $image->resize(600, 600);
    $image->save('/vagrant/photo_resize.jpg');
  }

  public function testSquarifyBlur()
  {
    $this->image->squarifyBlur(600);
    $this->image->save('/vagrant/photo_squarify.jpg');
  }

}
