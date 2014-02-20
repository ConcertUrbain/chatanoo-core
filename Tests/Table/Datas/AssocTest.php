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

	class Table_Datas_AssocTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Datas_Assoc
		 */
		private $_datasAssocTable;

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
			$this->_datasAssocTable = new Table_Datas_Assoc();
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
			$linkRowArray = $this->_datasAssocTable->createRow()->toArray();
			$this->assertArrayHasKey('datas_id', $linkRowArray);
			$this->assertArrayHasKey('dataType', $linkRowArray);
			$this->assertArrayHasKey('assoc_id', $linkRowArray);
			$this->assertArrayHasKey('assocType', $linkRowArray);
		}

		public function testFind()
		{
			$select = $this->_datasAssocTable->select();
			$select->where('datas_id = ?', 1)
					->where('dataType = ?', 'Adress')
					->where('assoc_id = ?', 1)
					->where('assocType = ?', 'User');
			$linkRow = $this->_datasAssocTable->fetchAll($select)->current();
			$this->assertEquals($linkRow->datas_id, 1);
			$this->assertEquals($linkRow->dataType, 'Adress');
			$this->assertEquals($linkRow->assoc_id, 1);
			$this->assertEquals($linkRow->assocType, 'User');
		}

		public function testAddLink()
		{
			$linkArray = array(
				'datas_id' => 1,
				'dataType' => 'Adress',
				'assoc_id' => 2,
				'assocType' => 'User'
			);
			$linkRow = $this->_datasAssocTable->createRow($linkArray);
			$linkRow->save();

			$insertXml = dirname(__FILE__) . '/../Flats/datasassoc-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteLink()
		{
			$this->_datasAssocTable->delete("datas_id = 1 AND dataType = 'Adress' AND assoc_id = 1 AND assocType = 'User'");
			$this->assertEquals($this->_datasAssocTable->fetchAll()->count(), 16);
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}