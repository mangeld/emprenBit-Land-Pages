<?php

$app = \mangeld\App::createApp();
$slimApp = new \Slim\Slim();
$slimApp->config('debug', true);
$slimApp->add( new \mangeld\PostCheckMiddleware($app) );

$slimApp->get('/login', function() use ($slimApp){

  $loader = new Twig_Loader_Filesystem('../templates/');
  $twig = new Twig_Environment($loader);
  $slimApp->response->setBody($twig->loadTemplate('index.twig')->render(array()));

});

$slimApp->get('/admin', function() use ($slimApp) {
  $slimApp->response->redirect('/admin.html', 303);
});

$slimApp->get('/logout', function(){
  echo 'logout';
});

$slimApp->get('/passHash/:pasw', function($pasw) use ($slimApp){
  $hasher = new \mangeld\lib\Hasher();
  $hash = $hasher->create_hash_blowfish($pasw, 12);
  $slimApp->response->setBody(
    'Length: ' . strlen($hash) . ' | Hash: ' . $hash );
});

$slimApp->get('/:pageName', function($pageName) use ($slimApp){
  $app = \mangeld\App::createApp();
  $loader = new Twig_Loader_Filesystem('../templates/landing2');
  $twig = new Twig_Environment($loader);

  $pages = $app->getPagesAsObj();
  /**
   * @var  $key
   * @var \mangeld\obj\Page $page
   */
  foreach( $pages as $key => $page )
  {
    if( $page->getName() == $pageName )
    {
      $slimApp->response->setBody( $twig->loadTemplate('index.twig')->render([ 'page' => $page ]) );
      return;
    }
  }
  $slimApp->notFound();
});

$slimApp->group('/v1', function() use ($slimApp, $app){

  $slimApp->post('/upload', function() use($slimApp, $app){

    $file = \mangeld\lib\filesystem\File::fromUploadedFile('image');
    $file->saveToStorage(\mangeld\obj\User::createUser());

  });

  $slimApp->group('/cards', function() use ($slimApp, $app){

    $slimApp->post('/:id', function($id) use ($slimApp, $app){

      $da = var_export($slimApp->request->params('data'), true);
      $slimApp->response->setBody( $da );
    });

  });

  /**
   * Here we define the routes for the 'pages' resource, wich lets
   * us list all the pages, add, remove, or get an specific Page data.
   */
  $slimApp->group('/pages', function() use ($slimApp, $app){

    /**
     * If you call the /v1/pages you get all the pages in the system
     *
     * TODO: Limit the data in each page & make pagination so the
     * response isn't too big.
     */
    $slimApp->get('/', function() use ($slimApp, $app){
      $pages = $app->getPages();
      $slimApp->response->headers->set('Content-Type', 'application/json');
      $slimApp->response->setBody($pages);
      $app->closeDB();
    });

    /**
     * Add a page by it's name.
     *
     * Returns: A json object with the page created.
     */
    $slimApp->post('/', function() use ($slimApp, $app){

      if( $app->maxPostSizeExceeded( (int) $slimApp->request->headers->get('Content-Length') ) )
      {

      }
      else
      {
        $json = $slimApp->request->post('data');
        $jsonObj = json_decode($json);
        //TODO: CREAR PAGINA Y DEVOLVER DATOS DE LA PAGINA CREADA, GUARDAR IMAGEN
        //TODO: Usar parametros y no un objeto json para mas compatibilidad
        //move_uploaded_file($_FILES['image']['tmp_name'], '../public/storage/'.$_FILES['image']['name']);
        $app->createPage($jsonObj);
      }
    });

    /**
     * Delete a page from the system.
     *
     * Return: A json object with the status.
     */
    $slimApp->delete('/:id', function($id) use ($slimApp, $app){
      //TODO: Remove from database & send confirmation
      $res = $app->deletePage($id);
      $slimApp->response->setBody('DELETED RESOURCE: ' . $res);
      $app->closeDB();
    });

    $slimApp->group('/:pageId/cards', function() use ($slimApp, $app){

      $slimApp->get('/', function($pageId) use ($slimApp, $app){
        $slimApp->response->setBody("CARD IN PAGES");
      });

      $slimApp->post('/', function($pageId) use ($slimApp, $app){
        //var_dump($slimApp->request->params('data'));
        $app->addCard($slimApp->request->params('data'), $pageId);
      });

      $slimApp->delete('/:cardId', function($pageId, $cardId) use ($app){
        $app->deleteCard($pageId, $cardId);
      });

    });

  });
});

$slimApp->run();
