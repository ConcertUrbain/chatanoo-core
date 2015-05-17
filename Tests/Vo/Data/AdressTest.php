<?php

  set_include_path(implode(PATH_SEPARATOR, array(
      dirname(__FILE__) . '/../../../Library',
      dirname(__FILE__) . '/../../../Application',
      dirname(__FILE__) . '/../../core',
      dirname(__FILE__),
      get_include_path(),
  )));
  require 'vendor/autoload.php';

  $autoloader = Zend_Loader_Autoloader::getInstance();
  $autoloader->setFallbackAutoloader(true);

  class Vo_Data_AdressTest extends PHPUnit_Extensions_Database_TestCase
  {

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;

    /**
     * Table DataAdress
     *
     * @var Table_Datas_Adress
     */
    private $_datasAdressTable;

    public function __construct()
    {
      if(!Zend_Registry::isRegistered('db'))
      {
        $config = new Zend_Config_Xml(dirname(__FILE__) . '/../../../Application/etc/config.xml', 'test');
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
      $originalXml = dirname(__FILE__).'/../Flats/all-original-data.xml';
      return $this->createFlatXMLDataSet($originalXml);
        }

    public function setUp()
    {
      parent::setUp();
      $this->_datasAdressTable = new Table_Datas_Adress();
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
      $adressArray = array(
        'id' => 1,
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'adress' => '8, rue de la Marne',
        'zipCode' => 94500,
        'city' => 'Champigny',
        'country' => 'France'
      );
      $adress = new Vo_Data_Adress($adressArray);

      $this->assertEquals($adress->id, $adressArray['id']);

      $this->assertTrue($adress->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($adress->addDate, new Zend_Date($adressArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($adress->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($adress->setDate, new Zend_Date($adressArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($adress->adress, $adressArray['adress']);
      $this->assertEquals($adress->zipCode, $adressArray['zipCode']);
      $this->assertEquals($adress->city, $adressArray['city']);
      $this->assertEquals($adress->country, $adressArray['country']);
    }

    public function testConstructObject()
    {
      $adressArray = array(
        'id' => 1,
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'adress' => '8, rue de la Marne',
        'zipCode' => 94500,
        'city' => 'Champigny',
        'country' => 'France'
      );
      $adressObject = new Vo_Data_Adress($adressArray);

      $adress = new Vo_Data_Adress($adressObject);

      $this->assertEquals($adress->id, $adressArray['id']);

      $this->assertTrue($adress->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($adress->addDate, new Zend_Date($adressArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($adress->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($adress->setDate, new Zend_Date($adressArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($adress->adress, $adressArray['adress']);
      $this->assertEquals($adress->zipCode, $adressArray['zipCode']);
      $this->assertEquals($adress->city, $adressArray['city']);
      $this->assertEquals($adress->country, $adressArray['country']);
    }

    public function testConstructZendDbTableRow()
    {
      $adressRow = $this->_datasAdressTable->find(1)->current();
      $adress = new Vo_Data_Adress($adressRow);

      $this->assertEquals($adress->id, $adressRow->id);

      $this->assertTrue($adress->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($adress->addDate, new Zend_Date($adressRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($adress->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($adress->setDate, new Zend_Date($adressRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($adress->adress, $adressRow->adress);
      $this->assertEquals($adress->zipCode, $adressRow->zipCode);
      $this->assertEquals($adress->city, $adressRow->city);
      $this->assertEquals($adress->country, $adressRow->country);
    }

    public function testException()
    {
      $adressArray = array(
        'id' => 1,
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'adress' => '8, rue de la Marne',
        'zipCode' => 94500,
        'city' => 'Champigny',
        'country' => 'France'
      );
      $adress = new Vo_Data_Adress($adressArray);

      try {
        $adress->bou = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 1);
      }

      try {
        $bou = $adress->bou;
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 2);
      }

      try {
        $adressString = new Vo_Data_Adress('bou');
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 3);
      }

      try {
        $adress->addDate = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 4);
      }
    }

    public function testToArray()
    {
      $adressArray = array(
        'id' => 1,
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => '2007.03.10 00:00:00',
        'adress' => '8, rue de la Marne',
        'zipCode' => 94500,
        'city' => 'Champigny',
        'country' => 'France'
      );
      $adress = new Vo_Data_Adress($adressArray);

      $this->assertEquals($adress->toArray(), $adressArray);
    }

    public function testGetType()
    {
      $adress = new Vo_Data_Adress();
      $this->assertEquals($adress->getType(), 'Adress');
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }
