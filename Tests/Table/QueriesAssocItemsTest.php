<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Table_QueriesAssocItemsTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_QueriesAssocItems
		 */
		private $_queriesAssocItemsTable;

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
			$this->_queriesAssocItemsTable = new Table_QueriesAssocItems();
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
			$linkRowArray = $this->_queriesAssocItemsTable->createRow()->toArray();
			$this->assertArrayHasKey('queries_id', $linkRowArray);
			$this->assertArrayHasKey('items_id', $linkRowArray);
		}

		public function testFind()
		{
			$linkRow = $this->_queriesAssocItemsTable->find(1, 1)->current();
			$this->assertEquals($linkRow->queries_id, 1);
			$this->assertEquals($linkRow->items_id, 1);
		}

		public function testAddLink()
		{
			$linkArray = array(
				'queries_id' => 1,
				'items_id' => 2
			);
			$linkRow = $this->_queriesAssocItemsTable->createRow($linkArray);
			$linkRow->save();

			$insertXml = dirname(__FILE__) . '/Flats/queriesassocitems-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteLink()
		{
			$linkRow = $this->_queriesAssocItemsTable->find(1, 1)->current();
			$linkRow->delete();
			$this->assertEquals($this->_queriesAssocItemsTable->fetchAll()->count(), 0);
		}

		public function testFindParents()
		{
			$linkRow = $this->_queriesAssocItemsTable->find(1, 1)->current();
			$queryRow = $linkRow->findParentTable_Queries();
			$this->assertEquals($queryRow->id, 1);
			$this->assertEquals($queryRow->content, 'Une question ?');
			$this->assertEquals($queryRow->description, 'bah une question');
			$this->assertEquals($queryRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->publishDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->endDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->users_id, 1);

			$itemRow = $linkRow->findParentTable_Items();
			$this->assertEquals($itemRow->id, 1);
			$this->assertEquals($itemRow->title, 'Mon Item');
			$this->assertEquals($itemRow->description, 'Ma description');
			$this->assertEquals($itemRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($itemRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($itemRow->isValid, 1);
			$this->assertEquals($itemRow->users_id, 1);
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}