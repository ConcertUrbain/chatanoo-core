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

  class Table_CommentsTest extends PHPUnit_Extensions_Database_TestCase
  {
    /**
     * Table users
     *
     * @var Table_Comments
     */
    private $_commentsTable;

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;


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
      $this->_commentsTable = new Table_Comments();
    }

    public function testInitialisation()
    {
      $this->assertDataSetsEqual(
        $this->getDataSet(),
        $this->getConnection()->createDataSet()
      );
    }

    public function testCreateRow()
    {
      $commentRowArray = $this->_commentsTable->createRow()->toArray();
      $this->assertArrayHasKey('id', $commentRowArray);
      $this->assertArrayHasKey('content', $commentRowArray);
      $this->assertArrayHasKey('addDate', $commentRowArray);
      $this->assertArrayHasKey('setDate', $commentRowArray);
      $this->assertArrayHasKey('isValid', $commentRowArray);
      $this->assertArrayHasKey('users_id', $commentRowArray);
      $this->assertArrayHasKey('items_id', $commentRowArray);
    }

    public function testFind()
    {
      $commentRow = $this->_commentsTable->find(1)->current();
      $this->assertEquals($commentRow->id, 1);
      $this->assertEquals($commentRow->content, 'Un commentaire');
      $this->assertEquals($commentRow->addDate, '2009-04-15 23:55:36');
      $this->assertEquals($commentRow->setDate, '2009-04-15 23:55:36');
      $this->assertEquals($commentRow->isValid, 1);
      $this->assertEquals($commentRow->users_id, 1);
      $this->assertEquals($commentRow->items_id, 1);
    }

    public function testAddComment()
    {
      $commentArray = array(
        'content' => 'Un commentaire 2',
        'addDate' => '2009-04-15 23:55:36',
        'setDate' => '2009-04-15 23:55:36',
        'isValid' => 1,
        'users_id' => 1,
        'sessions_id' => 1,
        'items_id' => 1
      );
      $commentRow = $this->_commentsTable->createRow($commentArray);
      $commentRow->save();

      $insertXml = dirname(__FILE__) . '/Flats/comments-insert-data.xml';
      $this->assertDataSetsEqual(
        $this->createFlatXMLDataSet($insertXml),
        $this->getConnection()->createDataSet()
      );
    }

    public function testDeleteComment()
    {
      $commentRow = $this->_commentsTable->find(1)->current();
      $commentRow->delete();
      $this->assertEquals($this->_commentsTable->fetchAll()->count(), 0);
    }

    public function testFindParentItem()
    {
      $commentRow = $this->_commentsTable->find(1)->current();
      $itemRow = $commentRow->findParentTable_Items();
      $this->assertEquals($itemRow->id, 1);
      $this->assertEquals($itemRow->title, 'Mon Item');
      $this->assertEquals($itemRow->description, 'Ma description');
      $this->assertEquals($itemRow->addDate, '2009-04-15 23:55:36');
      $this->assertEquals($itemRow->setDate, '2009-04-15 23:55:36');
      $this->assertEquals($itemRow->isValid, 1);
      $this->assertEquals($itemRow->users_id, 1);
    }

    public function testFindParentUser()
    {
      $commentRow = $this->_commentsTable->find(1)->current();
      $userRow = $commentRow->findParentTable_Users();
      $this->assertEquals($userRow->id, 1);
      $this->assertEquals($userRow->firstName, 'Desve');
      $this->assertEquals($userRow->lastName, 'Mathieu');
      $this->assertEquals($userRow->pseudo, 'mazerte');
      //$this->assertEquals($userRow->password, 'desperados');
      $this->assertEquals($userRow->email, 'mathieu.desve@unflux.fr');
      $this->assertEquals($userRow->role, 'admin');
      $this->assertEquals($userRow->addDate, '2009-04-15 23:55:36');
      $this->assertEquals($userRow->setDate, '2009-04-15 23:55:36');
      $this->assertEquals($userRow->isBan, 0);
    }

    public function testGetParentDatasCarto()
    {
      $db = Zend_Registry::get('db');
      $select = $db->select();
      $select->from('items', 'datas_carto.*')
          ->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
          ->join('datas_carto', 'datas_assoc.datas_id = datas_carto.id')
          ->where('items.id = 1')
          ->where("datas_assoc.dataType = 'Carto'")
          ->where("datas_assoc.assocType = 'Comment'");

      $datasArray = $db->fetchAll($select);

      $this->assertEquals($datasArray[0]['id'], 1);
      $this->assertEquals($datasArray[0]['x'], 1.89489e+06);
      $this->assertEquals($datasArray[0]['y'], 7881);
      $this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
    }

    public function testGetParentDatasVote()
    {
      $db = Zend_Registry::get('db');
      $select = $db->select();
      $select->from('items', 'datas_vote.*')
          ->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
          ->join('datas_vote', 'datas_assoc.datas_id = datas_vote.id')
          ->where('items.id = 1')
          ->where("datas_assoc.dataType = 'Vote'")
          ->where("datas_assoc.assocType = 'Comment'");

      $datasArray = $db->fetchAll($select);

      $this->assertEquals($datasArray[0]['id'], 1);
      $this->assertEquals($datasArray[0]['rate'], 1);
      $this->assertEquals($datasArray[0]['users_id'], 1);
      $this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }