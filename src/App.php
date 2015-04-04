<?php

namespace mangeld;

class App
{
  private $name = "";
  private $db = null;
  private $uuidGen = null;

  public function __construct($db = null, $idgen = null)
  {
    $this->db = $db;
    $this->uuidGen = $idgen;
  }

  public static function createApp()
  {
    $db = new \mangeld\db\DB();
    $idGen = \Rhumsaa\Uuid\Uuid::uuid4();
    //TODO: DO NOT Retrieve a single uuid4, but
    //a class that generates a new one every time
    //it's requested
    $app = new \mangeld\App($db, $idGen);
    return $app;
  }

  public function closeDB()
  {
    $this->db->close();
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function deletePage($pageId)
  {
    $this->db->deletePage($pageId);
  }

  public function getPages()
  {
    $pages = $this->db->fetchPages();

    if( !is_array($pages) ) return;

    $jsonArr = array(
      'status_code' => 200,
      'body' => array()
    );

    foreach($pages as $page)
    {
      $jsonArr['body'][] = array(
        'name' => $page->getName(),
        'creation_timestamp' => $page->getCreationTimestamp(),
        'id' => $page->getId()
      );
    }

    return json_encode($jsonArr);
  }

  public function createPage($name)
  {
    $time = microtime(true);
    $page = \mangeld\obj\Page::createPage();
    $page->setName($name);
    $page->setId( $this->uuidGen->toString() );
    $page->setCreationTimestamp($time);
    $this->db->savePage($page);
    return $page;
  }
}