<?php

include_once '../vendor/autoload.php';
use \mangeld\lib\filesystem\File;
use \mangeld\exceptions\FileSystemException;

if( !isset( $argv[1] ) && !isset($argv[2]) )
{
  \mangeld\lib\Logger::instance()->warning("Not enought args given to mk_image_versions");
  exit(1);
}
$config = new \mangeld\Config();
$source = $argv[1];
$destination = $argv[2];
try
{
  $image = File::openFile($source);
  $image->makeImageVersions($destination);
  
  if( $config->saveOriginalMedia() )
    File::openFile($source)->move($destination);
  else
    File::openFile($source)->delete();
}
catch (FileSystemException $e)
{ \mangeld\lib\Logger::instance()->error("Error opening image $source in mk_image_versions"); }

exit(0);
