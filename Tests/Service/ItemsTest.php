<?php

  set_include_path(implode(PATH_SEPARATOR, array(
      dirname(__FILE__) . '/../../Library',
      dirname(__FILE__) . '/../../Application',
      dirname(__FILE__) . '/../core',
      dirname(__FILE__),
      get_include_path(),
  )));
  require 'vendor/autoload.php';

  $autoloader = Zend_Loader_Autoloader::getInstance();
  $autoloader->setFallbackAutoloader(true);

  class Service_ItemsTest extends PHPUnit_Extensions_Database_TestCase
  {

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;

    /**
     * Table items
     *
     * @var Table_Items
     */
    private $_itemsTable;

    /**
     * Table metas_assoc
     *
     * @var Table_MetasAssoc
     */
    private $_metasAssocTable;

    /**
     * Table metas_assoc
     *
     * @var Table_Medias_Assoc
     */
    private $_mediasAssocTable;

    /**
     * Service Items
     *
     * @var Service_Items
     */
    private $_itemsService;

    /**
     * Service Coments
     *
     * @var Service_Comments
     */
    private $_commentsService;

    /**
     * Service Medias
     *
     * @var Service_Medias
     */
    private $_mediasService;

    /**
     * Service Datas
     *
     * @var Service_Datas
     */
    private $_datasService;

    /**
     * Service Users
     *
     * @var Service_Users
     */
    private $_usersService;


    public function __construct()
    {
      if(!Zend_Registry::isRegistered('db'))
      {
        $config = new Zend_Config_Xml(dirname(__FILE__) . '/../../Application/etc/config.xml', 'test');
        Zend_Registry::set('config', $config);

        $db = Zend_Db::factory($config->database);
        $this->_pdo = $db->getConnection();
        Zend_Registry::set('db', $db);

        Zend_Db_Table_Abstract::setDefaultAdapter($db);
      }
      else
      {
        $db = Zend_Registry::get('db');
        $this->_pdo = $db->getConnection();
      }
      
      Zend_Registry::set('userID', 1);
      Zend_Registry::set('sessionID', 1);
    }

      /**
       * Returns the test database connection.
       *
       * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
       */
    public function getConnection()
    {
      return $this->createDefaultDBConnection($this->_pdo, Zend_Registry::get('config')->database->dbname);
    }

      /**
       * Returns the test dataset.
       *
       * @return PHPUnit_Extensions_Database_DataSet_IDataSet
       */
    public function getDataSet()
        {
      $originalXml = dirname(__FILE__).'/Flats/all-original-data.xml';
      return $this->createFlatXMLDataSet($originalXml);
        }

    public function setUp()
    {
      parent::setUp();
      $this->_itemsTable = new Table_Items();
      $this->_itemsService = new Service_Items();
      $this->_commentsService = new Service_Comments();
      $this->_mediasService = new Service_Medias();
      $this->_datasService = new Service_Datas();
      $this->_usersService = new Service_Users();
      $this->_metasAssocTable = new Table_MetasAssoc();
      $this->_mediasAssocTable = new Table_Medias_Assoc();
    }

    public function testInitialisation()
    {
      $this->assertDataSetsEqual(
        $this->getDataSet(),
        $this->getConnection()->createDataSet()
      );
    }

    public function testGetItems()
    {
      $items = $this->_itemsService->getItems();

      $this->assertTrue(is_array($items));
      $this->assertEquals(count($items), 2);

      $item = $items[0];
      $this->assertTrue($item instanceof Vo_Item);

      $this->assertEquals($item->id, 1);
      $this->assertEquals($item->title, 'Mon Item');
      $this->assertEquals($item->description, 'Ma description');
      $this->assertEquals($item->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($item->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetItemById()
    {
      $item = $this->_itemsService->getItemById(1);
      $this->assertTrue($item instanceof Vo_Item);

      $this->assertEquals($item->id, 1);
      $this->assertEquals($item->title, 'Mon Item');
      $this->assertEquals($item->description, 'Ma description');
      $this->assertEquals($item->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($item->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

    }

    public function testGetItemsByQueryId()
    {
      $items = $this->_itemsService->getItemsByQueryId(1);

      $this->assertTrue(is_array($items));
      $this->assertEquals(count($items), 2);

      $item = $items[0];
      $this->assertTrue($item instanceof Vo_Item);

      $this->assertEquals($item->id, 1);
      $this->assertEquals($item->title, 'Mon Item');
      $this->assertEquals($item->description, 'Ma description');
      $this->assertEquals($item->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($item->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetItemsByMetaId()
    {
      $items = $this->_itemsService->getItemsByMetaId(1);

      $this->assertTrue(is_array($items));
      $this->assertEquals(count($items), 2);

      $item = $items[0];
      $this->assertTrue($item instanceof Vo_Item);

      $this->assertEquals($item->id, 1);
      $this->assertEquals($item->title, 'Mon Item');
      $this->assertEquals($item->description, 'Ma description');
      $this->assertEquals($item->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($item->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testAddItem()
    {
      $itemArray = array(
        'title' => 'Mon Item 2',
        'description' => 'Ma description 2',
        'isValid' => true,
        'users_id' => 1
      );
      $date = Zend_Date::now();

      $id = $this->_itemsService->addItem(new Vo_Item($itemArray));
      $item = $this->_itemsService->getItemById($id);
      $this->assertEquals($item->id, $id);
      $this->assertEquals($item->title, $itemArray['title']);
      $this->assertEquals($item->description, $itemArray['description']);
      $this->assertEquals($item->addDate, $date);
      $this->assertEquals($item->setDate, $date);
      $this->assertTrue($item->isValid());
    }

    public function testSetItem()
    {
      $date = Zend_Date::now();
      $item = $this->_itemsService->getItemById(1);
      $item->title = 'Mon Item modif';
      $this->_itemsService->setItem($item);
      $itemModif = $this->_itemsService->getItemById(1);
      $this->assertEquals($itemModif->id, 1);
      $this->assertEquals($itemModif->title, $item->title);
      $this->assertEquals($itemModif->description, $item->description);
      $this->assertEquals($itemModif->addDate, $item->addDate);
      $this->assertEquals($itemModif->setDate, $date);
      $this->assertEquals($itemModif->isValid(), $item->isValid());
    }

    public function testDeleteItem()
    {
      $this->assertEquals($this->_itemsTable->fetchAll()->count(), 2);
      $this->_itemsService->deleteItem(1);
      $this->assertEquals($this->_itemsTable->fetchAll()->count(), 1);
      $items = $this->_itemsService->getItems();
      $this->assertEquals(count($items), 1);
      $item = $this->_itemsService->getItemById(1);
      $this->assertEquals(count($item), 0);

      $datas = $this->_datasService->getDatasByItemId(1);
      $this->assertEquals(count($datas), 0);
    }

    public function testAddCommentIntoItem()
    {
      $commentArray = array(
        'content' => 'commentaire',
        'user' => 1
      );
      $comment = new Vo_Comment($commentArray);
      $this->_itemsService->addCommentIntoItem($comment, 1);
      $comments = $this->_commentsService->getCommentsByItemId(1);

      $this->assertEquals(count($comments), 2);
      $c = $comments[1];

      $this->assertEquals($c->id, 2);
      $this->assertEquals($c->content, $commentArray['content']);
    }

    public function testRemoveCommentFromItem()
    {
      $comments = $this->_commentsService->getCommentsByItemId(1);
      $this->assertEquals(count($comments), 1);
      $this->_itemsService->removeCommentFromItem(1, 1);
      $comments = $this->_commentsService->getCommentsByItemId(1);
      $this->assertEquals(count($comments), 0);
    }

    public function testAddMediaIntoItem()
    {
      $medias = $this->_mediasService->getMediasByItemId(1);
      $this->assertEquals(count($medias['Picture']), 1);

      $pictureArray = array(
        'title' => 'Title 2',
        'description' => 'Description 2',
        'url' => 'http://www.unsite.fr/',
        'width' => 800,
        'height' => 600,
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'user' => 1,
        'isValid' => true
      );
      $mediaPicture = new Vo_Media_Picture($pictureArray);
      $mediaPicture->id = $this->_itemsService->addMediaIntoItem($mediaPicture, 1);

      $medias = $this->_mediasService->getMediasByItemId(1);
      $this->assertEquals(count($medias['Picture']), 2);

      $picture = $medias['Picture'][1];
      $this->assertTrue($picture instanceof Vo_Media_Picture);
      $this->assertEquals($picture->id, 2);
      $this->assertEquals($picture->title, $pictureArray['title']);
      $this->assertEquals($picture->description, $pictureArray['description']);
      $this->assertEquals($picture->url, $pictureArray['url']);
      $this->assertEquals($picture->width, $pictureArray['width']);
      $this->assertEquals($picture->height, $pictureArray['height']);
      $this->assertEquals($picture->preview, $pictureArray['preview']);
      //$this->assertEquals($picture->addDate, Zend_Date::now());
      $this->assertEquals($picture->setDate, Zend_Date::now());
      $this->assertTrue($picture->isValid());
    }

    public function testRemoveMediaFromItem()
    {
      $select = $this->_mediasAssocTable->select();
      $select->where('medias_id = ?', 1)
          ->where('mediaType = ?', 'Video')
          ->where('assoc_id = ?', 1)
          ->where('assocType = ?', 'Item');
      $link = $this->_mediasAssocTable->fetchAll($select);
      $this->assertNotNull($link);

      $this->_itemsService->removeMediaFromItem(1, 'Video', 1);

      $link = $this->_mediasAssocTable->fetchAll($select);
      $this->assertNotNull($link);
    }

    public function testValidateVo()
    {
      $item = $this->_itemsService->getItemById(1);
      $this->assertTrue($item->isValid());

      $this->_itemsService->validateVo(1, false);
      $item = $this->_itemsService->getItemById(1);
      $this->assertFalse($item->isValid());
      $insertXml = dirname(__FILE__) . '/Flats/items-update2-data.xml';
      $this->assertDataSetsEqual(
        $this->createFlatXMLDataSet($insertXml),
        $this->getConnection()->createDataSet()
      );

      $this->_itemsService->validateVo(1, true);
      $item = $this->_itemsService->getItemById(1);
      $this->assertTrue($item->isValid());
      $this->assertDataSetsEqual(
        $this->getDataSet(),
        $this->getConnection()->createDataSet()
      );
    }

    public function testAddMetaIntoVo()
    {
      $meta = new Vo_Meta();
      $meta->content = 'meta';
      $meta->id = $this->_itemsService->addMetaIntoVo($meta, 1);

      $select = $this->_metasAssocTable->select();
      $select->where('metas_id = 2')
          ->where('assoc_id = 1')
          ->where('assocType = ?', 'Item');
      $link = $this->_metasAssocTable->fetchRow($select);
      $this->assertNotNull($link);
    }

    public function testRemoveMetaFromVo()
    {
      $select = $this->_metasAssocTable->select();
      $select->where('metas_id = 1')
          ->where('assoc_id = 1')
          ->where('assocType = ?', 'Item');
      $link = $this->_metasAssocTable->fetchRow($select);
      $this->assertNotNull($link);

      $this->_itemsService->removeMetaFromVo(1, 1);

      $link = $this->_metasAssocTable->fetchRow($select);
      $this->assertNull($link);
    }

    public function testAddDataIntoVo()
    {
      $date = Zend_Date::now();
      $cartoArray = array(
        'x' => 94500,
        'y' => 94100
      );
      $data = new Vo_Data_Carto($cartoArray);
      $data->id = $this->_itemsService->addDataIntoVo($data, 1);

      $datas = $this->_datasService->getDatasByItemId(1);
      $this->assertArrayHasKey('Adress', $datas);
      $this->assertArrayHasKey('Carto', $datas);
      $this->assertArrayHasKey('Vote', $datas);

      $this->assertEquals(count($datas['Carto']), 2);
      $carto = $datas['Carto'][1];
      $this->assertEquals($carto->id, 2);
      $this->assertEquals($carto->x, 94500);
      $this->assertEquals($carto->y, 94100);
      $this->assertEquals($carto->addDate, $date);
      $this->assertEquals($carto->setDate, $date);
    }

    public function testRemoveDataFromVo()
    {
      $this->_itemsService->removeDataFromVo(1, 'Carto', 1);
      $datas = $this->_datasService->getDatasByItemId(1);
      $this->assertArrayNotHasKey('Carto', $datas);
    }

    public function testGetUserFromVo()
    {
      $user = $this->_itemsService->getUserFromVo(1);

      $this->assertTrue($user instanceof Vo_User);

      $this->assertEquals($user->id, 1);
      $this->assertEquals($user->firstName, 'Desve');
      $this->assertEquals($user->lastName, 'Mathieu');
      $this->assertEquals($user->pseudo, 'mazerte');
      //$this->assertEquals($user->password, 'desperados');
      $this->assertEquals($user->email, 'mathieu.desve@unflux.fr');
      $this->assertEquals($user->role, 'admin');
      $this->assertEquals($user->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($user->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testSetUserOfVo()
    {
      $userArray = array(
        'firstName' => 'Tsolova',
        'lastName' => 'Irina',
        'pseudo' => 'Irina',
        'password' => 'irinator',
        'email' => 'tsolova_irina@yahoo.com',
        'role' => 'user'
      );
      $u = new Vo_User($userArray);
      $u->id = $this->_usersService->addUser($u);
      $this->_itemsService->setUserOfVo($u->id, 1);

      $user = $this->_itemsService->getUserFromVo(1);

      $this->assertTrue($user instanceof Vo_User);

      $this->assertEquals($user->id, 2);
      $this->assertEquals($user->firstName, 'Tsolova');
      $this->assertEquals($user->lastName, 'Irina');
      $this->assertEquals($user->pseudo, 'Irina');
      //$this->assertEquals($user->password, 'irinator');
      $this->assertEquals($user->email, 'tsolova_irina@yahoo.com');
      $this->assertEquals($user->role, 'user');
    }

    public function testGetVosByUserId()
    {
      $items = $this->_itemsService->getVosByUserId(1);

      $this->assertTrue(is_array($items));
      $this->assertEquals(count($items), 2);

      $item = $items[0];
      $this->assertTrue($item instanceof Vo_Item);

      $this->assertEquals($item->id, 1);
      $this->assertEquals($item->title, 'Mon Item');
      $this->assertEquals($item->description, 'Ma description');
      $this->assertEquals($item->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($item->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }

