<?php

namespace mangeld;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use mangeld\exceptions\FileSystemException;
use mangeld\exceptions\FileUploadException;
use mangeld\lib\filesystem\File;
use mangeld\lib\Logger;
use mangeld\obj\Card;
use mangeld\obj\DataTypes;
use mangeld\obj\Form;
use mangeld\obj\Page;
use Rhumsaa\Uuid\Console\Exception;
use Slim\Slim;

class App
{
  private $name = "";
  /**
   * @var \mangeld\db\DB
   */
  private $db = null;
  private $uuidGen = null;
  private $log = null;

  public function __construct($db = null, $idgen = null)
  {
    $this->db = $db;
    $this->uuidGen = $idgen;
    $this->log = Logger::instance();

    if( !class_exists('Imagick') )
      $this->log->critical('Class Imagick not found');
  }

  public function __destruct()
    { Logger::close(); }

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

  public function deleteCard($pageId, $cardId)
  {
    $page = $this->db->fetchPage($pageId);
    $card = $page->getCard($cardId);

    foreach( $card->getFields() as $id => $field )
      if( $field->getType() == DataTypes::fieldImage )
        $this->deleteImageResource( $page->getId(), $field->getText() );

    $this->db->deleteCard($cardId);
  }

  private function deleteImageResource($pageId, $fileId)
  {
    $folder =
      Config::storage_folder . DIRECTORY_SEPARATOR .
      $pageId . DIRECTORY_SEPARATOR;
    $file = $fileId . '.jpg';

    foreach( Config::$image_sizes as $name => $value )
      try{ File::openFile( $folder . $name . '_' . $file )->delete(); }
      catch( FileSystemException $e ) {}
  }

  public function deletePage($pageId)
  {
    $page = $this->db->fetchPage($pageId);
    if( !$page ) return;
    if( $page->getLogoId() )
      $this->deleteImageResource($page->getId(), $page->getLogoId());

    if( $page->getCards() )
      foreach( $page->getCards() as $cardK => $card )
        foreach( $card->getFields() as $fieldId => $field )
          if( $field->getType() == DataTypes::fieldImage )
            $this->deleteImageResource( $page->getId(), $field->getText() );

    //TODO: Not so clean...
    @rmdir(Config::storage_folder . DIRECTORY_SEPARATOR . $page->getId());

    $this->db->deletePage($pageId);
  }

  public function addCard($pageId)
  {
    @$page = $this->db->fetchPage($pageId);
    $slim = Slim::getInstance();

    if( $page != null )
    {
      $card = null;
      switch( $slim->request->params('type') )
      {
        case DataTypes::typeName( DataTypes::cardThreeColumns ):
          $card = $this->buildCard3Col($slim);
          break;
        case DataTypes::typeName( DataTypes::cardCarousel ):
          $card = $this->buildCardCarousel($slim);
          break;
      }
      $page->addCard($card);
      $result = $this->db->savePage($page);
      return $result;
    }
    else return false;
  }

  private function buildCardCarousel(Slim $slim)
  {
    $card = Card::createCard(DataTypes::cardCarousel);
    $img = $slim->request->params('images');
    $img_c = count( $img );
    $txt = $slim->request->params('texts');
    $txt_c = count( $txt );

    for( $i = 0; $i < max($img_c, $txt_c); $i++ )
    {
      if( $img_c > $i && $txt_c > $i)
        $card->addImage($img[$i], $txt[$i], $i);
      else if( $img_c > $i && $txt_c < $i )
        $card->addImage($img[$i], "", $i);
      else
        $card->addImage("", $txt[$i], $i);
    }
    return $card;
  }

  private function buildCard3Col(Slim $slim)
  {
    $card = Card::createCard(DataTypes::cardThreeColumns);
    $card->setBody($slim->request->params('body1'), 1);
    $card->setBody($slim->request->params('body2'), 2);
    $card->setBody($slim->request->params('body3'), 3);
    $card->setTitle($slim->request->params('title1'), 1);
    $card->setTitle($slim->request->params('title2'), 2);
    $card->setTitle($slim->request->params('title3'), 3);
    $card->setImage($slim->request->params('image1'), 1);
    $card->setImage($slim->request->params('image2'), 2);
    $card->setImage($slim->request->params('image3'), 3);
    return $card;
  }

  public function updateCard($cardData, $pageId, $cardId)
  {
    $newCard = $this->buildCardFromJson($cardData, $pageId, $cardId);
    $this->deleteCard($pageId, $cardId);
    $page = $this->db->fetchPage($pageId);
    $page->addCard($newCard);
    $this->db->savePage($page);
  }

  private function buildCardFromJson($cardData, $pageID, $cardId = null)
  {
    $jsonObj = json_decode($cardData, false);
    $card = $cardId == null ? Card::createCard(DataTypes::cardThreeColumns) : Card::createCard(DataTypes::cardThreeColumns, $cardId);

    for($i = 1; $i < 4; $i++)
      $card->setTitle(
        isset($jsonObj->fieldTitle->$i) ? $jsonObj->fieldTitle->$i : $jsonObj->fieldTitle[$i],
        $i
      );

    for($i = 1; $i < 4; $i++)
      $card->setBody(
        isset($jsonObj->fieldText->$i) ? $jsonObj->fieldText->$i : $jsonObj->fieldText[$i],
        $i
      );

    for($i = 1; $i < 4; $i++)
    {
      try
      {
        $img = File::fromUploadedFile('image'.($i-1));
        $img->saveToStorage($pageID);
        $card->setImage("{$img->getId()}", $i);
      } catch (\Exception $e) {}
    }

    return $card;
  }

  public function getPage($pageId)
  {
    @$pages = $this->getPagesAsObj();
    @$page = $pages[$pageId];
    unset($pages);
    return $page;
  }

  public function getForms($pageId)
  {
    $page = $this->db->fetchPage($pageId);
    $forms = array();
    $this->buildFormsObj($page, $forms);
    $obj = new \StdClass();
    $obj->forms = $forms;
    return json_encode($obj);
  }

  public function addForm($pageId, $params)
  {
    $page = $this->db->fetchPage($pageId);
    $form = Form::createForm();
    $form->setName( $params['name'] );
    $form->setEmail( $params['email'] );
    $page->addForm( $form );
    $this->db->savePage( $page );
  }

  public function getFormsAsCsv($pageId)
  {
    $page = $this->db->fetchPage($pageId);
    $slim = Slim::getInstance();

    if( $page == null || $page->countForms() == 0 )
      $slim->notFound();

    $slim->response->headers()->set('Content-Type', 'text/csv');
    $slim->response->headers()->set('Content-Disposition', 'attachment; filename="'.$page->getName().'.csv"');

    $formsArray = array_map(function($elemnt){
      return $elemnt->asArray();
    }, $page->getForms());

    $exampleForm = new Form();

    $config = new ExporterConfig();
    $config
      ->setDelimiter(';')
      ->setColumnHeaders( array_keys( $exampleForm->asArray() ) );

    $exporter = new Exporter($config);
    $exporter->export('php://output', $formsArray);
  }

  public function getPages()
  {
    $pages = $this->db->fetchPages();
    //TODO: Put here a json builder (fetches data from class & converts to json) instead of doing it here
    if( !is_array($pages) ) return;

    $jsonArr = array(
      'status_code' => 200,
      'body' => array()
    );

    /** @var \mangeld\obj\Page[] $pages */
    foreach($pages as $page)
    {
      $obj = new \StdClass();
      $obj->name = $page->getName();
      $obj->creation_timestamp = $page->getCreationTimestamp();
      $obj->id = $page->getId();
      $obj->title = $page->getTitle();
      $obj->description = $page->getDescription();
      $obj->logo = ($page->getLogoId() ? "storage/{$page->getId()}/small_{$page->getLogoId()}.jpg" : '');

      if( $page->getOwner() )
        $obj->owner = $page->getOwner()->getEmail();
      else
        $obj->owner = '';

      if( $page->countCards() > 0)
        foreach( $page->getCards() as $key => $card )
        {
          if( $card->hasFields() )
          {
            $cardJson = new \StdClass;
            $cardJson->id = $card->getId();
            foreach( $card->getFields() as $key2 => $field )
            {
              $typeName = DataTypes::typeName($field->getType());
              $index = $field->getIndex();
              $cardJson->{$typeName}[ (integer) $index] = $field->getText();
            }
            $obj->cards[DataTypes::typeName($card->getType())][] = $cardJson;
          }
          else
            $obj->cards = new \StdClass();
        }

      if( $page->countForms() > 0)
        $this->buildFormsObj($page, $obj->forms);

      $jsonArr['body'][] = $obj;
    }

    return json_encode($jsonArr);
  }

  private function buildFormsObj(Page $page, &$forms)
  {
    foreach( $page->getForms() as $id => $form )
    {
      $objForm = new \StdClass();
      $objForm->name = $form->getName();
      $objForm->email = $form->getEmail();
      $objForm->sourceIp = $form->getSourceIp();
      $objForm->completionDate = $form->getCompletionDate();

      $forms[] = $objForm;
    }
  }

  public function getPagesAsObj()
  {
    return $this->db->fetchPages();
  }

  /**
   * @param \StdClass a standard class with
   * these public fields:
   * - name
   * - email
   * - title
   * - desc
   * @return obj\Page
   */
  public function createPage($jsonObj)
  {
    $page = \mangeld\obj\Page::createPageWithNewUser( $jsonObj->email );
    $page->setName($jsonObj->name);
    //TODO: Provide a better way to ignore optional fields
    @$page->setTitle( $jsonObj->title );
    @$page->setDescription( $jsonObj->description );
    try
    {
      $img = File::fromUploadedFile('image');
      $img->saveToStorage($page);
      $page->setLogoId($img->getId());
    } catch ( FileUploadException $e ) {}
    $this->db->savePage($page);
    return $page;
  }

  public function updatePage($pageId, $data)
  {
    $page = $this->db->fetchPage($pageId);
    if( $page == null ) return false;
    $obj = json_decode($data);

    $page->setTitle( $obj->title );
    $page->setName( $obj->name );
    $page->setDescription( $obj->description );
    $page->getOwner()->setEmail( $obj->email );

    try{
      $logo = File::fromUploadedFile('logo');
      $logo->saveToStorage($page);
      $this->deleteImageResource($page->getId(), $page->getLogoId());
      $page->setLogoId( $logo->getId() );
    } catch ( FileUploadException $e ) {}

    $this->db->savePage($page);
  }

  /**
   * @param $size
   * @return bool True if max size is exceeded
   */
  public function maxPostSizeExceeded($size)
  {
    return $size > $this->getMaxPostSize();
  }

  public function getMaxPostSize()
  {
    $max_upload = $this->human2byte( ini_get('upload_max_filesize') );
    $max_post = $this->human2byte( ini_get('post_max_size') );
    $memory_limit = $this->human2byte( ini_get('memory_limit') );
    return (int) min($max_upload, $max_post, $memory_limit);
  }

  private function human2byte($value)
  {
    return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
      switch (strtolower($m[2])) {
        case 't':
          $m[1] *= 1024;
        case 'g':
          $m[1] *= 1024;
        case 'm':
          $m[1] *= 1024;
        case 'k':
          $m[1] *= 1024;
      }
      return $m[1];
    }, $value);
  }
}
