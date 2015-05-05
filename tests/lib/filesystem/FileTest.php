<?php

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use mangeld\lib\filesystem\File;

class FileTest extends PHPUnit_Framework_TestCase
{

  public function setUp()
  {
    \org\bovigo\vfs\vfsStreamWrapper::register();
    \org\bovigo\vfs\vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('test'));
  }

  public function testFileIsCreated()
  {
    $file =File::newFile(vfsStream::url('test/test.txt'));
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('test.txt'));
  }

  public function testFileIsMovedInSameDir()
  {
    vfsStreamWrapper::getRoot()->addChild(vfsStream::newFile('test.img'));
    $file = File::openFile(vfsStream::url('test/test.img'));
    $file->move(vfsStream::url('test/moved.img'));

    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('moved.img'));
    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('test.img'));
  }

  public function testFileIsMovedToOtherDir()
  {
    vfsStreamWrapper::getRoot()->addChild(vfsStream::newFile('test.img'));
    $file = File::openFile(vfsStream::url('test/test.img'));
    $file->move(vfsStream::url('test/storage/123123.img'));

    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('storage/123123.img'));
    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('test.img'));
  }

  /**
   * @expectedException \mangeld\exceptions\FileUploadException
   */
  public function testExceptionFileIsNotUploadedFile()
  {
    vfsStreamWrapper::getRoot()->addChild(vfsStream::newFile('test.img'));
    File::fromUploadedFile(vfsStream::url('test/test.img'));
  }
//
//  public function testFileRegexIsLoaded()
//  {
//    vfsStreamWrapper::getRoot()->addChild(vfsStream::newDirectory('09123'));
//    vfsStreamWrapper::getRoot()->addChild(vfsStream::newFile('09123/small_3334.jpg'));
//    vfsStreamWrapper::getRoot()->addChild(vfsStream::newFile('09123/large_3334.jpg'));
//    vfsStreamWrapper::getRoot()->addChild(vfsStream::newFile('09123/medium_3334.jpg'));
//    File::openFile(vfsStream::url('test/09123/small_3334.jpg'));
//    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('09123/large_3334.jpg'));
//    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('09123/medium_3334.jpg'));
//    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('09123/small_3334.jpg'));
//  }

}
