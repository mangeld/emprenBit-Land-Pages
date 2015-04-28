<?php

class ImageTest extends PHPUnit_Framework_TestCase
{

  public function testImage()
  {
    $image = mangeld\lib\Image::fromFile(\mangeld\lib\filesystem\File::openFile('/vagrant/photo.png'));
    $image->blur(15);
    $image->save('/vagrant/photo_blur.png');
  }

  public function testResize()
  {
    $image = mangeld\lib\Image::fromFile(\mangeld\lib\filesystem\File::openFile('/vagrant/photo.png'));
    $image->resize(400, 400);
    $image->save('/vagrant/photo_resize.png');
  }

}
