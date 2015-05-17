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

  class Table_Datas_CartoTest extends PHPUnit_Extensions_Database_TestCase
  {
    /**
     * Table users
     *
     * @var Table_Datas_Carto
     */
    private $_datasCartoTable;

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
      $this->_datasCartoTable = new Table_Datas_Carto();
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
      $cartoRowArray = $this->_datasCartoTable->createRow()->toArray();
      $this->assertArrayHasKey('id', $cartoRowArray);
      $this->assertArrayHasKey('x', $cartoRowArray);
      $this->assertArrayHasKey('y', $cartoRowArray);
      $this->assertArrayHasKey('addDate', $cartoRowArray);
      $this->assertArrayHasKey('setDate', $cartoRowArray);
    }

    public function testFind()
    {
      $cartoRow = $this->_datasCartoTable->find(1)->current();
      $this->assertEquals($cartoRow->id, 1);
      $this->assertEquals($cartoRow->x, 1.89489e+06);
      $this->assertEquals($cartoRow->y, 7881);
      $this->assertEquals($cartoRow->addDate, '2009-04-15 23:55:36');
      $this->assertEquals($cartoRow->setDate, '2009-04-15 23:55:36');
    }

    public function testAddCarto()
    {
      $cartoArray = array(
        'x' => 94500,
        'y' => 94100,
        'addDate' => '2009-04-15 23:55:36',
        'setDate' => '2009-04-15 23:55:36',
        'sessions_id' => 1
      );
      $cartoRow = $this->_datasCartoTable->createRow($cartoArray);
      $cartoRow->save();

      $insertXml = dirname(__FILE__) . '/../Flats/datascarto-insert-data.xml';
      $this->assertDataSetsEqual(
        $this->createFlatXMLDataSet($insertXml),
        $this->getConnection()->createDataSet()
      );
    }

    public function testDeleteCarto()
    {
      $cartoRow = $this->_datasCartoTable->find(1)->current();
      $cartoRow->delete();
      $this->assertEquals($this->_datasCartoTable->fetchAll()->count(), 0);
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }