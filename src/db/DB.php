<?php

namespace mangeld\db;

class DB implements DBInterface
{

  private $pdo;

  public function __construct()
  {
    $pass = 'toor'; //TODO: Load this from configuration obj
    $env = getenv('MYSQLPASSWD');
    if( $env !== false )
      $pass = $env;

    $this->pdo = new \PDO(
      'mysql:host=localhost;dbname=landingPages',
      'root',
      $pass
    );
  }

  public function savePage(\mangeld\obj\Page $page)
  {
    $sql = 'insert into `Pages` (`idPages`, `name`, `owner`, `creationDate`)
    values (?, ?, ?, ?);';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue(1, $page->getId());
    $prepared->bindValue(2, $page->getName());
    $prepared->bindValue(3, null);
    $prepared->bindValue(4, $page->getCreationTimestamp());
    return $prepared->execute();
  }

  public function fetchPages()
  {
    $sql = 'SELECT `idPages`, `name`, `creationDate` FROM `Pages`';
    $prepared = $this->pdo->prepare($sql);
    $prepared->execute();

    $pages = array();

    while( $row = $prepared->fetch(\PDO::FETCH_OBJ) )
    {
      $page = \mangeld\obj\Page::createPage();
      $page->setName( $row->name );
      $page->setId( $row->idPages );
      $page->setCreationTimestamp( (double) $row->creationDate );
      $pages[] = $page;
    }

    return $pages;
  }

  public function fetchPage($pageId)
  {
    $sql = 'SELECT `idPages`, `name`, `creationDate` FROM `Pages` WHERE `idPages` = ?';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue(1, $pageId);
    $prepared->execute();
    $row = $prepared->fetch(\PDO::FETCH_OBJ);

    $page = \mangeld\obj\Page::createPage();
    $page->setName( $row->name );
    $page->setCreationTimestamp( (double) $row->creationDate );
    $page->setId( $row->idPages );

    return $page;
  }
}