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

	class Service_QueriesTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table items
		 *
		 * @var Table_Queries
		 */
		private $_queriesTable;

		/**
		 * Service Items
		 *
		 * @var Service_Queries
		 */
		private $_queriesService;

		/**
		 * Service Datas
		 *
		 * @var Service_Datas
		 */
		private $_datasService;

		/**
		 * Service Medias
		 *
		 * @var Service_Medias
		 */
		private $_mediasService;

		/**
		 * Service Items
		 *
		 * @var Service_Items
		 */
		private $_itemsService;

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

		/**
		 * Table medias_assoc
		 *
		 * @var Table_Medias_Assoc
		 */
		private $_mediasAssocTable;

		/**
		 * Table queires_assoc_items
		 *
		 * @var Table_QueriesAssocItems
		 */
		private $_queriesAssocItemsTable;


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
			$this->_queriesTable = new Table_Queries();
			$this->_queriesService = new Service_Queries();
			$this->_datasService = new Service_Datas();
			$this->_mediasService = new Service_Medias();
			$this->_itemsService = new Service_Items();
			$this->_usersService = new Service_Users();
			$this->_metasAssocTable = new Table_MetasAssoc();
			$this->_mediasAssocTable = new Table_Medias_Assoc();
			$this->_queriesAssocItemsTable = new Table_QueriesAssocItems();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testGetQueries()
		{
			$queries = $this->_queriesService->getQueries();

			$this->assertTrue(is_array($queries));
			$this->assertEquals(count($queries), 1);

			$query = $queries[0];
			$this->assertTrue($query instanceof Vo_Query);

			$this->assertEquals($query->id, 1);
			$this->assertEquals($query->content, 'Une question ?');
			$this->assertEquals($query->description, 'bah une question');
			$this->assertEquals($query->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($query->isValid());
		}

		public function testGetQueryById()
		{
			$query = $this->_queriesService->getQueryById(1);
			$this->assertTrue($query instanceof Vo_Query);

			$this->assertEquals($query->id, 1);
			$this->assertEquals($query->content, 'Une question ?');
			$this->assertEquals($query->description, 'bah une question');
			$this->assertEquals($query->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($query->isValid());
		}

		public function testGetQueriesBySessionId()
		{
			$queries = $this->_queriesService->getQueriesBySessionId(1);

			$this->assertTrue(is_array($queries));
			$this->assertEquals(count($queries), 1);

			$query = $queries[0];
			$this->assertTrue($query instanceof Vo_Query);

			$this->assertEquals($query->id, 1);
			$this->assertEquals($query->content, 'Une question ?');
			$this->assertEquals($query->description, 'bah une question');
			$this->assertEquals($query->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($query->isValid());
		}

		public function testGetQueriesByItemId()
		{
			$queries = $this->_queriesService->getQueriesByItemId(1);

			$this->assertTrue(is_array($queries));
			$this->assertEquals(count($queries), 1);

			$query = $queries[0];
			$this->assertTrue($query instanceof Vo_Query);

			$this->assertEquals($query->id, 1);
			$this->assertEquals($query->content, 'Une question ?');
			$this->assertEquals($query->description, 'bah une question');
			$this->assertEquals($query->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($query->isValid());
		}

		public function testAddQuery()
		{
			$date = Zend_Date::now();
			$queryArray = array(
				'content' => 'Ma question 2',
				'description' => 'Ma description 2',
				'publishDate' => $date,
				'isValid' => true
			);
			$this->_queriesService->addQuery(new Vo_Query($queryArray));
			$query = $this->_queriesService->getQueryById(2);
			$this->assertEquals($query->id, 2);
			$this->assertEquals($query->content, $queryArray['content']);
			$this->assertEquals($query->description, $queryArray['description']);
			$this->assertEquals($query->addDate, $date);
			$this->assertEquals($query->setDate, $date);
			$this->assertEquals($query->publishDate, $date);
			$this->assertNull($query->endDate);
			$this->assertTrue($query->isValid());
		}

		public function testSetQuery()
		{
			$q = $this->_queriesService->getQueryById(1);
			$q->content = 'modif';
			$this->_queriesService->setQuery($q);
			$query = $this->_queriesService->getQueryById(1);
			$this->assertEquals($query->id, 1);
			$this->assertEquals($query->content, 'modif');
		}

		public function testDeleteQuery()
		{
			$this->assertEquals($this->_queriesTable->fetchAll()->count(), 1);
			$this->_queriesService->deleteQuery(1);
			$this->assertEquals($this->_queriesTable->fetchAll()->count(), 0);
			$queries = $this->_queriesService->getQueries();
			$this->assertEquals(count($queries), 0);
			$query = $this->_queriesService->getQueryById(1);
			$this->assertEquals(count($query), 0);

			$datas = $this->_datasService->getDatasByQueryId(1);
			$this->assertEquals(count($datas), 0);

			$items = $this->_itemsService->getItemsByQueryId(1);
			$this->assertEquals(count($items), 0);

			$medias = $this->_mediasService->getMediasByQueryId(1);
			$this->assertEquals(count($medias), 0);
		}

		public function testAddItemIntoQuery()
		{
			$items = $this->_itemsService->getItemsByQueryId(1);
			$this->assertEquals(count($items), 2);

			$date = Zend_Date::now();
			$itemArray = array(
				'title' => 'Mon Item 2',
				'description' => 'Ma description 2',
				'isValid' => 1,
				'users_id' => 1
			);

			$this->_queriesService->addItemIntoQuery(new Vo_Item($itemArray), 1);

			$items = $this->_itemsService->getItemsByQueryId(1);
			$this->assertEquals(count($items), 3);

			$item = $items[2];
			$this->assertEquals($item->id, 3);
			$this->assertEquals($item->title, $itemArray['title']);
			$this->assertEquals($item->description, $itemArray['description']);
			$this->assertEquals($item->addDate, $date);
			$this->assertEquals($item->setDate, $date);
			$this->assertTrue($item->isValid());
		}

		public function testRemoveItemFromQuery()
		{
			$select = $this->_queriesAssocItemsTable->select();
			$select->where('queries_id = ?', 1)
					->where('items_id = ?', 1);
			$link = $this->_queriesAssocItemsTable->fetchAll($select);
			$this->assertNotNull($link);

			$this->_queriesService->removeItemFromQuery(1, 1);

			$link = $this->_queriesAssocItemsTable->fetchAll($select);
			$this->assertNotNull($link);
		}

		public function testAddMediaIntoQuery()
		{
			$medias = $this->_mediasService->getMediasByQueryId(1);
			$this->assertEquals(count($medias), 1);

			$date = Zend_Date::now();
			$pictureArray = array(
				'title' => 'Title 2',
				'description' => 'Description 2',
				'url' => 'http://www.unsite.fr/',
				'width' => 800,
				'height' => 600,
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'user' => 1,
				'isValid' => 1
			);
			$mediaPicture = new Vo_Media_Picture($pictureArray);
			$this->_queriesService->addMediaIntoQuery($mediaPicture, 1);

			$medias = $this->_mediasService->getMediasByQueryId(1);
			$this->assertEquals(count($medias), 2);

			$picture = $medias['Picture'][0];
			$this->assertTrue($picture instanceof Vo_Media_Picture);
			$this->assertEquals($picture->id, 2);
			$this->assertEquals($picture->title, $pictureArray['title']);
			$this->assertEquals($picture->description, $pictureArray['description']);
			$this->assertEquals($picture->url, $pictureArray['url']);
			$this->assertEquals($picture->width, $pictureArray['width']);
			$this->assertEquals($picture->height, $pictureArray['height']);
			$this->assertEquals($picture->preview, $pictureArray['preview']);
			$this->assertEquals($picture->addDate, $date);
			$this->assertEquals($picture->setDate, $date);
			$this->assertTrue($picture->isValid());
		}

		public function testRemoveMediaFromQuery()
		{
			$select = $this->_mediasAssocTable->select();
			$select->where('medias_id = ?', 1)
					->where('mediaType = ?', 'Video')
					->where('assoc_id = ?', 1)
					->where('assocType = ?', 'Query');
			$link = $this->_mediasAssocTable->fetchAll($select);
			$this->assertNotNull($link);

			$this->_itemsService->removeMediaFromItem(1, 'Video', 1);

			$link = $this->_mediasAssocTable->fetchAll($select);
			$this->assertNotNull($link);
		}

		public function testValidateVo()
		{
			$query = $this->_queriesService->getQueryById(1);
			$this->assertTrue($query->isValid());
			$this->_queriesService->validateVo(1, false);
			$query = $this->_queriesService->getQueryById(1);
			$this->assertFalse($query->isValid());
			$this->_queriesService->validateVo(1, true);
			$query = $this->_queriesService->getQueryById(1);
			$this->assertTrue($query->isValid());
		}

		public function testAddMetaIntoVo()
		{
			$meta = new Vo_Meta();
			$meta->content = 'meta';
			$this->_queriesService->addMetaIntoVo($meta, 1);

			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = 2')
					->where('assoc_id = 1')
					->where('assocType = ?', 'Query');
			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNotNull($link);
		}

		public function testRemoveMetaFromVo()
		{
			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = 1')
					->where('assoc_id = 1')
					->where('assocType = ?', 'Query');
			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNotNull($link);

			$this->_queriesService->removeMetaFromVo(1, 1);

			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNull($link);
		}

		public function testAddDataIntoVo()
		{
			$date = Zend_Date::now();
			$cartoArray = array(
				'x' => 94500,
				'y' => 94100
			);
			$data = new Vo_Data_Carto($cartoArray);
			$this->_queriesService->addDataIntoVo($data, 1);

			$datas = $this->_datasService->getDatasByQueryId(1);
			$this->assertArrayHasKey('Adress', $datas);
			$this->assertArrayHasKey('Carto', $datas);

			$this->assertEquals(count($datas['Carto']), 2);
			$carto = $datas['Carto'][1];
			$this->assertEquals($carto->id, 2);
			$this->assertEquals($carto->x, 94500);
			$this->assertEquals($carto->y, 94100);
			$this->assertEquals($carto->addDate, $date);
			$this->assertEquals($carto->setDate, $date);
		}

		public function testRemoveDataFromVo()
		{
			$this->_queriesService->removeDataFromVo(1, 'Carto', 1);
			$datas = $this->_datasService->getDatasByQueryId(1);
			$this->assertArrayNotHasKey('Carto', $datas);
		}

		public function testGetUserFromVo()
		{
			$user = $this->_queriesService->getUserFromVo(1);

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
			$this->_queriesService->setUserOfVo($u->id, 1);

			$user = $this->_queriesService->getUserFromVo(1);

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
			$queries = $this->_queriesService->getVosByUserId(1);

			$this->assertTrue(is_array($queries));
			$this->assertEquals(count($queries), 1);

			$query = $queries[0];
			$this->assertTrue($query instanceof Vo_Query);
			$this->assertEquals($query->id, 1);
			$this->assertEquals($query->content, 'Une question ?');
			$this->assertEquals($query->description, 'bah une question');
			$this->assertEquals($query->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->publishDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($query->endDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($query->isValid());
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}

