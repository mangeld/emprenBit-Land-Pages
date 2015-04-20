<?php

namespace mangeld\db;

class DB implements DBInterface
{
  private $pdo;
  private static $sql_select_page_by_id = <<<SQL
    SELECT `idPages`, `name`, `creationDate`, `owner`, `title`, `description`, `logoId` FROM `Pages` WHERE `idPages` = ?
SQL;
  private static $sql_select_page_by_email = <<<SQL
    SELECT * FROM Pages WHERE owner = ( SELECT userId FROM Users WHERE email = ? )
SQL;

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

  public function close()
  {
    if( $this->pdo )
      $this->pdo = null;
  }

  /**
   * @param \mangeld\obj\Page $page
   * @return bool
   */
  public function savePage(\mangeld\obj\Page $page)
  {
    $userSaved = true;
    if( $page->getOwner() != null )
      $userSaved = $this->saveUser( $page->getOwner() );

    $sql = 'insert into `Pages` (`idPages`, `name`, `owner`, `creationDate`, `title`, `description`, `logoId`)
    values (?, ?, ?, ?, ?, ?, ?);';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue(1, $page->getId());
    $prepared->bindValue(2, $page->getName());

    if( $page->getOwner() != null )
      $prepared->bindValue(3, $page->getOwner()->getUuid() );
    else
      $prepared->bindValue(3, null);

    $prepared->bindValue(4, $page->getCreationTimestamp());
    $prepared->bindValue(5, $page->getTitle());
    $prepared->bindValue(6, $page->getDescription());
    if( $page->getLogoId() )
      $prepared->bindValue(7, $page->getLogoId());
    else
      $prepared->bindValue(7, null);
    $result = $prepared->execute();
    $prepared = null;

    return $result && $userSaved;
  }

  /**
   * @return \mangeld\obj\Page[] A key => value array where the key
   * is the id of the page and the value the page itself.
   */
  public function fetchPages()
  {
    $sql = 'SELECT `idPages`, `name`, `creationDate`, `title`, `description`, `owner`, `logoId` FROM `Pages`';
    $prepared = $this->pdo->prepare($sql);
    $prepared->execute();

    $pages = array();

    while( $row = $prepared->fetch(\PDO::FETCH_OBJ) )
    {
      $pages[$row->idPages] = $this->buildPage($row);
    }

    $prepared = null;
    return $pages;
  }

  /**
   * @param $page string | \mangeld\obj\Page
   * @return bool
   */
  public function deletePage($page)
  {
    if( $page instanceof \mangeld\obj\Page)
      $pageId = $page->getId();
    else
      $pageId = $page;

    $sql = 'DELETE FROM `Pages` WHERE `idPages` = ?';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue( 1, $pageId );
    $result = $prepared->execute();
    $prepared = null;
    return $result;
  }

  /**
   * @param $pageId
   * @return bool|\mangeld\obj\Page
   */
  public function fetchPage($pageId)
  {
    $sql = self::$sql_select_page_by_id;
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue(1, $pageId);
    $prepared->execute();
    $row = $prepared->fetch(\PDO::FETCH_OBJ);

    if($row === false)
    {
      $prepared = null;
      return false;
    }

    $prepared = null;
    return $this->buildPage($row);
  }

  public function fetchPageByEmail($email)
  {
    $sql = self::$sql_select_page_by_email;
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue( 1, $email );
    $prepared->execute();
    $row = $prepared->fetch(\PDO::FETCH_OBJ);

    if( $row === false )
    {
      $prepared = null;
      return false;
    }

    $prepared = null;
    return $this->buildPage($row);
  }

  private function buildPage($row)
  {
    $page = \mangeld\obj\Page::createPage();
    $page->setName( $row->name );
    $page->setCreationTimestamp( (double) $row->creationDate );
    $page->setId( $row->idPages );
    $page->setTitle( $row->title );
    $page->setDescription( $row->description );
    if( $row->logoId )
      $page->setLogoId( $row->logoId );

    if( $row->owner )
      $page->setOwner( $this->fetchUser( $row->owner ) );

    return $page;
  }

  /**
   * @param \mangeld\obj\User $user
   * @return boolean
   */
  public function saveUser(\mangeld\obj\User $user)
  {
    $sql = 'INSERT INTO `Users` (`userId`, `registrationDate`, `isAdmin`, `email`) values (?, ?, ?, ?);';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue( 1, $user->getUuid() );
    $prepared->bindValue( 2, $user->getResitrationDateTimestamp() );
    $prepared->bindValue( 3, $user->isAdmin() );
    $prepared->bindValue( 4, $user->getEmail() );
    $result = $prepared->execute();

    $prepared = null;
    return $result;
  }

  /**
   * @param $userId
   * @return bool|\mangeld\obj\User
   */
  public function fetchUser($userId)
  {
    $sql = 'SELECT `userId`, `registrationDate`, `isAdmin`, `email` FROM `Users` WHERE `userId` = ?';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue( 1, $userId );
    $prepared->execute();
    $row = $prepared->fetch(\PDO::FETCH_OBJ);

    if( $row == false )
    {
      $prepared = null;
      return false;
    }

    $user = \mangeld\obj\User::createUserWithId( $row->userId );
    $user->setRegistrationDateTimestamp( (double) $row->registrationDate );
    $user->setAdmin( (boolean) $row->isAdmin );
    $user->setEmail( $row->email );

    $prepared = null;
    return $user;
  }
}