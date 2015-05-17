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

  class Table_Datas_AdressTest extends PHPUnit_Extensions_Database_TestCase
  {
    /**
     * Table users
     *
     * @var Table_Datas_Adress
     */
    private $_datasAdressTable;

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

    public function testCreateRow()
    {
      $adressRowArray = $this->_datasAdressTable->createRow()->toArray();
      $this->assertArrayHasKey('id', $adressRowArray);
      $this->assertArrayHasKey('adress', $adressRowArray);
      $this->assertArrayHasKey('zipCode', $adressRowArray);
      $this->assertArrayHasKey('city', $adressRowArray);
      $this->assertArrayHasKey('country', $adressRowArray);
      $this->assertArrayHasKey('addDate', $adressRowArray);
      $this->assertArrayHasKey('setDate', $adressRowArray);
    }

    public function testFind()
    {
      $adressRow = $this->_datasAdressTable->find(1)->current();
      $this->assertEquals($adressRow->id, 1);
      $this->assertEquals($adressRow->adress, '8, rue de la Marne');
      $this->assertEquals($adressRow->zipCode, 94500);
      $this->assertEquals($adressRow->city, 'Champigny');
      $this->assertEquals($adressRow->country, 'France');
      $this->assertEquals($adressRow->addDate, '2009-04-15 23:55:36');
      $this->assertEquals($adressRow->setDate, '2009-04-15 23:55:36');
    }

    public function testAddAdress()
    {
      $adressArray = array(
        'adress' => '75, avenue de Lattre de Tassigny',
        'zipCode' => 94100,
        'city' => 'Saint-Maur',
        'country' => 'France',
        'addDate' => '2009-04-15 23:55:36',
        'setDate' => '2009-04-15 23:55:36',
        'sessions_id' => 1
      );
      $adressRow = $this->_datasAdressTable->createRow($adressArray);
      $adressRow->save();

      $insertXml = dirname(__FILE__) . '/../Flats/datasadress-insert-data.xml';
      $this->assertDataSetsEqual(
        $this->createFlatXMLDataSet($insertXml),
        $this->getConnection()->createDataSet()
      );
    }

    public function testDeleteAdress()
    {
      $adressRow = $this->_datasAdressTable->find(1)->current();
      $adressRow->delete();
      $this->assertEquals($this->_datasAdressTable->fetchAll()->count(), 0);
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }