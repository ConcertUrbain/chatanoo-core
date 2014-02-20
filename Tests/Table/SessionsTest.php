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

	class Table_SessionsTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Sessions
		 */
		private $_sessionsTable;

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
			$this->_sessionsTable = new Table_Sessions();
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
			$sessionRowArray = $this->_sessionsTable->createRow()->toArray();
			$this->assertArrayHasKey('id', $sessionRowArray);
			$this->assertArrayHasKey('title', $sessionRowArray);
			$this->assertArrayHasKey('description', $sessionRowArray);
			$this->assertArrayHasKey('addDate', $sessionRowArray);
			$this->assertArrayHasKey('setDate', $sessionRowArray);
			$this->assertArrayHasKey('publishDate', $sessionRowArray);
			$this->assertArrayHasKey('endDate', $sessionRowArray);
			$this->assertArrayHasKey('users_id', $sessionRowArray);
		}

		public function testFind()
		{
			$sessionRow = $this->_sessionsTable->find(1)->current();
			$this->assertEquals($sessionRow->id, 1);
			$this->assertEquals($sessionRow->title, 'Ma session');
			$this->assertEquals($sessionRow->description, 'Ma description');
			$this->assertEquals($sessionRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->publishDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->endDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->users_id, 1);
		}

		public function testAddSession()
		{
			$sessionArray = array(
				'title' => 'Ma session 2',
				'description' => 'Ma description 2',
				'addDate' => '2009-04-15 23:55:36',
				'setDate' => '2009-04-15 23:55:36',
				'publishDate' => '2009-04-15 23:55:36',
				'endDate' => '2009-04-15 23:55:36',
				'users_id' => 1
			);
			$sessionRow = $this->_sessionsTable->createRow($sessionArray);
			$sessionRow->save();

			$insertXml = dirname(__FILE__) . '/Flats/sessions-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteSession()
		{
			$sessionRow = $this->_sessionsTable->find(1)->current();
			$sessionRow->delete();
			$this->assertEquals($this->_sessionsTable->fetchAll()->count(), 0);
		}

		public function testFindParentQueries()
		{
			$sessionRow = $this->_sessionsTable->find(1)->current();
			$queryRow = $sessionRow->findManyToManyRowset('Table_Queries', 'Table_SessionsAssocQueries')->current();
			$this->assertEquals($queryRow->id, 1);
			$this->assertEquals($queryRow->content, 'Une question ?');
			$this->assertEquals($queryRow->description, 'bah une question');
			$this->assertEquals($queryRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->publishDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->endDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->users_id, 1);
		}

		public function testFindParentUser()
		{
			$sessionRow = $this->_sessionsTable->find(1)->current();
			$userRow = $sessionRow->findParentTable_Users();
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

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}