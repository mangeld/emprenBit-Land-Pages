<?php

require "../vendor/autoload.php";

$test = new \mangeld\App();
$test->setName("Hello World");

$slimApp = new \Slim\Slim();

$slimApp->get('/test', function(){
	echo 'TEST';
});

$slimApp->run();