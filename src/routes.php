<?php

$app = \mangeld\App::createApp();
$slimApp = new \Slim\Slim();
$slimApp->config('debug', true);

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
  $loader = new Twig_Loader_Filesystem('../templates/landing1');
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
    }
    else
      $slimApp->notFound();
  }
});

$slimApp->group('/v1', function() use ($slimApp, $app){

  $slimApp->post('/upload', function() use($slimApp, $app){

    $file = \mangeld\lib\filesystem\File::fromUploadedFile('image');
    $file->saveToStorage(\mangeld\obj\User::createUser());

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

    $slimApp->group('/cards', function() use ($slimApp, $app){
      $slimApp->get('/', function() use ($slimApp, $app){
        $slimApp->response->setBody("CARD IN PAGES");
      });
    });

  });
});

$slimApp->run();
