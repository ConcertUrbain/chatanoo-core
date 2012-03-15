<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Vo_Data_CartoTest extends PHPUnit_Extensions_Database_TestCase
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
		 * @var Table_Datas_Carto
		 */
		private $_datasCartoTable;

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
			$this->_datasCartoTable = new Table_Datas_Carto();
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
			$cartoArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'x' => 1561.489,
				'y' => 894.0895
			);
			$carto = new Vo_Data_Carto($cartoArray);

			$this->assertEquals($carto->id, $cartoArray['id']);

			$this->assertTrue($carto->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($carto->addDate, new Zend_Date($cartoArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($carto->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($carto->setDate, new Zend_Date($cartoArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($carto->x, $cartoArray['x']);
			$this->assertEquals($carto->y, $cartoArray['y']);
		}

		public function testConstructObject()
		{
			$cartoArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'x' => 1561.489,
				'y' => 894.0895
			);
			$cartoObject = new Vo_Data_Carto($cartoArray);

			$carto = new Vo_Data_Carto($cartoObject);

			$this->assertEquals($carto->id, $cartoArray['id']);

			$this->assertTrue($carto->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($carto->addDate, new Zend_Date($cartoArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($carto->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($carto->setDate, new Zend_Date($cartoArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($carto->x, $cartoArray['x']);
			$this->assertEquals($carto->y, $cartoArray['y']);
		}

		public function testConstructZendDbTableRow()
		{
			$cartoRow = $this->_datasCartoTable->find(1)->current();
			$carto = new Vo_Data_Carto($cartoRow);

			$this->assertEquals($carto->id, $cartoRow->id);

			$this->assertTrue($carto->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($carto->addDate, new Zend_Date($cartoRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($carto->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($carto->setDate, new Zend_Date($cartoRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($carto->x, $cartoRow->x);
			$this->assertEquals($carto->y, $cartoRow->y);
		}

		public function testException()
		{
			$cartoArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'x' => 1561.489,
				'y' => 894.0895
			);
			$carto = new Vo_Data_Carto($cartoArray);

			try {
				$carto->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $carto->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$cartoString = new Vo_Data_Carto('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$carto->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$cartoArray = array(
				'id' => 1,
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'x' => 1561.489,
				'y' => 894.0895
			);
			$carto = new Vo_Data_Carto($cartoArray);

			$this->assertEquals($carto->toArray(), $cartoArray);
		}

		public function testGetType()
		{
			$carto = new Vo_Data_Carto();
			$this->assertEquals($carto->getType(), 'Carto');
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
