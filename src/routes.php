<?php

$test = new \mangeld\App();
$test->setName("Hello World");

$slimApp = new \Slim\Slim();

$slimApp->get('/login', function() use ($slimApp){
  
  $loader = new Twig_Loader_Filesystem('../templates/');
  $twig = new Twig_Environment($loader);
  $slimApp->response->setBody($twig->loadTemplate('index.html')->render(array()));

});

$slimApp->get('/admin', function() use ($slimApp) {
  $slimApp->response->redirect('/admin.html', 303);
});

$slimApp->get('/logout', function(){
  echo 'logout';
});

$slimApp->get('/time', function() use ($slimApp){
  $time = microtime(true);
  $slimApp->response->setBody(
    gettype($time).' - '.
    $time);
});

$slimApp->get('/passHash/:pasw', function($pasw) use ($slimApp){
  $hasher = new \mangeld\lib\Hasher();
  $hash = $hasher->create_hash_blowfish($pasw, 12);
  $slimApp->response->setBody(
    'Length: ' . strlen($hash) . ' | Hash: ' . $hash );
});

$slimApp->group('/v1', function() use ($slimApp) {

  //TODO: ADD HERE API ROUTES FOR ANGULAR JS

});

$slimApp->run();