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

	class Vo_QueryTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table Queries
		 *
		 * @var Table_Queries
		 */
		private $_queriesTable;

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

		public function testConstructArray()
		{
			$queryArray = array(
				'id' => 1,
				'_user' => 1,
				'content' => 'content ?',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'publishDate' => '2007.03.10 00:00:00',
				'endDate' => Zend_Date::now(),
				'_sessions' => array(1)
			);
			$query = new Vo_Query($queryArray);

			$this->assertEquals($query->id, $queryArray['id']);
			$this->assertEquals($query->content, $queryArray['content']);
			$this->assertEquals($query->description, $queryArray['description']);

			$this->assertTrue($query->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($query->addDate, new Zend_Date($queryArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($query->setDate, new Zend_Date($queryArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->publishDate instanceof Zend_Date, "publishDate isn't instance of Zend_Date.");
			$this->assertEquals($query->publishDate, new Zend_Date($queryArray['publishDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->endDate instanceof Zend_Date, "endDate isn't instance of Zend_Date.");
			$this->assertEquals($query->endDate, new Zend_Date($queryArray['endDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructObject()
		{
			$queryArray = array(
				'id' => 1,
				'_user' => 1,
				'content' => 'content ?',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'publishDate' => '2007.03.10 00:00:00',
				'endDate' => Zend_Date::now(),
				'_sessions' => array(1)
			);
			$queryObject = new Vo_Query($queryArray);

			$query = new Vo_Query($queryObject);

			$this->assertEquals($query->id, $queryArray['id']);
			$this->assertEquals($query->content, $queryArray['content']);
			$this->assertEquals($query->description, $queryArray['description']);

			$this->assertTrue($query->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($query->addDate, new Zend_Date($queryArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($query->setDate, new Zend_Date($queryArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->publishDate instanceof Zend_Date, "publishDate isn't instance of Zend_Date.");
			$this->assertEquals($query->publishDate, new Zend_Date($queryArray['publishDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->endDate instanceof Zend_Date, "endDate isn't instance of Zend_Date.");
			$this->assertEquals($query->endDate, new Zend_Date($queryArray['endDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructZendDbTableRow()
		{
			$queryRow = $this->_queriesTable->find(1)->current();
			$query = new Vo_Query($queryRow);

			$this->assertEquals($query->id, $queryRow->id);
			$this->assertEquals($query->content, $queryRow->content);
			$this->assertEquals($query->description, $queryRow->description);

			$this->assertTrue($query->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($query->addDate, new Zend_Date($queryRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($query->setDate, new Zend_Date($queryRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->publishDate instanceof Zend_Date, "publishDate isn't instance of Zend_Date.");
			$this->assertEquals($query->publishDate, new Zend_Date($queryRow->publishDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($query->endDate instanceof Zend_Date, "endDate isn't instance of Zend_Date.");
			$this->assertEquals($query->endDate, new Zend_Date($queryRow->endDate, 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testException()
		{
			$queryArray = array(
				'id' => 1,
				'_user' => 1,
				'content' => 'content ?',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'publishDate' => '2007.03.10 00:00:00',
				'endDate' => Zend_Date::now()
			);
			$query = new Vo_Query($queryArray);

			try {
				$query->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $query->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$queryString = new Vo_Query('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$query->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$queryArray = array(
				'id' => 1,
				'_user' => 1,
				'content' => 'content ?',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'publishDate' => '2007.03.10 00:00:00',
				'endDate' => '2007.03.10 00:00:00',
				'_isValid' => 1
			);
			$query = new Vo_Query($queryArray);

			$this->assertEquals($query->toArray(), $queryArray);
		}

		public function testGetType()
		{
			$query = new Vo_Query();
			$this->assertEquals($query->getType(), 'Query');
		}

		public function testGetParentsSessions()
		{
			$this->markTestSkipped(
              'GetParentsSessions not tested.'
            );
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
