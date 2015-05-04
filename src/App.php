<?php

namespace mangeld;

use mangeld\exceptions\FileUploadException;
use mangeld\lib\filesystem\File;
use mangeld\obj\Card;
use mangeld\obj\DataTypes;
use Rhumsaa\Uuid\Console\Exception;

class App
{
  private $name = "";
  /**
   * @var \mangeld\db\DB
   */
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

  public function deleteCard($cardId)
  {
    $this->db->deleteCard($cardId);
  }

  public function deletePage($pageId)
  {
    $this->db->deletePage($pageId);
  }

  public function addCard($cardData, $pageId)
  {
    @$pages = $this->getPagesAsObj();
    @$page = $pages[$pageId];
    unset($pages);

    if( $page != null )
    {
      $jsonObj = json_decode($cardData);
      $card = Card::createCard(DataTypes::cardThreeColumns);

      for($i = 1; $i < 4; $i++)
        $card->setTitle($jsonObj->fieldTitle[$i], $i);

      for($i = 1; $i < 4; $i++)
        $card->setBody($jsonObj->fieldText[$i], $i);

      for($i = 1; $i < 4; $i++)
      {
        try
        {
          $img = File::fromUploadedFile('image'.($i-1));
          $img->saveToStorage($page);
        } catch (Exception $e) {}
        $card->setImage("{$img->getId()}", $i);
      }
      $page->addCard($card);
      $result = $this->db->savePage($page);

      return true;
    }
    else return false;
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
      $obj->logo = ($page->getLogoId() ? "storage/{$page->getId()}/{$page->getLogoId()}.jpg" : '');

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
              $cardJson->{$typeName}[(integer) $index] = $field->getText();
            }
            $obj->cards[DataTypes::typeName($card->getType())][] = $cardJson;
          }
          else
            $obj->cards = new \StdClass();
        }


      $jsonArr['body'][] = $obj;
    }

    return json_encode($jsonArr);
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
