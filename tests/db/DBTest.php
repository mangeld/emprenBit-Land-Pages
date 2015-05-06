<?php

use \mangeld\obj\Card;
use \mangeld\obj\CardField;
use \mangeld\obj\Page;
use \mangeld\obj\DataTypes;

class DBTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var \mangeld\db\DB
   */
  public $db;

	public function setUp()
	{
		$this->db = new \mangeld\db\DB();
	}

	public function testPageIsSaved()
	{
		$page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');
		$page->setName('Test Page Is Saved');
		$page->getId();
		$status = $this->db->savePage($page);

		$this->assertTrue($status);
	}

  public function testUserIsSaved()
  {
    $user = \mangeld\obj\User::createUser();
    $user->setEmail('test@test.com');
    $status = $this->db->saveUser($user);

    $this->assertNotFalse($status);
  }

  public function testUserIsFetched()
  {
    $user = \mangeld\obj\User::createUser();
    $user->setEmail('user@fetched.com');
    $id = $user->getUuid();
    $this->db->saveUser($user);
    $result = $this->db->fetchUser($id);

    $this->assertNotFalse($result);
    $this->assertEquals($id, $result->getUuid());
  }

  public function testPageWithNewUserIsSaved()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');
    $page->setName('Page with new user is saved');

    $this->db->savePage($page);
    $result = $this->db->fetchPage($page->getId());

    $this->assertAttributeInstanceOf('\mangeld\obj\User', 'owner', $result);
  }

	public function testPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPageWithNewUser('test@test.com');
		$page->setName('Test Page Is Fetched');
		$this->db->savePage($page);
		$result = $this->db->fetchPages();

		$this->assertGreaterThan(0, count($result));
	}

  public function testFullPageIsSavedAndRetrieved()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('juanito@juan.com');
    $page->setName("Nombre full");
    $page->setTitle('Title test');
    $page->setDescription('cool description');
    $page->setLogoId('4f65bb11-d1fb-41b9-8c53-55b05b3c7202');
    $this->db->savePage($page);
    $result = $this->db->fetchPage($page->getId());

    $this->assertEquals($page, $result, '', .0001);
  }

  public function testFullSpecificPageIsSavedAndRetrievedInList()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('testFullLista@test.com');
    $page->setName('CoolName');
    $page->setTitle('Cooler Title');
    $page->setDescription('Some random description');
    $page->setLogoId('4f65bb11-d1fb-41b9-8c53-55b05b3c7202');
    $this->db->savePage($page);
    $resultSet = $this->db->fetchPages();
    $interestingObj = $resultSet[$page->getId()];

    $this->assertEquals($page, $interestingObj, '', 0.0001);
  }

  public function testFullPageIsSavedAndRetrievedByEmail()
  {
    $page = $this->buildFullPage('fullPageByEmail@mail.com', 'Test Full Page Is Saved And Retrieved By Email');
    $this->db->savePage($page);
    $result = $this->db->fetchPageByEmail($page->getOwner()->getEmail());

    $this->assertEquals($page, $result, '', 0.0001);
  }

  private function buildFullPage($email, $name)
  {
    $page = \mangeld\obj\Page::createPageWithNewUser($email);
    $page->setName($name);
    $page->setTitle('Title title title title title title titletitle titletitle titletitle title');
    $page->setDescription('Description description description description description description');
    $page->setLogoId(\Rhumsaa\Uuid\Uuid::uuid4()->toString());
    return $page;
  }

	public function testPageIsDeleted()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Page to delete');
		$uid = $page->getId();
		$this->db->savePage($page);
		$status = $this->db->deletePage($page);
		$result = $this->db->fetchPage($uid);

		$this->assertEquals(true, $status);
		$this->assertEquals(false, $result);
	}

  public function testPageWithCardIsSavedAndRetrieved()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('testPageWithCard@test.com');
    $card = \mangeld\obj\Card::createEmptyCard();
    $page->addCard( $card );
    $page->setName( 'Test Page With Card Save & Retrieved' );
    $this->db->savePage( $page );
    $result = $this->db->fetchPage( $page->getId() );

    $this->assertEquals($page, $result, '', 0.001);
  }

	public function testPageIdIsDeleted()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('Page to delete');
		$uid = $page->getId();
		$this->db->savePage($page);
		$status = $this->db->deletePage($page->getId());
		$result = $this->db->fetchPage($uid);

		$this->assertEquals(true, $status);
		$this->assertEquals(false, $result);
	}

	public function testSpecificPageIsFetched()
	{
		$page = \mangeld\obj\Page::createPage();
		$page->setName('testSpecificPageIsFetched');
		$uid = $page->getId();
		$this->db->savePage($page);
		$result = $this->db->fetchPage($uid);

		$this->assertInstanceOf('\mangeld\obj\Page', $result);
		$this->assertEquals($uid, $result->getId());
	}

  public function testCardFieldsOfPageAreSaved()
  {
    $page = \mangeld\obj\Page::createPageWithNewUser('testCardFieldsSaved@test.io');
    $page->setName('testCardFieldsOfPageAreSaved');
    $uid = $page->getId();
    $card = Card::createCard(DataTypes::cardThreeColumns);
    $card->setBody('asdfasdfasdf1', 1);
    $card->setBody('asdfasdfasdf2', 2);
    $card->setBody('asdfasdfasdf3', 3);
    $card->setImage('asdasdasdasdasdasdasd1', 1);
    $card->setImage('asdasdasdasdasdasdasd2', 2);
    $card->setImage('asdasdasdasdasdasdasd3', 3);
    $card->setTitle('asdf343a4fe34faet1', 1);
    $card->setTitle('asdf343a4fe34faet2', 2);
    $card->setTitle('asdf343a4fe34faet3', 3);
    $page->addCard($card);

    $this->db->savePage( $page );
    $result = $this->db->fetchPage($uid);

    $this->assertEquals($page, $result, '', 0.0001);
  }

  public function testPageIsUpdated()
  {
    $originalPage = Page::createPageWithNewUser('testPageIsUpdated@update.io');
    $originalPage->setName('coolname');
    $originalPage->setTitle('coolTitle');
    $originalPage->setDescription('coolDesc');
    $this->db->savePage($originalPage);
    $modifiedPage = $this->db->fetchPage($originalPage->getId());
    $modifiedPage->setTitle('newTitle');
    $this->db->savePage($modifiedPage);

    $result = $this->db->fetchPage($originalPage->getId());

    $this->assertNotEquals($originalPage, $result, '', 0.0001);
    $this->assertEquals($modifiedPage, $result, '', 0.0001);
  }

  public function testUserOfPageIsUpdated()
  {
    $origPage = Page::createPageWithNewUser('testPageUserUpdated@update.io');
    $origPage->setName('name');
    $savedOriginal = $this->db->savePage($origPage);
    $modPage = $this->db->fetchPage($origPage->getId());
    $modPage->getOwner()->setEmail('userUpdated@changed.io');
    $savedMod = $this->db->savePage($modPage);

    $result = $this->db->fetchPage($origPage->getId());

    $this->assertTrue($savedMod);
    $this->assertTrue($savedOriginal);

    $this->assertNotEquals($origPage, $result, '', 0.0001);
    $this->assertEquals($modPage, $result, '', 0.0001);
  }

  public function testCardIsDeleted()
  {
    $page = Page::createPageWithNewUser('testCardIsDeleted@delete.this');
    $page->setName('Delete a card');
    $page->addCard(Card::createCard(DataTypes::cardThreeColumns));
    $cardToDelete = Card::createCard(DataTypes::cardThreeColumns);
    $cardToDelete->setTitle('adsfasdf',2);
    $cardToDelete->setBody('HOLA HOLA HOLA HOLA HOLA', 2);
    $cardToDelete->setImage('298347928374', 3);
    $cardId = $cardToDelete->getId();

    $this->db->savePage($page);
    $this->db->deleteCard($cardId);

    @$shouldBeEmpty = $this->db->fetchPage($page->getId())->getCard($cardId);

    $this->assertNull($shouldBeEmpty);
  }

  public function testFormIsAddedAndRetrieved()
  {
    $page = Page::createPageWithNewUser('testFormIsAddedAndRetrieved@additmuthafucka.add');
    $page->setName('sometest');
    $form = \mangeld\obj\Form::createForm();
    $form->setName('Some name');
    $page->addForm($form);
    $this->db->savePage( $page );
    //$this->db->saveForm($form);

    $res = $this->db->fetchPage($page->getId())->getForm($form->getId());

    $this->assertEquals($form, $res, '', 0.0001);
  }

  public function testLonelyFormIsSaved()
  {
    $page = Page::createPageWithNewUser('testLonelyform@soooLooonely.alo');
    $page->setName('Some lonely name');
    $this->db->savePage( $page );

    $pageRetrieved = $this->db->fetchPage($page->getId());
    $lonelyForm = \mangeld\obj\Form::createForm();
    $lonelyForm->setName('lonely form');
    $lonelyForm->setEmail('test@test.lon');
    $pageRetrieved->addForm($lonelyForm);
    $this->db->savePage( $pageRetrieved );
    $pageShouldHaveForm = $this->db->fetchPage( $page->getId() );

    $this->assertEquals($lonelyForm, $pageShouldHaveForm->getForm($lonelyForm->getId()), '', 0.0001);
  }

  public function testNewFormIsAddedToExistingOnes()
  {
    $page = Page::createPageWithNewUser('testFormsAndNewAreAdded@forms.form');
    $page->setName('Tha forms');
    $page->addForm(\mangeld\obj\Form::createForm());
    $page->addForm(\mangeld\obj\Form::createForm());
    $thisAswell = \mangeld\obj\Form::createForm();
    $page->addForm($thisAswell);
    $page->addForm(\mangeld\obj\Form::createForm());
    $this->db->savePage($page);
    $thisShoulExist = \mangeld\obj\Form::createForm();
    $thisShoulExist->setName('Hiiii');
    $page->addForm($thisShoulExist);
    $page->getForms();

    $this->assertEquals($thisShoulExist, $page->getForm($thisShoulExist->getId()));
    $this->assertEquals($thisAswell, $page->getForm($thisAswell->getId()));
  }
}
