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

	class Table_ItemsTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Items
		 */
		private $_itemsTable;

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
			$this->_itemsTable = new Table_Items();
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
			$itemRowArray = $this->_itemsTable->createRow()->toArray();
			$this->assertArrayHasKey('id', $itemRowArray);
			$this->assertArrayHasKey('title', $itemRowArray);
			$this->assertArrayHasKey('description', $itemRowArray);
			$this->assertArrayHasKey('addDate', $itemRowArray);
			$this->assertArrayHasKey('setDate', $itemRowArray);
			$this->assertArrayHasKey('isValid', $itemRowArray);
			$this->assertArrayHasKey('users_id', $itemRowArray);
		}

		public function testFind()
		{
			$itemRow = $this->_itemsTable->find(1)->current();
			$this->assertEquals($itemRow->id, 1);
			$this->assertEquals($itemRow->title, 'Mon Item');
			$this->assertEquals($itemRow->description, 'Ma description');
			$this->assertEquals($itemRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($itemRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($itemRow->isValid, 1);
			$this->assertEquals($itemRow->users_id, 1);
		}

		public function testAddItem()
		{
			$itemArray = array(
				'title' => 'Mon Item 2',
				'description' => 'Ma description 2',
				'addDate' => '2009-04-15 23:55:36',
				'setDate' => '2009-04-15 23:55:36',
				'isValid' => 1,
				'sessions_id' => 1,
				'users_id' => 1
			);
			$itemRow = $this->_itemsTable->createRow($itemArray);
			$itemRow->save();

			$insertXml = dirname(__FILE__) . '/Flats/items-insert-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testDeleteComment()
		{
			$itemRow = $this->_itemsTable->find(1)->current();
			$itemRow->delete();
			$this->assertEquals($this->_itemsTable->fetchAll()->count(), 0);
		}

		public function testFindParentQueries()
		{
			$itemRow = $this->_itemsTable->find(1)->current();
			$queryRow = $itemRow->findManyToManyRowset('Table_Queries', 'Table_QueriesAssocItems')->current();
			$this->assertEquals($queryRow->id, 1);
			$this->assertEquals($queryRow->content, 'Une question ?');
			$this->assertEquals($queryRow->description, 'bah une question');
			$this->assertEquals($queryRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->publishDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->endDate, '2009-04-15 23:55:36');
			$this->assertEquals($queryRow->users_id, 1);
		}

		public function testFindComments()
		{
			$itemRow = $this->_itemsTable->find(1)->current();
			$commentRow = $itemRow->findDependentRowset('Table_Comments')->current();
			$this->assertEquals($commentRow->id, 1);
			$this->assertEquals($commentRow->content, 'Un commentaire');
			$this->assertEquals($commentRow->addDate, '2009-04-15 23:55:36');
			$this->assertEquals($commentRow->setDate, '2009-04-15 23:55:36');
			$this->assertEquals($commentRow->isValid, 1);
			$this->assertEquals($commentRow->users_id, 1);
			$this->assertEquals($commentRow->items_id, 1);
		}

		public function testFindParentUser()
		{
			$itemRow = $this->_itemsTable->find(1)->current();
			$userRow = $itemRow->findParentTable_Users();
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

		public function testGetParentMediasSound()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('metas', 'medias_sound.*')
					->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
					->join('medias_sound', 'metas_assoc.assoc_id = medias_sound.id')
					->where('metas.id = 1')
					->where("metas_assoc.assocType = 'Media_Sound'");

			$mediasArray = $db->fetchAll($select);

			$this->assertEquals($mediasArray[0]['id'], 1);
			$this->assertEquals($mediasArray[0]['title'], "Un son");
			$this->assertEquals($mediasArray[0]['description'], "il s ecoute");
			$this->assertEquals($mediasArray[0]['url'], "http://www.unsite.fr/sound.mp3");
			$this->assertEquals($mediasArray[0]['totalTime'], 180);
			$this->assertEquals($mediasArray[0]['preview'], "http://www.unsite.fr/preview.jpg");
			$this->assertEquals($mediasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['setDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['isValid'], 1);
			$this->assertEquals($mediasArray[0]['users_id'], 1);
		}

		public function testGetParentMediasPicture()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('metas', 'medias_picture.*')
					->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
					->join('medias_picture', 'metas_assoc.assoc_id = medias_picture.id')
					->where('metas.id = 1')
					->where("metas_assoc.assocType = 'Media_Picture'");

			$mediasArray = $db->fetchAll($select);

			$this->assertEquals($mediasArray[0]['id'], 1);
			$this->assertEquals($mediasArray[0]['title'], "Une image");
			$this->assertEquals($mediasArray[0]['description'], "Elle se regarde");
			$this->assertEquals($mediasArray[0]['url'], "http://www.unsite.fr/picture.jpg");
			$this->assertEquals($mediasArray[0]['width'], 400);
			$this->assertEquals($mediasArray[0]['height'], 300);
			$this->assertEquals($mediasArray[0]['preview'], "http://www.unsite.fr/preview.jpg");
			$this->assertEquals($mediasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['setDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['isValid'], 1);
			$this->assertEquals($mediasArray[0]['users_id'], 1);
		}

		public function testGetParentMediasText()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('metas', 'medias_text.*')
					->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
					->join('medias_text', 'metas_assoc.assoc_id = medias_text.id')
					->where('metas.id = 1')
					->where("metas_assoc.assocType = 'Media_Text'");

			$mediasArray = $db->fetchAll($select);

			$this->assertEquals($mediasArray[0]['id'], 1);
			$this->assertEquals($mediasArray[0]['title'], "Un texte");
			$this->assertEquals($mediasArray[0]['description'], "il se lit");
			$this->assertEquals($mediasArray[0]['content'], "Un texte");
			$this->assertEquals($mediasArray[0]['preview'], "http://www.unsite.fr/preview.jpg");
			$this->assertEquals($mediasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['setDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['isValid'], 1);
			$this->assertEquals($mediasArray[0]['users_id'], 1);
		}

		public function testGetParentMediasVideo()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('metas', 'medias_video.*')
					->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
					->join('medias_video', 'metas_assoc.assoc_id = medias_video.id')
					->where('metas.id = 1')
					->where("metas_assoc.assocType = 'Media_Video'");

			$mediasArray = $db->fetchAll($select);

			$this->assertEquals($mediasArray[0]['id'], 1);
			$this->assertEquals($mediasArray[0]['title'], "Une video");
			$this->assertEquals($mediasArray[0]['description'], "il s ecoute et se regarde");
			$this->assertEquals($mediasArray[0]['url'], "http://www.unsite.fr/video.flv");
			$this->assertEquals($mediasArray[0]['totalTime'], 180);
			$this->assertEquals($mediasArray[0]['width'], 400);
			$this->assertEquals($mediasArray[0]['height'], 300);
			$this->assertEquals($mediasArray[0]['preview'], "http://www.unsite.fr/preview.jpg");
			$this->assertEquals($mediasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['setDate'], "2009-04-15 23:55:36");
			$this->assertEquals($mediasArray[0]['isValid'], 1);
			$this->assertEquals($mediasArray[0]['users_id'], 1);
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
					->where("datas_assoc.assocType = 'Item'");

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
					->where("datas_assoc.assocType = 'Item'");

			$datasArray = $db->fetchAll($select);

			$this->assertEquals($datasArray[0]['id'], 1);
			$this->assertEquals($datasArray[0]['x'], 1.89489e+06);
			$this->assertEquals($datasArray[0]['y'], 7881);
			$this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
		}

		public function testGetParentDatasVote()
		{
			$db = Zend_Registry::get('db');
			$select = $db->select();
			$select->from('items', 'datas_vote.*')
					->join('datas_assoc', 'datas_assoc.assoc_id = items.id')
					->join('datas_vote', 'datas_assoc.datas_id = datas_vote.id')
					->where('items.id = 1')
					->where("datas_assoc.dataType = 'Vote'")
					->where("datas_assoc.assocType = 'Item'");

			$datasArray = $db->fetchAll($select);

			$this->assertEquals($datasArray[0]['id'], 1);
			$this->assertEquals($datasArray[0]['rate'], 1);
			$this->assertEquals($datasArray[0]['users_id'], 1);
			$this->assertEquals($datasArray[0]['addDate'], "2009-04-15 23:55:36");
			$this->assertEquals($datasArray[0]['setDate'], "2009-04-15 23:55:36");
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}