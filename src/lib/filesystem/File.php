<?php

namespace mangeld\lib\filesystem;

use mangeld\Config;
use mangeld\exceptions\FileSystemException;
use mangeld\exceptions\FileUploadException;
use mangeld\lib\Image;
use mangeld\lib\Logger;
use mangeld\obj\Page;

class File
{
  private $id;
  private $ownerId;
  private $path;
  private $filename;
  private $realName;
  private $size;
  private $handle;
  private $uploadedFile = false;

  /**
   * @param $variable_name
   * @return File
   * @throws FileUploadException
   */
  public static function fromUploadedFile($variable_name)
  {
    if( !isset($_FILES[$variable_name]['tmp_name']) )
      throw new FileUploadException("No file uploaded");

    $filePath = $_FILES[$variable_name]['tmp_name'];
    $errorStatus = $_FILES[$variable_name]['error'];

    if( !is_uploaded_file($filePath) )
      throw new FileUploadException("File $filePath is not an uploaded file");

    if( $errorStatus != 0 )
      switch( $errorStatus )
      {
        case UPLOAD_ERR_INI_SIZE:
          throw new FileUploadException("Variable $variable_name is larger than 'upload_max_filesize'");
          break;
        case UPLOAD_ERR_FORM_SIZE:
          throw new FileUploadException("Variable $variable_name exceeds MAX_FILE_SIZE specified in the html form");
          break;
        case UPLOAD_ERR_PARTIAL:
          throw new FileUploadException("Variable $variable_name error partial");
          break;
        case UPLOAD_ERR_NO_FILE:
          throw new FileUploadException("No file uploaded for $variable_name");
          break;
        case UPLOAD_ERR_NO_TMP_DIR:
          throw new FileUploadException("No temp dir");
          break;
        case UPLOAD_ERR_CANT_WRITE:
          throw new FileUploadException("Can't write $variable_name to disk");
          break;
        case UPLOAD_ERR_EXTENSION:
          throw new FileUploadException("Upload err extension: an extension stopped the file upload");
          break;
      }

    $file = self::openFile($filePath);
    $file->realName = $_FILES[$variable_name]['name'];
    $file->uploadedFile = true;
    return $file;
  }

  public static function openFile($path)
  {
    if( !file_exists($path) )
      throw new FileSystemException("File $path doesn't exist");

    $file = new File();
    $file->filename = pathinfo($path, PATHINFO_BASENAME);
    $file->path = pathinfo($path, PATHINFO_DIRNAME);
    return $file;
  }

  public static function newFile($path, $overWrite = false)
  {
    if( $overWrite ) unlink( $path );

    if( file_exists($path) )
      throw new FileSystemException("Path $path already exists.");

    //echo PHP_EOL . $path . PHP_EOL;

    fclose( fopen( $path, 'x' ) );
    $file = new File();
    $file->path = pathinfo($path, PATHINFO_DIRNAME);
    $file->size = filesize($path);
    $file->filename = pathinfo($path, PATHINFO_BASENAME);
    return $file;
  }

  public function open($mode = 'r+')
  {
    $this->handle = fopen($this->fullPath(), $mode);
  }

  public function close()
  {
    fclose( $this->handle );
    $this->handle = null;
  }

  public function getHandle()
    { return $this->handle; }

  public function saveToStorage($page, \mangeld\App $app = null)
  {
    $id = \Rhumsaa\Uuid\Uuid::uuid4();
    $this->id = $id->toString();
    $this->ownerId = $page instanceof Page ? $page->getId() : $page ;

    $newPath =
      Config::storage_folder .
      DIRECTORY_SEPARATOR .
      $this->ownerId .
      DIRECTORY_SEPARATOR .
      $id . '.jpg';

    try
      { $this->move($newPath); }
    catch ( FileSystemException $e )
      { Logger::instance()->error( $e ); }
  }

  public function makeImageVersions($newPath)
  {
    $im = null;
    foreach (Config::$image_sizes as $name => $size)
    {
      try
        { $im = Image::fromFile($this); }
      catch( \ImagickException $e)
       { Logger::instance()->error('Error making image versions; '.$e->getMessage()); }

      if( $im != null )
      {
        $folder = pathinfo($newPath, PATHINFO_DIRNAME);
        $file = pathinfo($newPath, PATHINFO_FILENAME);

        $im->resize($size, $size);
        $im->save(
          $folder . DIRECTORY_SEPARATOR .
          $name . '_' . $file . '.jpg');
      }
      $im = null;
    }

  }

  public function makeImageVersionsAsync($newPath)
  {
    //TODO: Move to pool
    $poolFile =
      Config::storage_folder . DIRECTORY_SEPARATOR .
      'imagePool' . DIRECTORY_SEPARATOR .
      \Rhumsaa\Uuid\Uuid::uuid4();

    $this->move(
      $poolFile,
      true
    );

    $command = Config::script_mk_image_versions . " $poolFile $newPath > /dev/null &";
    exec($command);
  }

  public function move($newPath, $force = false)
  {
    $currPath = $this->fullPath();

    if( file_exists($newPath) )
      throw new FileSystemException("Unable move file $currPath to $newPath already exists.");

    $folderCreation = true;
    $moved = true;
    $movedUploaded = true;

    $newPathDir = pathinfo($newPath, PATHINFO_DIRNAME);

    if( $this->path != $newPathDir && !is_dir($newPathDir) )
      $folderCreation = mkdir($newPathDir, Config::storage_permission, true);

    if( !$force && class_exists('Imagick') && $this->uploadedFile && $this->isImage() )
      $this->makeImageVersionsAsync($newPath);
    elseif( $this->uploadedFile )
      $movedUploaded = move_uploaded_file( $currPath, $newPath );
    else
      $moved = rename( $currPath, $newPath );

    if( !$folderCreation )
      throw new FileSystemException("Error creating $newPath");

    if( !$movedUploaded )
      throw new FileSystemException("Error moving uploaded file $currPath to $newPath");

    if( !$moved )
      throw new FileSystemException("Error moving $currPath to $newPath");

    $this->filename = pathinfo($newPath, PATHINFO_BASENAME);
    $this->path = pathinfo($newPath, PATHINFO_DIRNAME);
  }

  public function fullPath()
  {
    return $this->path . DIRECTORY_SEPARATOR . $this->filename;
  }

  public function delete()
    { unlink($this->fullPath()); }

  public function getId()
    { return $this->id; }

  public function isImage()
  {
    $itIs = true;
    try
      { Image::fromFile($this); }
    catch ( \ImagickException $e)
      { $itIs = false; }
    return $itIs;
  }
}
