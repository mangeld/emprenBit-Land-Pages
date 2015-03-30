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
    $this->db->fetchPages();
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