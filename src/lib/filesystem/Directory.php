<?php

namespace mangeld\lib\filesystem;

class Directory
{

  /** @var File[] */
  private $files;
  private $path;
  private $size;

  public static function create($path)
  {
    if(is_dir($path))
    {
      mkdir($path, 0700, true);
      $dir = new Directory();
      $dir->path = $path;
      return $dir;
    }
  }

  public function getFiles()
  {

  }

  public function getFile($filename)
  {

  }

  public function addFile(File $file)
  {

  }

}
