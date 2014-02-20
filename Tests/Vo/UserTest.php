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

	class Vo_UserTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table Users
		 *
		 * @var Table_Users
		 */
		private $_usersTable;

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
			$this->_usersTable = new Table_Users();
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
			$userArray = array(
				'id' => 1,
				'firstName' => 'DesvŽ',
				'lastName' => 'Mathieu',
				'pseudo' => 'mŠzrtŽ',
				'password' => 'desperados',
				'email' => 'mathieu.desve@unflux.fr',
				'role' => 'admin',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isBan' => true
			);
			$user = new Vo_User($userArray);

			$this->assertEquals($user->id, $userArray['id']);
			$this->assertEquals($user->firstName, $userArray['firstName']);
			$this->assertEquals($user->lastName, $userArray['lastName']);
			$this->assertEquals($user->pseudo, $userArray['pseudo']);
			//$this->assertEquals($user->password, $userArray['password']);
			$this->assertEquals($user->email, $userArray['email']);
			$this->assertEquals($user->role, $userArray['role']);

			$this->assertTrue($user->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($user->addDate, new Zend_Date($userArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($user->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($user->setDate, new Zend_Date($userArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructObject()
		{
			$userArray = array(
				'id' => 1,
				'firstName' => 'DesvŽ',
				'lastName' => 'Mathieu',
				'pseudo' => 'mŠzrtŽ',
				'password' => 'desperados',
				'email' => 'mathieu.desve@unflux.fr',
				'role' => 'admin',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isBan' => true
			);
			$userObject = new Vo_User($userArray);

			$user = new Vo_User($userObject);

			$this->assertEquals($user->id, $userArray['id']);
			$this->assertEquals($user->firstName, $userArray['firstName']);
			$this->assertEquals($user->lastName, $userArray['lastName']);
			$this->assertEquals($user->pseudo, $userArray['pseudo']);
			//$this->assertEquals($user->password, $userArray['password']);
			$this->assertEquals($user->email, $userArray['email']);
			$this->assertEquals($user->role, $userArray['role']);

			$this->assertTrue($user->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($user->addDate, new Zend_Date($userArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($user->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($user->setDate, new Zend_Date($userArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructZendDbTableRow()
		{
			$userRow = $this->_usersTable->find(1)->current();
			$user = new Vo_User($userRow);

			$this->assertEquals($user->id, $userRow->id);
			$this->assertEquals($user->firstName, $userRow->firstName);
			$this->assertEquals($user->lastName, $userRow->lastName);
			$this->assertEquals($user->pseudo, $userRow->pseudo);
			//$this->assertEquals($user->password, $userRow->password);
			$this->assertEquals($user->email, $userRow->email);
			$this->assertEquals($user->role, $userRow->role);

			$this->assertTrue($user->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($user->addDate, new Zend_Date($userRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($user->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($user->setDate, new Zend_Date($userRow->setDate, 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testException()
		{
			$userArray = array(
				'id' => 1,
				'firstName' => 'DesvŽ',
				'lastName' => 'Mathieu',
				'pseudo' => 'mŠzrtŽ',
				'password' => 'desperados',
				'email' => 'mathieu.desve@unflux.fr',
				'role' => 'admin',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isBan' => true
			);
			$user = new Vo_User($userArray);
		}

		public function testToArray()
		{
			$userArray = array(
				'id' => 1,
				'firstName' => 'DesvŽ',
				'lastName' => 'Mathieu',
				'pseudo' => 'mŠzrtŽ',
				'password' => 'desperados',
				'email' => 'mathieu.desve@unflux.fr',
				'role' => 'admin',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'_isBan' => true
			);
			$user = new Vo_User($userArray);
			
			$user = $user->toArray();
			unset($user['password']);
			unset($userArray['password']);
			$this->assertEquals($user, $userArray);
		}

		public function testGetType()
		{
			$user = new Vo_User();
			$this->assertEquals($user->getType(), 'User');
		}

		public function testBan()
		{
			$userArray = array(
				'id' => 1,
				'firstName' => 'DesvŽ',
				'lastName' => 'Mathieu',
				'pseudo' => 'mŠzrtŽ',
				'password' => 'desperados',
				'email' => 'mathieu.desve@unflux.fr',
				'role' => 'admin',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isBan' => true
			);
			$user = new Vo_User($userArray);

			$this->assertTrue($user->isBan());
			$user->ban(false);
			$this->assertFalse($user->isBan());
			$user->ban(true);
			$this->assertTrue($user->isBan());
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
