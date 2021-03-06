<?php

namespace mangeld\db;

use mangeld\Config;
use mangeld\obj\Form;
use mangeld\obj\Page;

class DB implements DBInterface
{
  private $pdo;
  /**
   * @var Config
   */
  private $config = null;
  private static $sql_select_page_by_id = <<<SQL
    SELECT `idPages`, `name`, `creationDate`, `owner`, `title`, `description`, `logoId`, `color`, `backgroundColor`, `formText` FROM `Pages` WHERE `idPages` = ?
SQL;
  private static $sql_select_page_by_email = <<<SQL
    SELECT * FROM Pages WHERE owner = ( SELECT userId FROM Users WHERE email = ? )
SQL;
  private static $sql_insert_card = <<<SQL
  INSERT INTO `PageCards` (`idPage`, `idCard`, `cardTypeId`, `color`, `backgroundColor`) VALUES ( ?, ?, ?, ?, ? )
SQL;
  private static $sql_count_cards = <<<SQL
  SELECT count(`idCard`) count FROM `PageCards` WHERE `idPage` = ?
SQL;
  private static $sql_count_card_fields = <<<SQL
  SELECT count(`idCardContent`) count FROM `CardContent` WHERE `idCard` = ?
SQL;
  private static $sql_select_cards = <<<SQL
  SELECT `idPage`, `idCard`, `cardTypeId`, `color`, `backgroundColor` FROM `PageCards` WHERE `idPage` = ?
SQL;
  private static $sql_insert_card_content = <<<SQL
  INSERT INTO `CardContent` (`idCardContent`, `idCard`, `typeId`, `text`, `index`) VALUES ( ?, ?, ?, ?, ? )
SQL;
  private static $sql_select_card_content = <<<SQL
  SELECT `idCardContent`, `idCard`, `typeId`, `text`, `index` FROM `CardContent` WHERE `idCard` = ?
SQL;
  private static $sql_update_page = <<<SQL
  UPDATE `Pages` SET `name` = ?, `owner` = ?, `creationDate` = ?, `title` = ?, `description` = ?, `logoId` = ?, `color` = ?, `backgroundColor` = ?, `formText` = ? WHERE `idPages` = ?
SQL;
  private static $sql_update_user = <<<SQL
  UPDATE `Users` SET `registrationDate` = ?, `isAdmin` = ?, `email` = ?, `passwordHash` = ? WHERE `userId` = ?
SQL;
  private static $sql_update_card = <<<SQL
  UPDATE `PageCards` SET `index` = ?, `color` = ?, `backgroundColor` = ? WHERE `idCard` = ? AND `idPage` = ?
SQL;
  private static $sql_delete_card = <<<SQL
  DELETE FROM `PageCards` WHERE `idCard` = ?
SQL;
  private static $sql_insert_form = <<<SQL
  INSERT INTO `CompletedForms`
    (`formId`, `completionDate`, `sourceIp`, `pageId`, `field_name`, `field_email`)
  VALUES (?, ?, ?, ?, ?, ?);
SQL;
  private static $sql_select_form = <<<SQL
SELECT `formId`, `completionDate`, `sourceIp`, `pageId`, `field_name`, `field_email`
FROM `CompletedForms` WHERE `pageId` = ?;
SQL;
  private static $sql_update_card_fields = <<<SQL
UPDATE `landingPages`.`CardContent`
SET `text` = ?, `index` = ?
WHERE `idCardContent` = ? AND `idCard` = ?;
SQL;



  public function __construct()
  {
    $this->config = new Config();

    $this->pdo = new \PDO(
      "mysql:host={$this->config->mysqlHost()};" .
      "dbname={$this->config->mysqlDbName()}",
      $this->config->mysqlUser(),
      $this->config->mysqlPasswd()
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
   * @TODO: Make this a controller & split in save_page, save_cards, save_owner
   */
  public function savePage(\mangeld\obj\Page $page)
  {
    $status = true;

    if( !$this->insertPage($page) )
      $status = $this->updatePage($page);

    return $status;
  }

  public function deleteCard($cardId)
  {
    $prep = $this->pdo->prepare(self::$sql_delete_card);

    $prep->bindValue( 1, $cardId );

    return $prep->execute();
  }

  private function updatePage(\mangeld\obj\Page $page, $recursive = true)
  {
    $prep = $this->pdo->prepare(self::$sql_update_page);

    $prep->bindValue( 1, $page->getName() );
    $prep->bindValue( 2, $page->getOwner()->getUuid() );
    $prep->bindValue( 3, $page->getCreationTimestamp() );
    $prep->bindValue( 4, $page->getTitle() );
    $prep->bindValue( 5, $page->getDescription() );
    $prep->bindValue( 6, $page->getLogoId() );
    $prep->bindValue( 7, $page->getColor() );
    $prep->bindValue( 8, $page->getBackgroundColor() );
    $prep->bindValue( 9, $page->getFormText() );
    $prep->bindValue( 10, $page->getId() );

    $statPage = $prep->execute();

    if( $recursive && $page->getOwner() )
      $statUser = $this->updateUser($page->getOwner());

    return $statPage && $statUser;
  }

  private function updateUser(\mangeld\obj\User $user)
  {
    $prep = $this->pdo->prepare(self::$sql_update_user);

    $prep->bindValue( 1, $user->getResitrationDateTimestamp() );
    $prep->bindValue( 2, $user->isAdmin() );
    $prep->bindValue( 3, $user->getEmail() );
    $prep->bindValue( 4, null ); //TODO: Change this when authentication is implemented
    $prep->bindValue( 5, $user->getUuid() );

    return $prep->execute();
  }

  private function insertPage(\mangeld\obj\Page $page)
  {
    $userSaved = true;
    $cardsSaved = true;
    $formsSaved = true;

    if( $page->getOwner() != null )
      $userSaved = $this->saveUser( $page->getOwner() );

    $sql = 'insert into `Pages` (`idPages`, `name`, `owner`, `creationDate`, `title`, `description`, `logoId`, `color`, `backgroundColor`, `formText`)
    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
    $prepared = $this->pdo->prepare($sql);
    $prepared->bindValue(1, $page->getId());
    $prepared->bindValue(2, $page->getName());
    $prepared->bindValue(8, $page->getColor());
    $prepared->bindValue(9, $page->getBackgroundColor());
    $prepared->bindValue(10, $page->getFormText());

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

    if( $page->countCards() > 0 )
      $cardsSaved = $this->saveCards( $page->getCards() );

    if( $page->countForms() > 0 )
      $formsSaved = $this->saveForms( $page->getForms() );

    return $result && $userSaved && $cardsSaved && $formsSaved;
  }

  /**
   * @param $cards \mangeld\obj\Card[]
   */
  private function saveCards($cards)
  {
    $result = true;
    $fieldStatus = true;
    foreach( $cards as $id => $card )
    {
      $prepared = $this->pdo->prepare( self::$sql_insert_card );
      $prepared->bindValue( 1, $card->getPage()->getId() );
      $prepared->bindValue( 2, $card->getId() );
      $prepared->bindValue( 3, $card->getType() );
      $prepared->bindValue( 4, $card->getColor() );
      $prepared->bindValue( 5, $card->getBackgroundColor() );
      $status = $prepared->execute();

      if( $card->countFields() > 0 )
        $fieldStatus = $this->saveCardFields($card);

      $result = $status && $fieldStatus && $result;
    }
    return $result;
  }

  private function saveCardFields(\mangeld\obj\Card $card)
  {
    $result = true;
    foreach( $card->getFields() as $id => $field )
    {
      $prepared = $this->pdo->prepare( self::$sql_insert_card_content );
      $prepared->bindValue( 1, $field->getId() );
      $prepared->bindValue( 2, $field->getCard()->getId() );
      $prepared->bindValue( 3, $field->getType() );
      $prepared->bindValue( 4, $field->getText() );
      $prepared->bindValue( 5, $field->getIndex() );
      $status = $prepared->execute();
      $result = $status && $result;
    }
    return $result;
  }

  public function updateCard(\mangeld\obj\Card $card)
  {
    $cardUpdated = true;

    $prepared = $this->pdo->prepare( self::$sql_update_card );
    $prepared->bindValue( 1, 0 );
    $prepared->bindValue( 2, $card->getColor() );
    $prepared->bindValue( 3, $card->getBackgroundColor() );
    $prepared->bindValue( 4, $card->getId() );
    $prepared->bindValue( 5, $card->getPage()->getId() );
    $cardUpdated = $prepared->execute();

    return $this->updateCardFields( $card ) && $cardUpdated;
  }

  private function updateCardFields(\mangeld\obj\Card $card)
  {
    $allFieldsUpdated = true;

    foreach( $card->getFields() as $id => $field )
    {
      $prepared = $this->pdo->prepare( self::$sql_update_card_fields );
      $prepared->bindValue( 1, $field->getText() );
      $prepared->bindValue( 2, $field->getIndex() );
      $prepared->bindValue( 3, $field->getId() );
      $prepared->bindValue( 4, $card->getId() );
      $allFieldsUpdated = $allFieldsUpdated && $prepared->execute();
    }

    return $allFieldsUpdated;
  }

  /**
   * @return \mangeld\obj\Page[] A key => value array where the key
   * is the id of the page and the value the page itself.
   */
  public function fetchPages()
  {
    $sql = 'SELECT `idPages`, `name`, `creationDate`, `title`, `description`, `owner`, `logoId`, `color`, `backgroundColor`, `formText` FROM `Pages`';
    $prepared = $this->pdo->prepare($sql);
    $prepared->execute();

    $pages = array();

    while( $row = $prepared->fetch(\PDO::FETCH_OBJ) )
    {
      $page = $this->buildPage($row);

      if( $this->countCards( $page->getId() ) > 0 )
        $this->fetchCardsIntoPage($page);

      if( $page->countCards() > 0 )
        foreach( $page->getCards() as $key => $card )
          if( $this->countCardFields($card->getId()) > 0 )
            $this->fetchFieldsIntoCard($card);

      $pages[$page->getId()] = $page;
    }

    $prepared = null;
    return $pages;
  }

  private function fetchCardsIntoPage(\mangeld\obj\Page &$page)
  {
    $prepared = $this->pdo->prepare( self::$sql_select_cards );
    $prepared->bindValue( 1, $page->getId() );
    $prepared->execute();

    while( $row = $prepared->fetch(\PDO::FETCH_OBJ) )
      $page->addCard( $this->buildCard($row) );
  }

  private function fetchFieldsIntoCard(\mangeld\obj\Card &$card)
  {
    $prepared = $this->pdo->prepare( self::$sql_select_card_content );
    $prepared->bindValue( 1, $card->getId() );
    $status = $prepared->execute();

    while( $row = $prepared->fetchObject() )
      $card->addField( $this->buildCardFields($row) );
  }

  private function buildCardFields($row)
  {
    $field = \mangeld\obj\CardField::createField(
      $row->typeId,
      $row->idCardContent
    );
    $field->setText( $row->text );
    $field->setIndex( $row->index );

    return $field;
  }

  private function buildCard($row)
  {
    $card = \mangeld\obj\Card::createCard($row->cardTypeId, $row->idCard);
    $card->setColor( $row->color );
    $card->setBackgroundColor( $row->backgroundColor );
    return $card;
  }

  private function countCards($pageId)
  {
    $prepared = $this->pdo->prepare( self::$sql_count_cards );
    $prepared->bindValue( 1, $pageId );
    $status = $prepared->execute();
    $row = $prepared->fetchObject();

    return $row->count;
  }

  private function countCardFields($cardId)
  {
    $prepared = $this->pdo->prepare( self::$sql_count_card_fields );
    $prepared->bindValue( 1, $cardId );
    $prepared->execute();
    $row = $prepared->fetchObject();

    return $row->count;
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

    $page = $this->buildPage($row);
    if( $this->countCards($page->getId()) > 0 )
      $this->fetchCardsIntoPage( $page );

    if( $page->countCards() > 0 )
      foreach( $page->getCards() as $key => $card )
        if( $this->countCardFields($card->getId()) > 0 )
          $this->fetchFieldsIntoCard($card);

    return $page;
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
    $page->setColor( $row->color );
    $page->setBackgroundColor( $row->backgroundColor );
    $page->setFormText( $row->formText );
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

  public function saveForms($forms)
  {
    $formsSaved = true;

    foreach( $forms as $id => $form )
      $formsSaved = $this->saveForm($form) && $formsSaved;

    return $formsSaved;
  }

  public function saveForm(Form $form)
  {
    $prep = $this->pdo->prepare(self::$sql_insert_form);
    $prep->bindValue( 1, $form->getId() );
    $prep->bindValue( 2, $form->getCompletionDate() );
    $prep->bindValue( 3, $form->getSourceIp() );
    $prep->bindValue( 4, $form->getPage()->getId() );
    $prep->bindValue( 5, $form->getName() );
    $prep->bindValue( 6, $form->getEmail() );
    $status = $prep->execute();

    return $status;
  }

  public function fetchForms($pageID)
  {
    $prep = $this->pdo->prepare(self::$sql_select_form);
    $prep->bindValue( 1, $pageID );
    $status = $prep->execute();

    $forms = array();

    while( $row = $prep->fetchObject() )
    {
      $form = Form::buildForm($row);
      $forms[ $form->getId() ] = $form;
    }

    return $forms;
  }
}
