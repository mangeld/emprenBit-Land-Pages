<?php

$app = \mangeld\App::createApp();
$slimApp = new \Slim\Slim();
$slimApp->config('debug', true);
$slimApp->add( new \mangeld\PostCheckMiddleware($app) );

$slimApp->get('/admin', function() use ($slimApp) {
  $slimApp->response->redirect('/admin.html', 303);
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
    //TODO: Delete this endpoint
    $file = \mangeld\lib\filesystem\File::fromUploadedFile('image');
    $file->saveToStorage(\mangeld\obj\User::createUser());

  });

  $slimApp->group('/cards', function() use ($slimApp, $app){

    $slimApp->post('/:id', function($id) use ($slimApp, $app){

      $da = var_export($slimApp->request->params('data'), true);
      $slimApp->response->setBody( $da );
    });

  });

  $slimApp->group('/forms', function() use ($slimApp, $app){

    $slimApp->get('/:pageId', function($pageId) use ($slimApp, $app){
      $slimApp->response->setBody( $app->getForms($pageId) );
    });

    $slimApp->get('/:pageId/csv', function($pageId) use ($slimApp, $app){
      $app->getFormsAsCsv($pageId);
    });

    $slimApp->put('/:pageId', function($pageId) use ($slimApp, $app){
      $params = $slimApp->request->params();

      if( isset($params['name']) && isset($params['email']) )
      {
        $app->addForm($pageId, $params);
        $slimApp->response->setBody('OK');
      }
      else
      {
        $slimApp->response->setStatus(400);
        $slimApp->response->setBody('BAD REQUEST');
      }

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

    //TODO: If page exists update it
    $slimApp->put('/:id', function($id) use ($slimApp, $app){
      $app->updatePage($id, $slimApp->request->params('data'));
      //$slimApp->response->setBody( $slimApp->request->post('data') );
    });

    /**
     * Add a page by it's name.
     *
     * Returns: A json object with the page created.
     */
    $slimApp->post('/', function() use ($slimApp, $app){
      $json = $slimApp->request->post('data');
      $jsonObj = json_decode($json);
      //TODO: CREAR PAGINA Y DEVOLVER DATOS DE LA PAGINA CREADA, GUARDAR IMAGEN
      //TODO: Usar parametros y no un objeto json para mas compatibilidad
      //move_uploaded_file($_FILES['image']['tmp_name'], '../public/storage/'.$_FILES['image']['name']);
      $app->createPage($jsonObj);
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
