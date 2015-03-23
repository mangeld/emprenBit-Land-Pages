<?php

require "../vendor/autoload.php";

$test = new \mangeld\App();
$test->setName("Hello World");

$slimApp = new \Slim\Slim();

$slimApp->get('/login', function(){
	
});

$slimApp->get('/logout', function(){

});

$slimApp->group('/v1', function() use ($slimApp) {

	//TODO: ADD HERE API ROUTES FOR ANGULAR JS

});

$slimApp->run();