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

	class Table_MetasAssocTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_MetasAssoc
		 */
		private $_metasAssocTable;

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
			$this->_metasAssocTable = new Table_MetasAssoc();
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
			$linkRowArray = $this->_metasAssocTable->createRow()->toArray();
			$this->assertArrayHasKey('metas_id', $linkRowArray);
			$this->assertArrayHasKey('assoc_id', $linkRowArray);
			$this->assertArrayHasKey('assocType', $linkRowArray);
		}

		public function testFind()
		{
			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = ?', 1)
					->where('assoc_id = ?', 1)
					->where('assocType = ?', 'Session');
			$linkRow = $this->_metasAssocTable->fetchAll($select)->current();
			$this->assertEquals($linkRow->metas_id, 1);
			$this->assertEquals($linkRow->assoc_id, 1);
			$this->assertEquals($linkRow->assocType, 'Session');
		}

		public function testAddLink()
		{
			$linkArray = array(
				'metas_id' => 1,
				'assoc_id' => 2,
				'assocType' => 'Session'
			);
			$linkRow = $this->_metasAssocTable->createRow($linkArray);
			$linkRow->save();

			$insertXml = dirname(__FILE__) . '/Flats/metasassoc-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteLink()
		{
			$this->_metasAssocTable->delete("metas_id = 1 AND assoc_id = 1 AND assocType = 'Session'");
			$this->assertEquals($this->_metasAssocTable->fetchAll()->count(), 6);
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}