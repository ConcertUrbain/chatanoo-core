<?php

  set_include_path(implode(PATH_SEPARATOR, array(
      dirname(__FILE__) . '/../Library',
      dirname(__FILE__) . '/core',
      dirname(__FILE__),
      get_include_path(),
  )));
  require 'vendor/autoload.php';

  require_once "Zend/Loader/Autoloader.php";
  $autoloader = Zend_Loader_Autoloader::getInstance();
  $autoloader->setFallbackAutoloader(true);

  class Vo_SessionTest extends PHPUnit_Extensions_Database_TestCase
  {

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;

    /**
     * Table Sessions
     *
     * @var Table_Sessions
     */
    private $_sessionsTable;

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
      $this->_sessionsTable = new Table_Sessions();
    }

    public function testInitialisation()
    {
      $this->assertDataSetsEqual(
        $this->getDataSet(),
        $this->getConnection()->createDataSet()
      );
    }

    public function testConstructArray()
    {
      $sessionArray = array(
        'id' => 1,
        '_user' => 1,
        'title' => 'Mon titre',
        'description' => 'Une description',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'publishDate' => '2007.03.10 00:00:00',
        'endDate' => Zend_Date::now()
      );
      $session = new Vo_Session($sessionArray);

      $this->assertEquals($session->id, $sessionArray['id']);
      $this->assertEquals($session->description, $sessionArray['description']);

      $this->assertTrue($session->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($session->addDate, new Zend_Date($sessionArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($session->setDate, new Zend_Date($sessionArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->publishDate instanceof Zend_Date, "publishDate isn't instance of Zend_Date.");
      $this->assertEquals($session->publishDate, new Zend_Date($sessionArray['publishDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->endDate instanceof Zend_Date, "endDate isn't instance of Zend_Date.");
      $this->assertEquals($session->endDate, new Zend_Date($sessionArray['endDate'], 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testConstructObject()
    {
      $sessionArray = array(
        'id' => 1,
        '_user' => 1,
        'title' => 'Mon titre',
        'description' => 'Une description',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'publishDate' => '2007.03.10 00:00:00',
        'endDate' => Zend_Date::now()
      );
      $sessionObject = new Vo_Session($sessionArray);

      $session = new Vo_Session($sessionObject);

      $this->assertEquals($session->id, $sessionArray['id']);
      $this->assertEquals($session->description, $sessionArray['description']);

      $this->assertTrue($session->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($session->addDate, new Zend_Date($sessionArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($session->setDate, new Zend_Date($sessionArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->publishDate instanceof Zend_Date, "publishDate isn't instance of Zend_Date.");
      $this->assertEquals($session->publishDate, new Zend_Date($sessionArray['publishDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->endDate instanceof Zend_Date, "endDate isn't instance of Zend_Date.");
      $this->assertEquals($session->endDate, new Zend_Date($sessionArray['endDate'], 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testConstructZendDbTableRow()
    {
      $sessionRow = $this->_sessionsTable->find(1)->current();
      $session = new Vo_Session($sessionRow);

      $this->assertEquals($session->id, $sessionRow->id);
      $this->assertEquals($session->description, $sessionRow->description);

      $this->assertTrue($session->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($session->addDate, new Zend_Date($sessionRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($session->setDate, new Zend_Date($sessionRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->publishDate instanceof Zend_Date, "publishDate isn't instance of Zend_Date.");
      $this->assertEquals($session->publishDate, new Zend_Date($sessionRow->publishDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($session->endDate instanceof Zend_Date, "endDate isn't instance of Zend_Date.");
      $this->assertEquals($session->endDate, new Zend_Date($sessionRow->endDate, 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testException()
    {
      $sessionArray = array(
        'id' => 1,
        '_user' => 1,
        'title' => 'Mon titre',
        'description' => 'Une description',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'publishDate' => '2007.03.10 00:00:00',
        'endDate' => Zend_Date::now()
      );
      $session = new Vo_Session($sessionArray);

      try {
        $session->bou = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 1);
      }

      try {
        $bou = $session->bou;
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 2);
      }

      try {
        $sessionString = new Vo_Session('bou');
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 3);
      }

      try {
        $session->addDate = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 4);
      }
    }

    public function testToArray()
    {
      $sessionArray = array(
        'id' => 1,
        '_user' => 1,
        'title' => 'Mon titre',
        'description' => 'Une description',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => '2007.03.10 00:00:00',
        'publishDate' => '2007.03.10 00:00:00',
        'endDate' => '2007.03.10 00:00:00'
      );
      $session = new Vo_Session($sessionArray);

      $this->assertEquals($session->toArray(), $sessionArray);
    }

    public function testGetType()
    {
      $session = new Vo_Session();
      $this->assertEquals($session->getType(), 'Session');
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }
