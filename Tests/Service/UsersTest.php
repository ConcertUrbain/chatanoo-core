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

	class Service_UsersTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table users
		 *
		 * @var Table_Users
		 */
		private $_usersTable;

		/**
		 * Service Users
		 *
		 * @var Service_Users
		 */
		private $_usersService;

		/**
		 * Service Datas
		 *
		 * @var Service_Datas
		 */
		private $_datasService;

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
			$this->_usersTable = new Table_Users();
			$this->_usersService = new Service_Users();
			$this->_datasService = new Service_Datas();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testGetUsers()
		{
			$users = $this->_usersService->getUsers();

			$this->assertTrue(is_array($users));
			$this->assertEquals(count($users), 1);

			$user = $users[0];
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

		public function testGetUserById()
		{
			$user = $this->_usersService->getUserById(1);
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

		public function testAddUser()
		{
			$date = Zend_Date::now();
			$userArray = array(
				'firstName' => 'Tsolova',
				'lastName' => 'Irina',
				'pseudo' => 'Irina',
				'password' => 'irinator',
				'email' => 'tsolova_irina@yahoo.com',
				'role' => 'user'
			);
			$u = new Vo_User($userArray);
			$this->_usersService->addUser($u);
			$user = $this->_usersService->getUserById(2);

			$this->assertEquals($user->id, 2);
			$this->assertEquals($user->firstName, $userArray['firstName']);
			$this->assertEquals($user->lastName, $userArray['lastName']);
			$this->assertEquals($user->pseudo, $userArray['pseudo']);
			//$this->assertEquals($user->password, $userArray['password']);
			$this->assertEquals($user->email, $userArray['email']);
			$this->assertEquals($user->role, $userArray['role']);
			$this->assertEquals($user->addDate, $date);
			$this->assertEquals($user->setDate, $date);
		}

		public function testSetUser()
		{
			$u = $this->_usersService->getUserById(1);
			$u->lastName = 'modif';
			$this->_usersService->setUser($u);
			$user = $this->_usersService->getUserById(1);
			$this->assertEquals($user->id, 1);
			$this->assertEquals($user->lastName, 'modif');
		}

		public function testDeleteUser()
		{
			$this->assertEquals($this->_usersTable->fetchAll()->count(), 1);
			$this->_usersService->deleteUser(1);
			$this->assertEquals($this->_usersTable->fetchAll()->count(), 0);
			$users = $this->_usersService->getUsers();
			$this->assertEquals(count($users), 0);
			$user = $this->_usersService->getUserById(1);
			$this->assertEquals(count($user), 0);

			$datas = $this->_datasService->getDatasByUserId(1);
			$this->assertEquals(count($datas), 0);
		}

		public function testBanUser()
		{
			$user = $this->_usersService->getUserById(1);
			$this->assertFalse($user->isBan());
			$this->_usersService->banUser(1, true);
			$user = $this->_usersService->getUserById(1);
			$this->assertTrue($user->isBan());
			$this->_usersService->banUser(1, false);
			$user = $this->_usersService->getUserById(1);
			$this->assertFalse($user->isBan());
		}

		public function testAddDataIntoVo()
		{
			$date = Zend_Date::now();
			$voteArray = array(
				'rate' => 1,
				'users_id' => 1
			);
			$data = new Vo_Data_Vote($voteArray);
			$this->_usersService->addDataIntoVo($data, 1);

			$datas = $this->_datasService->getDatasByUserId(1);
			$this->assertArrayHasKey('Adress', $datas);
			$this->assertArrayHasKey('Carto', $datas);
			$this->assertArrayHasKey('Vote', $datas);

			$this->assertEquals(count($datas['Vote']), 1);
			$vote = $datas['Vote'][0];
			$this->assertEquals($vote->id, 2);
			$this->assertEquals($vote->rate, 1);
			$this->assertEquals($vote->addDate, $date);
			$this->assertEquals($vote->setDate, $date);
		}

		public function testRemoveDataFromVo()
		{
			$datas = $this->_datasService->getDatasByUserId(1);
			$this->assertArrayHasKey('Carto', $datas);
			$this->_usersService->removeDataFromVo(1, 'Carto', 1);
			$datas = $this->_datasService->getDatasByUserId(1);
			$this->assertArrayNotHasKey('Carto', $datas);
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}

