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

	class Service_SessionsTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table sessions
		 *
		 * @var Table_Sessions
		 */
		private $_sessionsTable;

		/**
		 * Service Queries
		 *
		 * @var Service_Queries
		 */
		private $_queriesService;

		/**
		 * Service Sessions
		 *
		 * @var Service_Sessions
		 */
		private $_sessionsService;

		/**
		 * Service Users
		 *
		 * @var Service_Users
		 */
		private $_usersService;

		/**
		 * Table metas_assoc
		 *
		 * @var Table_MetasAssoc
		 */
		private $_metasAssocTable;


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
			
			Zend_Registry::set('userID', 1);
			Zend_Registry::set('sessionID', 1);
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
			$this->_sessionsService = new Service_Sessions();
			$this->_queriesService = new Service_Queries();
			$this->_sessionsTable = new Table_Sessions();
			$this->_usersService = new Service_Users();
			$this->_metasAssocTable = new Table_MetasAssoc();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testGetSessions()
		{
			$sessions = $this->_sessionsService->getSessions();

			$this->assertTrue(is_array($sessions));
			$this->assertEquals(count($sessions), 1);

			$session = $sessions[0];
			$this->assertTrue($session instanceof Vo_Session);

			$this->assertEquals($session->id, 1);
			$this->assertEquals($session->title, 'Ma session');
			$this->assertEquals($session->description, 'Ma description');
			$this->assertEquals($session->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testGetSessionById()
		{
			$session =$this->_sessionsService->getSessionById(1);
			$this->assertTrue($session instanceof Vo_Session);

			$this->assertEquals($session->id, 1);
			$this->assertEquals($session->title, 'Ma session');
			$this->assertEquals($session->description, 'Ma description');
			$this->assertEquals($session->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testGetSessionsByQueryId()
		{
			$sessions = $this->_sessionsService->getSessionsByQueryId(1);

			$this->assertTrue(is_array($sessions));
			$this->assertEquals(count($sessions), 1);

			$session = $sessions[0];
			$this->assertTrue($session instanceof Vo_Session);

			$this->assertEquals($session->id, 1);
			$this->assertEquals($session->title, 'Ma session');
			$this->assertEquals($session->description, 'Ma description');
			$this->assertEquals($session->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testAddSession()
		{
			$date = Zend_Date::now();
			$sessionArray = array(
				'title' => 'Ma session 2',
				'description' => 'Ma description 2',
				'publishDate' => $date,
				'users_id' => 1
			);
			$s = new Vo_Session($sessionArray);
			$this->_sessionsService->addSession($s);

			$session =$this->_sessionsService->getSessionById(2);
			$this->assertNull($session);
			
			/*$this->assertTrue($session instanceof Vo_Session);
			
			$this->assertEquals($session->id, 2);
			$this->assertEquals($session->title, $sessionArray['title']);
			$this->assertEquals($session->description, $sessionArray['description']);
			$this->assertEquals($session->addDate, $date);
			$this->assertEquals($session->setDate, $date);
			$this->assertEquals($session->publishDate, $date);
			$this->assertNull($session->endDate);*/
		}

		public function testSetSession()
		{
			$s = $this->_sessionsService->getSessionById(1);
			$s->title = 'modif';
			$this->_sessionsService->setSession($s);
			$session = $this->_sessionsService->getSessionById(1);
			$this->assertEquals($session->id, 1);
			$this->assertEquals($session->title, 'modif');
		}

		public function testDeleteSession()
		{
			$this->assertEquals($this->_sessionsTable->fetchAll()->count(), 1);
			$this->_sessionsService->deleteSession(1);
			$this->assertEquals($this->_sessionsTable->fetchAll()->count(), 0);
			$sessions = $this->_sessionsService->getSessions();
			$this->assertEquals(count($sessions), 0);
			$session = $this->_sessionsService->getSessionById(1);
			$this->assertEquals(count($session), 0);

			$queries = $this->_queriesService->getQueriesBySessionId(1);
			$this->assertEquals(count($queries), 0);
		}

		public function testAddQueryIntoSession()
		{
			$date = Zend_Date::now();
			$queryArray = array(
				'content' => 'Ma question 2',
				'description' => 'Ma description 2',
				'publishDate' => $date,
				'isValid' => true
			);
			$q = new Vo_Query($queryArray);
			$this->_sessionsService->addQueryIntoSession($q, 1);

			$queries = $this->_queriesService->getQueriesBySessionId(1);
			$query = $queries[1];
			$this->assertEquals($query->id, 2);
			$this->assertEquals($query->content, $queryArray['content']);
			$this->assertEquals($query->description, $queryArray['description']);
			$this->assertEquals($query->addDate, $date);
			$this->assertEquals($query->setDate, $date);
			$this->assertEquals($query->publishDate, $date);
			$this->assertNull($query->endDate);
			$this->assertTrue($query->isValid());
		}

		public function testRemoveQueryFromSession()
		{
			$queries = $this->_queriesService->getQueriesBySessionId(1);
			$this->assertEquals(count($queries), 1);

			$this->_sessionsService->removeQueryFromSession(1, 1);

			$queries = $this->_queriesService->getQueriesBySessionId(1);
			$this->assertEquals(count($queries), 0);
		}

		public function testAddMetaIntoVo()
		{
			$meta = new Vo_Meta();
			$meta->content = 'meta';
			$this->_sessionsService->addMetaIntoVo($meta, 1);

			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = 2')
					->where('assoc_id = 1')
					->where('assocType = ?', 'Session');
			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNotNull($link);
		}

		public function testRemoveMetaFromVo()
		{
			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = 1')
					->where('assoc_id = 1')
					->where('assocType = ?', 'Session');
			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNotNull($link);

			$this->_sessionsService->removeMetaFromVo(1, 1);

			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNull($link);
		}

		public function testGetUserIntoVo()
		{
			$user = $this->_sessionsService->getUserFromVo(1);

			$this->assertTrue($user instanceof Vo_User);

			$this->assertEquals($user->id, 1);
			$this->assertEquals($user->firstName, 'Desve');
			$this->assertEquals($user->lastName, 'Mathieu');
			$this->assertEquals($user->pseudo, 'mazerte');
			//$this->assertEquals($user->password, 'desperados');
			$this->assertEquals($user->email, 'mathieu.desve@unflux.fr');
			$this->assertEquals($user->role, 'admin');
			$this->assertEquals($user->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($user->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testSetUserOfVo()
		{
			$userArray = array(
				'firstName' => 'Tsolova',
				'lastName' => 'Irina',
				'pseudo' => 'Irina',
				'password' => 'irinator',
				'email' => 'tsolova_irina@yahoo.com',
				'role' => 'user'
			);
			$u = new Vo_User($userArray);
			$u->id = $this->_usersService->addUser($u);
			$this->_sessionsService->setUserOfVo($u->id, 1);

			$user = $this->_sessionsService->getUserFromVo(1);

			$this->assertTrue($user instanceof Vo_User);

			$this->assertEquals($user->id, 2);
			$this->assertEquals($user->firstName, 'Tsolova');
			$this->assertEquals($user->lastName, 'Irina');
			$this->assertEquals($user->pseudo, 'Irina');
			//$this->assertEquals($user->password, 'irinator');
			$this->assertEquals($user->email, 'tsolova_irina@yahoo.com');
			$this->assertEquals($user->role, 'user');
		}

		public function testGetVosByUserId()
		{
			$sessions = $this->_sessionsService->getVosByUserId(1);

			$this->assertTrue(is_array($sessions));
			$this->assertEquals(count($sessions), 1);

			$session = $sessions[0];
			$this->assertTrue($session instanceof Vo_Session);

			$this->assertEquals($session->id, 1);
			$this->assertEquals($session->title, 'Ma session');
			$this->assertEquals($session->description, 'Ma description');
			$this->assertEquals($session->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($session->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}

