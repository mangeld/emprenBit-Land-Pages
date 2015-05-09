<?php

include_once '../vendor/autoload.php';
use \mangeld\lib\filesystem\File;
use \mangeld\exceptions\FileSystemException;

if( !isset( $argv[1] ) && !isset($argv[2]) ) exit(1);

ini_set("log_errors", 1);
ini_set("error_log", "error.log");

$source = $argv[1];
$destination = $argv[2];
try
{
  $image = File::openFile($source);
  $image->makeImageVersions($destination);
  File::openFile($source)->delete();
}
catch (FileSystemException $e)
{ error_log('Error opening image $source', 3, \mangeld\Config::error_log_file); }

exit(0);
