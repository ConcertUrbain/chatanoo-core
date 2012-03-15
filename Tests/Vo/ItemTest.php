<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Vo_ItemTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table Items
		 *
		 * @var Table_Items
		 */
		private $_itemsTable;

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
			$this->_itemsTable = new Table_Items();
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
			$itemArray = array(
				'id' => 1,
				'_user' => 1,
				'title' => 'Mon titre',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true
			);
			$item = new Vo_Item($itemArray);

			$this->assertEquals($item->id, $itemArray['id']);
			$this->assertEquals($item->title, $itemArray['title']);
			$this->assertEquals($item->description, $itemArray['description']);

			$this->assertTrue($item->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($item->addDate, new Zend_Date($itemArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($item->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($item->setDate, new Zend_Date($itemArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructObject()
		{
			$itemArray = array(
				'id' => 1,
				'_user' => 1,
				'title' => 'Mon titre',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true
			);
			$itemObject = new Vo_Item($itemArray);

			$item = new Vo_Item($itemObject);

			$this->assertEquals($item->id, $itemArray['id']);
			$this->assertEquals($item->title, $itemArray['title']);
			$this->assertEquals($item->description, $itemArray['description']);

			$this->assertTrue($item->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($item->addDate, new Zend_Date($itemArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($item->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($item->setDate, new Zend_Date($itemArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructZendDbTableRow()
		{
			$itemRow = $this->_itemsTable->find(1)->current();
			$item = new Vo_Item($itemRow);

			$this->assertEquals($item->id, $itemRow->id);
			$this->assertEquals($item->title, $itemRow->title);
			$this->assertEquals($item->description, $itemRow->description);

			$this->assertTrue($item->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($item->addDate, new Zend_Date($itemRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($item->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($item->setDate, new Zend_Date($itemRow->setDate, 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testException()
		{
			$itemArray = array(
				'id' => 1,
				'_user' => 1,
				'title' => 'Mon titre',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true
			);
			$item = new Vo_Item($itemArray);

			try {
				$item->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $item->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$itemString = new Vo_Item('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$item->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$itemArray = array(
				'id' => 1,
				'_user' => 1,
				'title' => 'Mon titre',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'_isValid' => true
			);
			$item = new Vo_Item($itemArray);

			$this->assertEquals($item->toArray(), $itemArray);
		}

		public function testGetType()
		{
			$item = new Vo_Item();
			$this->assertEquals($item->getType(), 'Item');
		}

		public function testValidate()
		{
			$itemArray = array(
				'id' => 1,
				'_user' => 1,
				'_queries' => array(1),
				'title' => 'Mon titre',
				'description' => 'Une description',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'_isValid' => true
			);
			$item = new Vo_Item($itemArray);

			$this->assertTrue($item->isValid());
			$item->validate(false);
			$this->assertFalse($item->isValid());
			$item->validate(true);
			$this->assertTrue($item->isValid());
		}

		public function testGetRate()
		{
			$this->markTestSkipped(
              'GetRate not tested.'
            );
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
