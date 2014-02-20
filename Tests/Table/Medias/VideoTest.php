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

	class Table_Medias_VideoTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Medias_Video
		 */
		private $_mediasVideoTable;

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
			$this->_mediasVideoTable = new Table_Medias_Video();
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
			$mediaRowArray = $this->_mediasVideoTable->createRow()->toArray();
			$this->assertArrayHasKey('id', $mediaRowArray);
			$this->assertArrayHasKey('title', $mediaRowArray);
			$this->assertArrayHasKey('description', $mediaRowArray);
			$this->assertArrayHasKey('url', $mediaRowArray);
			$this->assertArrayHasKey('width', $mediaRowArray);
			$this->assertArrayHasKey('height', $mediaRowArray);
			$this->assertArrayHasKey('totalTime', $mediaRowArray);
			$this->assertArrayHasKey('preview', $mediaRowArray);
			$this->assertArrayHasKey('addDate', $mediaRowArray);
			$this->assertArrayHasKey('setDate', $mediaRowArray);
			$this->assertArrayHasKey('isValid', $mediaRowArray);
			$this->assertArrayHasKey('users_id', $mediaRowArray);
		}

		public function testFind()
		{
			$mediaRow = $this->_mediasVideoTable->find(1)->current();
			$this->assertEquals($mediaRow->id, 1);
			$this->assertEquals($mediaRow->title, 'Une video');
			$this->assertEquals($mediaRow->description, 'il s ecoute et se regarde');
			$this->assertEquals($mediaRow->url, 'http://www.unsite.fr/video.flv');
			$this->assertEquals($mediaRow->width, 400);
			$this->assertEquals($mediaRow->height, 300);
			$this->assertEquals($mediaRow->totalTime, 180);
			$this->assertEquals($mediaRow->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($mediaRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($mediaRow->isValid, 1);
			$this->assertEquals($mediaRow->users_id, 1);
		}

		public function testAddVideo()
		{
			$mediaArray = array(
				'title' => 'Une video 2',
				'description' => 'il s ecoute et se regarde 2',
				'url' => 'http://www.unsite.fr/video2.flv',
				'width' => 400,
				'height' => 300,
				'totalTime' => 180,
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2009-04-15 23:55:36',
				'setDate' => '2009-04-15 23:55:36',
				'isValid' => 1,
				'users_id' => 1,
				'sessions_id' => 1
			);
			$mediaRow = $this->_mediasVideoTable->createRow($mediaArray);
			$mediaRow->save();

			$insertXml = dirname(__FILE__) . '/../Flats/mediasvideo-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteComment()
		{
			$mediaRow = $this->_mediasVideoTable->find(1)->current();
			$mediaRow->delete();
			$this->assertEquals($this->_mediasVideoTable->fetchAll()->count(), 0);
		}

		public function testFindParentUser()
		{
			$mediaRow = $this->_mediasVideoTable->find(1)->current();
			$userRow = $mediaRow->findParentTable_Users();
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

		public function testGetParentDatasAdress()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('items', 'datas_adress.*')
					->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
					->join('datas_adress', 'datas_assoc.datas_id = datas_adress.id')
					->where('items.id = 1')
					->where("datas_assoc.dataType = 'Adress'")
					->where("datas_assoc.assocType = 'Media_Video'");

			$datasArray = $db->fetchAll($select);

			$this->assertEquals($datasArray[0]['id'], 1);
			$this->assertEquals($datasArray[0]['adress'], "8, rue de la Marne");
			$this->assertEquals($datasArray[0]['zipCode'], 94500);
			$this->assertEquals($datasArray[0]['city'], "Champigny");
			$this->assertEquals($datasArray[0]['country'], 'France');
			$this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
		}

		public function testGetParentDatasCarto()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('items', 'datas_carto.*')
					->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
					->join('datas_carto', 'datas_assoc.datas_id = datas_carto.id')
					->where('items.id = 1')
					->where("datas_assoc.dataType = 'Carto'")
					->where("datas_assoc.assocType = 'Media_Video'");

			$datasArray = $db->fetchAll($select);

			$this->assertEquals($datasArray[0]['id'], 1);
			$this->assertEquals($datasArray[0]['x'], 1.89489e+06);
			$this->assertEquals($datasArray[0]['y'], 7881);
			$this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}