<?php

class ImageTest extends PHPUnit_Framework_TestCase
{

  public function testImage()
  {
    $image = mangeld\lib\Image::fromFile(\mangeld\lib\filesystem\File::openFile('/vagrant/photo.jpg'));
    $image->blur(2);
    $image->save();
  }

}