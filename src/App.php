<?php

namespace mangeld;

use mangeld\obj\DataTypes;

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

  public function deletePage($pageId)
  {
    $this->db->deletePage($pageId);
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

      if( $page->getOwner() )
        $obj->owner = $page->getOwner()->getEmail();
      else
        $obj->owner = '';

      if( $page->countCards() > 0)
        foreach( $page->getCards() as $key => $card )
        {
          if( $card->hasFields() )
          {
            $cardJson = array( );
            foreach( $card->getFields() as $key2 => $field )
            {
              $typeName = DataTypes::typeName($field->getType());
              $index = $field->getIndex();
              $cardJson[$typeName][(integer) $index] = $field->getText();
            }
            $obj->cards[DataTypes::typeName($card->getType())][] = $cardJson;
          }
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

    if( $page->cards )

    $this->db->savePage($page);
    return $page;
  }
}
