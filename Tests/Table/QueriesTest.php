<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Table_QueriesTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Queries
		 */
		private $_queriesTable;

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
			$this->_queriesTable = new Table_Queries();
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
			$queryRowArray = $this->_queriesTable->createRow()->toArray();
			$this->assertArrayHasKey('id', $queryRowArray);
			$this->assertArrayHasKey('content', $queryRowArray);
			$this->assertArrayHasKey('description', $queryRowArray);
			$this->assertArrayHasKey('addDate', $queryRowArray);
			$this->assertArrayHasKey('setDate', $queryRowArray);
			$this->assertArrayHasKey('publishDate', $queryRowArray);
			$this->assertArrayHasKey('endDate', $queryRowArray);
			$this->assertArrayHasKey('users_id', $queryRowArray);
		}

		public function testFind()
		{
			$queryRow = $this->_queriesTable->find(1)->current();
			$this->assertEquals($queryRow->id, 1);
			$this->assertEquals($queryRow->content, 'Une question ?');
			$this->assertEquals($queryRow->description, 'bah une question');
			$this->assertEquals($queryRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->publishDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->endDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->users_id, 1);
		}

		public function testAddQuery()
		{
			$queryArray = array(
				'content' => 'Une question ? 2',
				'description' => 'bah une question 2',
				'addDate' => '2009-04-15 23:55:36',
				'setDate' => '2009-04-15 23:55:36',
				'publishDate' => '2009-04-15 23:55:36',
				'endDate' => '2009-04-15 23:55:36',
				'users_id' => 1,
				'isValid' => 1
			);
			$queryRow = $this->_queriesTable->createRow($queryArray);
			$queryRow->save();

			$insertXml = dirname(__FILE__) . '/Flats/queries-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteQuery()
		{
			$queryRow = $this->_queriesTable->find(1)->current();
			$queryRow->delete();
			$this->assertEquals($this->_queriesTable->fetchAll()->count(), 0);
		}

		public function testFindParentSessions()
		{
			$queryRow = $this->_queriesTable->find(1)->current();
			$sessionRow = $queryRow->findManyToManyRowset('Table_Sessions', 'Table_SessionsAssocQueries')->current();
			$this->assertEquals($sessionRow->id, 1);
			$this->assertEquals($sessionRow->title, 'Ma session');
			$this->assertEquals($sessionRow->description, 'Ma description');
			$this->assertEquals($sessionRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->publishDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->endDate, '2009-04-15 23:55:36');
			$this->assertEquals($sessionRow->users_id, 1);
		}

		public function testFindParentItems()
		{
			$queryRow = $this->_queriesTable->find(1)->current();
			$itemRow = $queryRow->findManyToManyRowset('Table_Items', 'Table_QueriesAssocItems')->current();
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
			$queryRow = $this->_queriesTable->find(1)->current();
			$userRow = $queryRow->findParentTable_Users();
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

		public function testGetParentDatasAdress()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('items', 'datas_adress.*')
					->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
					->join('datas_adress', 'datas_assoc.datas_id = datas_adress.id')
					->where('items.id = 1')
					->where("datas_assoc.dataType = 'Adress'")
					->where("datas_assoc.assocType = 'Query'");

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
					->where("datas_assoc.assocType = 'Query'");

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