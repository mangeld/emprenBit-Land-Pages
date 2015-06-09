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
  $loader = new Twig_Loader_Filesystem('../templates/bootstrap');
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
    $page_id = $slimApp->request->params('page_id');

    if( $app->getPage( $page_id ) )
    {
      $file = \mangeld\lib\filesystem\File::fromUploadedFile('data');
      $file->saveToStorage( $slimApp->request->params('page_id') );

      $response = new \StdClass();
      $response->owner = $file->getOwnerId();
      $response->id = $file->getId();

      $slimApp->response->setBody( json_encode( $response ) );
    }
    else
    {
      $slimApp->response->setStatus(404);
    }


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
        try
        {
          $app->addForm($pageId, $params);
        } catch ( Exception $e )
        {
          $log = \mangeld\lib\Logger::instance();
          $log->error($e);
          $log->close();
          $slimApp->redirect($slimApp->request->getReferer());
        }
        $slimApp->redirect( $slimApp->request->getReferer() );
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
        $app->addCard($pageId);
      });

      $slimApp->put('/:cardId', function($pageId, $cardId) use ($slimApp, $app){
        $app->updateCard($slimApp->request->params('data'), $pageId, $cardId);
      });

      $slimApp->delete('/:cardId', function($pageId, $cardId) use ($app){
        $app->deleteCard($pageId, $cardId);
      });

    });

  });
});

$slimApp->run();
