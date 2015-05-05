<?php

namespace mangeld;

use mangeld\obj\JsonResponse;

class PostCheckMiddleware extends \Slim\Middleware
{

  private $landingApp;

  public function __construct(\mangeld\App $app)
  { $this->landingApp = $app; }

  /**
   * Call
   *
   * Perform actions specific to this middleware and optionally
   * call the next downstream middleware.
   */
  public function call()
  {

    $app = $this->app;
    $landingPage = $this->landingApp;

    if( $app->request->isPost() )
      $this->checkPostSize();

    $this->next->call();
  }

  private function checkPostSize()
  {
    $maxSize = $this->landingApp->getMaxPostSize();
    $app = $this->app;

    $this->app->hook('slim.before.router', function() use ($maxSize, $app){

      $req = $app->request;
      $length = $req->getContentLength();

      if( $length > $maxSize )
      {
        $app->response->setStatus( 413 );
        $app->response->headers()->set('Content-Type', 'application/json');
        $msg = "The post data is too large, max size is $maxSize";
        $json = JsonResponse::postTooLargeFactory($msg)->jsonSerialize();
        $app->response->setBody($json);
        $app->stop();
      }
  });

  }
}
