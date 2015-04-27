<?php

namespace mangeld\lib\filesystem;

use mangeld\Config;
use mangeld\exceptions\FileSystemException;
use mangeld\exceptions\FileUploadException;

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

  public static function newFile($path)
  {
    if( file_exists($path) )
      throw new FileSystemException("Path $path already exists.");

    //echo PHP_EOL . $path . PHP_EOL;

    fclose( fopen( $path, 'x' ) );
    $file = new File();
    $file->path = $path;
    $file->size = filesize($path);
    $file->filename = pathinfo($path, PATHINFO_BASENAME);
    return $file;
  }

  public function saveToStorage(\mangeld\obj\Page $page, \mangeld\App $app = null)
  {
    $id = \Rhumsaa\Uuid\Uuid::uuid4();
    $this->id = $id->toString();
    $this->ownerId = $page->getId();

    $this->move(
      Config::storage_folder .
      DIRECTORY_SEPARATOR .
      $page->getId() .
      DIRECTORY_SEPARATOR .
      $id . '.jpg'
    );
  }

  public function move($newPath)
  {
    $currPath = $this->fullPath();

    if( file_exists($newPath) )
      throw new FileSystemException("Unable move file $currPath to $newPath already exists.");

    $folderCreation = true;
    $moved = true;
    $movedUploaded = true;

    if( $this->path != pathinfo($newPath, PATHINFO_DIRNAME) )
      $folderCreation = mkdir(pathinfo($newPath, PATHINFO_DIRNAME), Config::storage_permission, true);

    if( $this->uploadedFile )
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

  public function getId()
    { return $this->id; }

  public function isImage()
  {
    $image = new \Imagick();
    $test = array( _('test') );
  }
}