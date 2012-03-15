<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Table_Datas_VoteTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Datas_Vote
		 */
		private $_datasVoteTable;

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
			$this->_datasVoteTable = new Table_Datas_Vote();
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
			$voteRowArray = $this->_datasVoteTable->createRow()->toArray();
			$this->assertArrayHasKey('id', $voteRowArray);
			$this->assertArrayHasKey('rate', $voteRowArray);
			$this->assertArrayHasKey('users_id', $voteRowArray);
			$this->assertArrayHasKey('addDate', $voteRowArray);
			$this->assertArrayHasKey('setDate', $voteRowArray);
		}

		public function testFind()
		{
			$voteRow = $this->_datasVoteTable->find(1)->current();
			$this->assertEquals($voteRow->id, 1);
			$this->assertEquals($voteRow->rate, 1);
			$this->assertEquals($voteRow->users_id, 1);
			$this->assertEquals($voteRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($voteRow->setDate, '2009-04-15 23:55:36');
		}

		public function testAddVote()
		{
			$voteArray = array(
				'rate' => -1,
				'users_id' => 1,
				'addDate' => '2009-04-15 23:55:36',
				'setDate' => '2009-04-15 23:55:36',
				'sessions_id' => 1
			);
			$voteRow = $this->_datasVoteTable->createRow($voteArray);
			$voteRow->save();

			$insertXml = dirname(__FILE__) . '/../Flats/datasvote-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteVote()
		{
			$voteRow = $this->_datasVoteTable->find(1)->current();
			$voteRow->delete();
			$this->assertEquals($this->_datasVoteTable->fetchAll()->count(), 0);
		}

		public function testFindParentUser()
		{
			$voteRow = $this->_datasVoteTable->find(1)->current();
			$userRow = $voteRow->findParentTable_Users();
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