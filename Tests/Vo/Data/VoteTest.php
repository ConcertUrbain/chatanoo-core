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

	class Vo_Data_VoteTest extends PHPUnit_Extensions_Database_TestCase
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
		 * @var Table_Datas_Vote
		 */
		private $_datasVoteTable;

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
			$this->_datasVoteTable = new Table_Datas_Vote();
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
			$voteArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'rate' => 1,
				'user' => 1
			);
			$vote = new Vo_Data_Vote($voteArray);

			$this->assertEquals($vote->id, $voteArray['id']);

			$this->assertTrue($vote->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($vote->addDate, new Zend_Date($voteArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($vote->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($vote->setDate, new Zend_Date($voteArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($vote->rate, $voteArray['rate']);
		}

		public function testConstructObject()
		{
			$voteArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'rate' => 1,
				'user' => 1
			);
			$voteObject = new Vo_Data_Vote($voteArray);

			$vote = new Vo_Data_Vote($voteObject);

			$this->assertEquals($vote->id, $voteArray['id']);

			$this->assertTrue($vote->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($vote->addDate, new Zend_Date($voteArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($vote->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($vote->setDate, new Zend_Date($voteArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($vote->rate, $voteArray['rate']);
		}

		public function testConstructZendDbTableRow()
		{
			$voteRow = $this->_datasVoteTable->find(1)->current();
			$vote = new Vo_Data_Vote($voteRow);

			$this->assertEquals($vote->id, $voteRow->id);

			$this->assertTrue($vote->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($vote->addDate, new Zend_Date($voteRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($vote->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($vote->setDate, new Zend_Date($voteRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($vote->rate, $voteRow->rate);
		}

		public function testException()
		{
			$voteArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'rate' => 1,
				'user' => 1
			);
			$vote = new Vo_Data_Vote($voteArray);

			try {
				$vote->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $vote->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$voteString = new Vo_Data_Vote('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$vote->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$voteArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'rate' => 1,
				'user' => 1
			);
			$vote = new Vo_Data_Vote($voteArray);

			$this->assertEquals($vote->toArray(), $voteArray);
		}

		public function testGetType()
		{
			$vote = new Vo_Data_Vote();
			$this->assertEquals($vote->getType(), 'Vote');
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
