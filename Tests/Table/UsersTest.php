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

  class Table_UsersTest extends PHPUnit_Extensions_Database_TestCase
  {
    /**
     * Table users
     *
     * @var Table_Users
     */
    private $_usersTable;

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
      $this->_usersTable = new Table_Users();
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
      $userRowArray = $this->_usersTable->createRow()->toArray();
      $this->assertArrayHasKey('id', $userRowArray);
      $this->assertArrayHasKey('firstName', $userRowArray);
      $this->assertArrayHasKey('lastName', $userRowArray);
      $this->assertArrayHasKey('pseudo', $userRowArray);
      $this->assertArrayHasKey('password', $userRowArray);
      $this->assertArrayHasKey('email', $userRowArray);
      $this->assertArrayHasKey('role', $userRowArray);
      $this->assertArrayHasKey('addDate', $userRowArray);
      $this->assertArrayHasKey('setDate', $userRowArray);
      $this->assertArrayHasKey('isBan', $userRowArray);
    }

    public function testFind()
    {
      $userRow = $this->_usersTable->find(1)->current();
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

    public function testAddUser()
    {
      $userArray = array(
        'firstName' => 'Tsolova',
        'lastName' => 'Irina',
        'pseudo' => 'amour',
        'password' => '3183c09bfdcaaa0bb97d',
        'email' => 'tsolova_irina@yahoo.com',
        'role' => 'user',
        'addDate' => '2009-04-15 23:55:36',
        'setDate' => '2009-04-15 23:55:36',
        'sessions_id' => 1,
        'isBan' => 0
      );
      $userRow = $this->_usersTable->createRow($userArray);
      $userRow->save();

      $insertXml = dirname(__FILE__) . '/Flats/users-insert-data.xml';
      /*$this->assertDataSetsEqual(
        $this->createFlatXMLDataSet($insertXml),
        $this->getConnection()->createDataSet()
      );*/
    }

    public function testDeleteUser()
    {
      $userRow = $this->_usersTable->find(1)->current();
      $userRow->delete();
      $this->assertEquals($this->_usersTable->fetchAll()->count(), 0);
    }

    public function testGetParentDatasAdress()
    {
      $db = Zend_Registry::get('db');
      $select = $db->select();
      $select->from('items', 'datas_adress.*')
          ->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
          ->join('datas_adress', 'datas_assoc.datas_id = datas_adress.id')
          ->where('items.id = 1')
          ->where("datas_assoc.dataType = 'Adress'")
          ->where("datas_assoc.assocType = 'User'");

      $datasArray = $db->fetchAll($select);

      $this->assertEquals($datasArray[0]['id'], 1);
      $this->assertEquals($datasArray[0]['adress'], "8, rue de la Marne");
      $this->assertEquals($datasArray[0]['zipCode'], 94500);
      $this->assertEquals($datasArray[0]['city'], "Champigny");
      $this->assertEquals($datasArray[0]['country'], 'France');
      $this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
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
          ->where("datas_assoc.assocType = 'User'");

      $datasArray = $db->fetchAll($select);

      $this->assertEquals($datasArray[0]['id'], 1);
      $this->assertEquals($datasArray[0]['x'], 1.89489e+06);
      $this->assertEquals($datasArray[0]['y'], 7881);
      $this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }