<?php

include_once '../vendor/autoload.php';
use \mangeld\lib\filesystem\File;

if( !isset( $argv[1] ) && !isset($argv[2]) ) exit(1);

$source = $argv[1];
$destination = $argv[2];
$image = File::openFile($source);
$image->makeImageVersions($destination);
File::openFile($source)->delete();

exit(0);
