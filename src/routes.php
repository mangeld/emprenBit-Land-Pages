<?php

$app = new \mangeld\App();
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

$slimApp->group('/v1', function() use ($slimApp){

  $slimApp->get('/pages/:id', function($id){
    //TODO: Fetch data & define json object
  });

  $slimApp->post('/pages', function(){
    //TODO: Add page & define json object
    //TODO: Authenticate user
  });



});

$slimApp->run();