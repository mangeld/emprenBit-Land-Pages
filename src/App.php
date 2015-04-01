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
    $app = new \mangeld\App($db);
    return $app;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getPages()
  {
    $pages = $this->db->fetchPages();

    if( !is_array($pages) ) return;

    $jsonArr = [
      'status_code' => 200,
      'body' => []
    ];

    foreach($pages as $page)
    {
      $jsonArr['body'][] = [
        'name' => $page->getName(),
        'creation_timestamp' => $page->getCreationTimestamp(),
        'id' => $page->getId()
      ];
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
  }
}