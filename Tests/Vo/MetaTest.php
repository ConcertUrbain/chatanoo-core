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

	class Vo_MetaTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table Comments
		 *
		 * @var Table_Metas
		 */
		private $_metasTable;

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
			$this->_metasTable = new Table_Metas();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testContructArray()
		{
			$metaArray = array(
				'id' => 1,
				'name' => 'keyword',
				'content' => 'meta'
			);

			$meta = new Vo_Meta($metaArray);
			$this->assertEquals($meta->id, $metaArray['id']);
			$this->assertEquals($meta->name, $metaArray['name']);
			$this->assertEquals($meta->content, $metaArray['content']);
		}

		public function testContructObject()
		{
			$metaArray = array(
				'id' => 1,
				'name' => 'keyword',
				'content' => 'meta'
			);

			$metaObject = new Vo_Meta($metaArray);

			$meta = new Vo_Meta($metaObject);
			$this->assertEquals($meta->id, $metaArray['id']);
			$this->assertEquals($meta->name, $metaArray['name']);
			$this->assertEquals($meta->content, $metaArray['content']);
		}

		public function testContructZendDbTableRow()
		{
			$metaRow = $this->_metasTable->find(1)->current();
			$meta = new Vo_Meta($metaRow);

			$this->assertEquals($meta->id, $metaRow->id);
			$this->assertEquals($meta->name, $metaRow->name);
			$this->assertEquals($meta->content, $metaRow->content);
		}

		public function testException()
		{
			$metaArray = array(
				'id' => 1,
				'name' => 'keyword',
				'content' => 'meta'
			);
			$meta = new Vo_Meta($metaArray);

			try {
				$meta->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $meta->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$metaString = new Vo_Meta('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}
		}

		public function testToArray()
		{
			$metaArray = array(
				'id' => 1,
				'name' => 'keyword',
				'content' => 'meta'
			);

			$meta = new Vo_Meta($metaArray);
			$this->assertEquals($meta->toArray(), $metaArray);
		}

		public function testGetType()
		{
			$meta = new Vo_Meta();
			$this->assertEquals($meta->getType(), 'Meta');
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
