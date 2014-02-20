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

	class Service_SearchTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Service Search
		 *
		 * @var Service_Search
		 */
		private $_searchService;

		/**
		 * Table metas
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
			$this->_searchService = new Service_Search();
			$this->_metasTable = new Table_Metas();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testGetMetas()
		{
			$metas = $this->_searchService->getMetas();

			$this->assertTrue(is_array($metas));
			$this->assertEquals(count($metas), 1);

			$meta = $metas[0];
			$this->assertTrue($meta instanceof Vo_Meta);

			$this->assertEquals($meta->id, 1);
			$this->assertEquals($meta->content, 'Un meta');
		}

		public function testGetMetaById()
		{
			$meta = $this->_searchService->getMetaById(1);
			$this->assertTrue($meta instanceof Vo_Meta);

			$this->assertEquals($meta->id, 1);
			$this->assertEquals($meta->content, 'Un meta');
		}

		public function testGetMetasByVo()
		{
			$metas = $this->_searchService->getMetasByVo(1, 'Query');
			$this->assertTrue($metas[0] instanceof Vo_Meta);

			$this->assertEquals($metas[0]->id, 1);
			$this->assertEquals($metas[0]->content, 'Un meta');
		}

		public function testGetMetaByContent()
		{
			$meta = $this->_searchService->getMetaByContent('Un meta');
			$this->assertTrue($meta instanceof Vo_Meta);

			$this->assertEquals($meta->id, 1);
			$this->assertEquals($meta->content, 'Un meta');
		}

		public function testAddMeta()
		{
			$metaArray = array(
				'content' => 'meta2'
			);
			$m = new Vo_Meta($metaArray);
			$this->_searchService->addMeta($m);

			$meta = $this->_searchService->getMetaById(2);

			$this->assertEquals($meta->id, 2);
			$this->assertEquals($meta->content, $metaArray['content']);
		}

		public function testSetMeta()
		{
			$meta = $this->_searchService->getMetaById(1);
			$meta->content = 'modif';
			$this->_searchService->setMeta($meta);
			$metaModif = $this->_searchService->getMetaById(1);
			$this->assertEquals($metaModif->content, 'modif');
		}

		public function testDeleteMeta()
		{
			$this->assertEquals($this->_metasTable->fetchAll()->count(), 1);
			$this->_searchService->deleteMeta(1);
			$this->assertEquals($this->_metasTable->fetchAll()->count(), 0);
			$metas = $this->_searchService->getMetas();
			$this->assertEquals(count($metas), 0);
			$meta = $this->_searchService->getMetaById(1);
			$this->assertNull($meta);
		}

		public function testSearch()
		{
			$this->_searchService->search('meta regarde video');
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}

