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

	class Service_MediasTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Service Medias
		 *
		 * @var Service_Medias
		 */
		private $_mediasService;

		/**
		 * Service Datas
		 *
		 * @var Service_Datas
		 */
		private $_datasService;

		/**
		 * Tableau des tables de datas
		 *
		 * @var array
		 */
		private $_datasTables;

	    /**
	     * Passerelles vers la table de liaison des datas
	     *
	     * @access private
	     * @var Table_Datas_Assoc
	     */
	    private $_datasAssocTable = null;

	    /**
	     * Passerelles vers la table de liaison des mŽdias
	     *
	     * @access protected
	     * @var Assoc
	     */
	    protected $_mediasAssocTable = null;

		/**
		 * Table metas_assoc
		 *
		 * @var Table_MetasAssoc
		 */
		private $_metasAssocTable;


		/**
		 * Service Users
		 *
		 * @var Service_Users
		 */
		private $_usersService;

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
			$this->_mediasService = new Service_Medias();
			$this->_datasService = new Service_Datas();
			$this->_mediasAssocTable = new Table_Medias_Assoc();
			$this->_datasAssocTable = new Table_Datas_Assoc();
			$this->_mediasTables = array(
				'Picture' => new Table_Medias_Picture(),
				'Sound' => new Table_Medias_Sound(),
				'Text' => new Table_Medias_Text(),
				'Video' => new Table_Medias_Video()
			);
			$this->_usersService = new Service_Users();
			$this->_metasAssocTable = new Table_MetasAssoc();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testGetMedias()
		{
			$medias = $this->_mediasService->getMedias();

			$this->assertTrue(is_array($medias));
			$this->assertEquals(count($medias), 4);

			$mediaPicture = $medias['Picture'][0];
			$this->assertTrue($mediaPicture instanceof Vo_Media_Picture);
			$this->assertEquals($mediaPicture->id, 1);
			$this->assertEquals($mediaPicture->title, 'Une image');
			$this->assertEquals($mediaPicture->description, 'Elle se regarde');
			$this->assertEquals($mediaPicture->url, 'http://www.unsite.fr/picture.jpg');
			$this->assertEquals($mediaPicture->width, 400);
			$this->assertEquals($mediaPicture->height, 300);
			$this->assertEquals($mediaPicture->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaPicture->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaPicture->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaPicture->isValid());

			$mediaSound = $medias['Sound'][0];
			$this->assertTrue($mediaSound instanceof Vo_Media_Sound);
			$this->assertEquals($mediaSound->id, 1);
			$this->assertEquals($mediaSound->title, 'Un son');
			$this->assertEquals($mediaSound->description, 'il s ecoute');
			$this->assertEquals($mediaSound->url, 'http://www.unsite.fr/sound.mp3');
			$this->assertEquals($mediaSound->totalTime, 180);
			$this->assertEquals($mediaSound->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaSound->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaSound->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaSound->isValid());

			$mediaText = $medias['Text'][0];
			$this->assertTrue($mediaText instanceof Vo_Media_Text);
			$this->assertEquals($mediaText->id, 1);
			$this->assertEquals($mediaText->title, 'Un texte');
			$this->assertEquals($mediaText->description, 'il se lit');
			$this->assertEquals($mediaText->content, 'Un texte');
			$this->assertEquals($mediaText->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaText->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaText->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaText->isValid());

			$mediaVideo = $medias['Video'][0];
			$this->assertTrue($mediaVideo instanceof Vo_Media_Video);
			$this->assertEquals($mediaVideo->id, 1);
			$this->assertEquals($mediaVideo->title, 'Une video');
			$this->assertEquals($mediaVideo->description, 'il s ecoute et se regarde');
			$this->assertEquals($mediaVideo->url, 'http://www.unsite.fr/video.flv');
			$this->assertEquals($mediaVideo->width, 400);
			$this->assertEquals($mediaVideo->height, 300);
			$this->assertEquals($mediaVideo->totalTime, 180);
			$this->assertEquals($mediaVideo->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaVideo->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaVideo->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaVideo->isValid());
		}

		public function testGetMediaById()
		{
			$mediaPicture = $this->_mediasService->getMediaById(1, 'Picture');
			$this->assertTrue($mediaPicture instanceof Vo_Media_Picture);
			$this->assertEquals($mediaPicture->id, 1);
			$this->assertEquals($mediaPicture->title, 'Une image');
			$this->assertEquals($mediaPicture->description, 'Elle se regarde');
			$this->assertEquals($mediaPicture->url, 'http://www.unsite.fr/picture.jpg');
			$this->assertEquals($mediaPicture->width, 400);
			$this->assertEquals($mediaPicture->height, 300);
			$this->assertEquals($mediaPicture->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaPicture->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaPicture->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaPicture->isValid());
		}

		public function testGetMediasByItemId()
		{
			$medias = $this->_mediasService->getMediasByItemId(1);

			$this->assertTrue(is_array($medias));
			$this->assertEquals(count($medias), 4);

			$mediaPicture = $medias['Picture'][0];
			$this->assertTrue($mediaPicture instanceof Vo_Media_Picture);
			$this->assertEquals($mediaPicture->id, 1);
			$this->assertEquals($mediaPicture->title, 'Une image');
			$this->assertEquals($mediaPicture->description, 'Elle se regarde');
			$this->assertEquals($mediaPicture->url, 'http://www.unsite.fr/picture.jpg');
			$this->assertEquals($mediaPicture->width, 400);
			$this->assertEquals($mediaPicture->height, 300);
			$this->assertEquals($mediaPicture->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaPicture->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaPicture->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaPicture->isValid());

			$mediaSound = $medias['Sound'][0];
			$this->assertTrue($mediaSound instanceof Vo_Media_Sound);
			$this->assertEquals($mediaSound->id, 1);
			$this->assertEquals($mediaSound->title, 'Un son');
			$this->assertEquals($mediaSound->description, 'il s ecoute');
			$this->assertEquals($mediaSound->url, 'http://www.unsite.fr/sound.mp3');
			$this->assertEquals($mediaSound->totalTime, 180);
			$this->assertEquals($mediaSound->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaSound->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaSound->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaSound->isValid());

			$mediaText = $medias['Text'][0];
			$this->assertTrue($mediaText instanceof Vo_Media_Text);
			$this->assertEquals($mediaText->id, 1);
			$this->assertEquals($mediaText->title, 'Un texte');
			$this->assertEquals($mediaText->description, 'il se lit');
			$this->assertEquals($mediaText->content, 'Un texte');
			$this->assertEquals($mediaText->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaText->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaText->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaText->isValid());

			$mediaVideo = $medias['Video'][0];
			$this->assertTrue($mediaVideo instanceof Vo_Media_Video);
			$this->assertEquals($mediaVideo->id, 1);
			$this->assertEquals($mediaVideo->title, 'Une video');
			$this->assertEquals($mediaVideo->description, 'il s ecoute et se regarde');
			$this->assertEquals($mediaVideo->url, 'http://www.unsite.fr/video.flv');
			$this->assertEquals($mediaVideo->width, 400);
			$this->assertEquals($mediaVideo->height, 300);
			$this->assertEquals($mediaVideo->totalTime, 180);
			$this->assertEquals($mediaVideo->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaVideo->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaVideo->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaVideo->isValid());
		}

		public function testGetMediasByMetaId()
		{
			$medias = $this->_mediasService->getMediasByMetaId(1);

			$this->assertTrue(is_array($medias));
			$this->assertEquals(count($medias), 4);

			$mediaPicture = $medias['Picture'][0];
			$this->assertTrue($mediaPicture instanceof Vo_Media_Picture);
			$this->assertEquals($mediaPicture->id, 1);
			$this->assertEquals($mediaPicture->title, 'Une image');
			$this->assertEquals($mediaPicture->description, 'Elle se regarde');
			$this->assertEquals($mediaPicture->url, 'http://www.unsite.fr/picture.jpg');
			$this->assertEquals($mediaPicture->width, 400);
			$this->assertEquals($mediaPicture->height, 300);
			$this->assertEquals($mediaPicture->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaPicture->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaPicture->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaPicture->isValid());

			$mediaSound = $medias['Sound'][0];
			$this->assertTrue($mediaSound instanceof Vo_Media_Sound);
			$this->assertEquals($mediaSound->id, 1);
			$this->assertEquals($mediaSound->title, 'Un son');
			$this->assertEquals($mediaSound->description, 'il s ecoute');
			$this->assertEquals($mediaSound->url, 'http://www.unsite.fr/sound.mp3');
			$this->assertEquals($mediaSound->totalTime, 180);
			$this->assertEquals($mediaSound->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaSound->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaSound->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaSound->isValid());

			$mediaText = $medias['Text'][0];
			$this->assertTrue($mediaText instanceof Vo_Media_Text);
			$this->assertEquals($mediaText->id, 1);
			$this->assertEquals($mediaText->title, 'Un texte');
			$this->assertEquals($mediaText->description, 'il se lit');
			$this->assertEquals($mediaText->content, 'Un texte');
			$this->assertEquals($mediaText->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaText->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaText->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaText->isValid());

			$mediaVideo = $medias['Video'][0];
			$this->assertTrue($mediaVideo instanceof Vo_Media_Video);
			$this->assertEquals($mediaVideo->id, 1);
			$this->assertEquals($mediaVideo->title, 'Une video');
			$this->assertEquals($mediaVideo->description, 'il s ecoute et se regarde');
			$this->assertEquals($mediaVideo->url, 'http://www.unsite.fr/video.flv');
			$this->assertEquals($mediaVideo->width, 400);
			$this->assertEquals($mediaVideo->height, 300);
			$this->assertEquals($mediaVideo->totalTime, 180);
			$this->assertEquals($mediaVideo->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaVideo->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaVideo->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaVideo->isValid());
		}

		public function testGetMediasByQueryId()
		{
			$medias = $this->_mediasService->getMediasByQueryId(1);

			$this->assertTrue(is_array($medias));
			$this->assertEquals(count($medias), 1);

			$mediaVideo = $medias['Video'][0];
			$this->assertTrue($mediaVideo instanceof Vo_Media_Video);
			$this->assertEquals($mediaVideo->id, 1);
			$this->assertEquals($mediaVideo->title, 'Une video');
			$this->assertEquals($mediaVideo->description, 'il s ecoute et se regarde');
			$this->assertEquals($mediaVideo->url, 'http://www.unsite.fr/video.flv');
			$this->assertEquals($mediaVideo->width, 400);
			$this->assertEquals($mediaVideo->height, 300);
			$this->assertEquals($mediaVideo->totalTime, 180);
			$this->assertEquals($mediaVideo->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaVideo->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaVideo->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaVideo->isValid());
		}

		public function testAddMedia()
		{
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
			$this->_mediasService->addMedia($mediaPicture);
			$picture = $this->_mediasService->getMediaById(2, 'Picture');
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

			$date = Zend_Date::now();
			$soundArray = array(
				'title' => 'Title 2',
				'description' => 'Description 2',
				'url' => 'http://www.unsite.fr/',
				'totalTime' => 800,
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'user' => 1,
				'isValid' => 0
			);
			$mediaSound = new Vo_Media_Sound($soundArray);
			$this->_mediasService->addMedia($mediaSound);
			$sound = $this->_mediasService->getMediaById(2, 'Sound');
			$this->assertTrue($sound instanceof Vo_Media_Sound);
			$this->assertEquals($sound->id, 2);
			$this->assertEquals($sound->title, $soundArray['title']);
			$this->assertEquals($sound->description, $soundArray['description']);
			$this->assertEquals($sound->url, $soundArray['url']);
			$this->assertEquals($sound->totalTime, $soundArray['totalTime']);
			$this->assertEquals($sound->addDate, $date);
			$this->assertEquals($sound->setDate, $date);
			$this->assertFalse($sound->isValid());

			$date = Zend_Date::now();
			$textArray = array(
				'title' => 'Title 2',
				'description' => 'Description 2',
				'content' => 'bou',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'user' => 1,
				'isValid' => 1
			);
			$mediaText = new Vo_Media_Text($textArray);
			$this->_mediasService->addMedia($mediaText);
			$text = $this->_mediasService->getMediaById(2, 'Text');
			$this->assertTrue($text instanceof Vo_Media_Text);
			$this->assertEquals($text->id, 2);
			$this->assertEquals($text->title, $textArray['title']);
			$this->assertEquals($text->description, $textArray['description']);
			$this->assertEquals($text->content, $textArray['content']);
			$this->assertEquals($text->preview, $textArray['preview']);
			$this->assertEquals($text->addDate, $date);
			$this->assertEquals($text->setDate, $date);
			$this->assertTrue($text->isValid());

			$date = Zend_Date::now();
			$videoArray = array(
				'title' => 'Title 2',
				'description' => 'Description 2',
				'url' => 'http://www.unsite.fr/',
				'width' => 800,
				'height' => 600,
				'totalTime' => 800,
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'user' => 1,
				'isValid' => 1
			);
			$mediaVideo = new Vo_Media_Video($videoArray);
			$this->_mediasService->addMedia($mediaVideo);
			$video = $this->_mediasService->getMediaById(2, 'Video');
			$this->assertTrue($video instanceof Vo_Media_Video);
			$this->assertEquals($video->id, 2);
			$this->assertEquals($video->title, $videoArray['title']);
			$this->assertEquals($video->description, $videoArray['description']);
			$this->assertEquals($video->url, $videoArray['url']);
			$this->assertEquals($video->width, $videoArray['width']);
			$this->assertEquals($video->height, $videoArray['height']);
			$this->assertEquals($video->totalTime, $videoArray['totalTime']);
			$this->assertEquals($video->preview, $videoArray['preview']);
			$this->assertEquals($video->addDate, $date);
			$this->assertEquals($video->setDate, $date);
			$this->assertTrue($video->isValid());
		}

		public function testSetMedia()
		{
			$mediaPicture = $this->_mediasService->getMediaById(1, 'Picture');
			$mediaPicture->title = 'bou';
			$date = Zend_Date::now();
			$this->_mediasService->setMedia($mediaPicture);
			$picture = $this->_mediasService->getMediaById(1, 'Picture');
			$this->assertTrue($picture instanceof Vo_Media_Picture);
			$this->assertEquals($picture->id, 1);
			$this->assertEquals($picture->title, $mediaPicture->title);
			$this->assertEquals($picture->description, $mediaPicture->description);
			$this->assertEquals($picture->preview, $mediaPicture->preview);
			$this->assertEquals($picture->addDate, new Zend_Date($mediaPicture->addDate, 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($picture->setDate, $date);
			$this->assertTrue($picture->isValid());

			$mediaSound = $this->_mediasService->getMediaById(1, 'Sound');
			$mediaSound->title = 'bou';
			$date = Zend_Date::now();
			$this->_mediasService->setMedia($mediaSound);
			$sound = $this->_mediasService->getMediaById(1, 'Sound');
			$this->assertTrue($sound instanceof Vo_Media_Sound);
			$this->assertEquals($sound->id, 1);
			$this->assertEquals($sound->title, $mediaSound->title);
			$this->assertEquals($sound->description, $mediaSound->description);
			$this->assertEquals($sound->preview, $mediaSound->preview);
			$this->assertEquals($sound->addDate, new Zend_Date($mediaSound->addDate, 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($sound->setDate, $date);
			$this->assertTrue($sound->isValid());

			$mediaText = $this->_mediasService->getMediaById(1, 'Text');
			$mediaText->title = 'bou';
			$date = Zend_Date::now();
			$this->_mediasService->setMedia($mediaText);
			$text = $this->_mediasService->getMediaById(1, 'Text');
			$this->assertTrue($text instanceof Vo_Media_Text);
			$this->assertEquals($text->id, 1);
			$this->assertEquals($text->title, $mediaText->title);
			$this->assertEquals($text->description, $mediaText->description);
			$this->assertEquals($text->preview, $mediaText->preview);
			$this->assertEquals($text->addDate, new Zend_Date($mediaText->addDate, 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($text->setDate, $date);
			$this->assertTrue($text->isValid());

			$mediaVideo = $this->_mediasService->getMediaById(1, 'Video');
			$mediaVideo->title = 'bou';
			$date = Zend_Date::now();
			$this->_mediasService->setMedia($mediaVideo);
			$video = $this->_mediasService->getMediaById(1, 'Video');
			$this->assertTrue($video instanceof Vo_Media_Video);
			$this->assertEquals($video->id, 1);
			$this->assertEquals($video->title, $mediaVideo->title);
			$this->assertEquals($video->description, $mediaVideo->description);
			$this->assertEquals($video->preview, $mediaVideo->preview);
			$this->assertEquals($video->addDate, new Zend_Date($mediaVideo->addDate, 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($video->setDate, $date);
			$this->assertTrue($video->isValid());
		}

		public function testDeleteMedia()
		{
			foreach($this->_mediasTables as $type=>$mediaTable)
			{
				$this->assertEquals($mediaTable->fetchAll()->count(), 1);
				$this->_mediasService->deleteMedia(1, $type);
				$this->assertEquals($mediaTable->fetchAll()->count(), 0);
				$select = $this->_mediasAssocTable->select();
				$select->where('medias_id = ?', 1)
						->where('mediaType = ?', $type);
				$rowset = $this->_mediasAssocTable->fetchAll($select);
				$this->assertEquals($rowset->count(), 0);
				$select = $this->_datasAssocTable->select();
				$select->where('assoc_id = ?', 1)
						->where('assocType = ?', $type);
				$rowset = $this->_datasAssocTable->fetchAll($select);
				$this->assertEquals($rowset->count(), 0);
			}

			$medias = $this->_mediasService->getMediasByItemId(1);
			$this->assertEquals(count($medias), 0);

			$medias = $this->_mediasService->getMediasByQueryId(1);
			$this->assertEquals(count($medias), 0);
		}

		public function testValidateMedia()
		{
			$media = $this->_mediasService->getMediaById(1, 'Picture');
			$this->assertTrue($media->isValid());
			$this->_mediasService->validateMedia(1, 'Picture', false);
			$media = $this->_mediasService->getMediaById(1, 'Picture');
			$this->assertFalse($media->isValid());
			$this->_mediasService->validateMedia(1, 'Picture', true);
			$media = $this->_mediasService->getMediaById(1, 'Picture');
			$this->assertTrue($media->isValid());
		}

		public function testAddMetaIntoMedia()
		{
			$meta = new Vo_Meta();
			$meta->content = 'meta';
			$this->_mediasService->addMetaIntoMedia($meta, 1, 'Picture');

			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = 2')
					->where('assoc_id = 1')
					->where('assocType = ?', 'Media_Picture');
			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNotNull($link);
		}

		public function testRemoveMetaFromMedia()
		{
			$select = $this->_metasAssocTable->select();
			$select->where('metas_id = 1')
					->where('assoc_id = 1')
					->where('assocType = ?', 'Media_Picture');
			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNotNull($link);

			$this->_mediasService->removeMetaFromMedia(1, 1, 'Picture');

			$link = $this->_metasAssocTable->fetchRow($select);
			$this->assertNull($link);
		}

		public function testAddDataIntoMedia()
		{
			$date = Zend_Date::now();
			$voteArray = array(
				'rate' => 1,
				'users_id' => 1
			);
			$data = new Vo_Data_Vote($voteArray);
			$this->_mediasService->addDataIntoMedia($data, 1, 'Picture');

			$datas = $this->_datasService->getDatasByMediaId(1, 'Picture');
			$this->assertArrayHasKey('Adress', $datas);
			$this->assertArrayHasKey('Carto', $datas);
			$this->assertArrayHasKey('Vote', $datas);

			$this->assertEquals(count($datas['Vote']), 1);
			$data = $datas['Vote'][0];
			$this->assertEquals($data->id, 2);
			$this->assertEquals($data->rate, 1);
			$this->assertEquals($data->addDate, $date);
			$this->assertEquals($data->setDate, $date);
		}

		public function testRemoveDataFromMedia()
		{
			$this->_mediasService->removeDataFromMedia(1, 'Carto', 1, 'Picture');
			$datas = $this->_datasService->getDatasByMediaId(1, 'Picture');
			$this->assertArrayNotHasKey('Carto', $datas);
		}

		public function testGetUserIntoMedia()
		{
			$user = $this->_mediasService->getUserFromMedia(1, 'Picture');

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

		public function testSetUserOfMedia()
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
			$this->_mediasService->setUserOfMedia($u->id, 1, 'Picture');

			$user = $this->_mediasService->getUserFromMedia(1, 'Picture');

			$this->assertTrue($user instanceof Vo_User);

			$this->assertEquals($user->id, 2);
			$this->assertEquals($user->firstName, 'Tsolova');
			$this->assertEquals($user->lastName, 'Irina');
			$this->assertEquals($user->pseudo, 'Irina');
			//$this->assertEquals($user->password, 'irinator');
			$this->assertEquals($user->email, 'tsolova_irina@yahoo.com');
			$this->assertEquals($user->role, 'user');
		}

		public function getMediasByUserId()
		{
			$medias = $this->_mediasService->getMediasByUserId(1);

			$this->assertTrue(is_array($medias));
			$this->assertEquals(count($medias), 4);

			$mediaPicture = $medias['Picture'][0];
			$this->assertTrue($mediaPicture instanceof Vo_Media_Picture);
			$this->assertEquals($mediaPicture->id, 1);
			$this->assertEquals($mediaPicture->title, 'Une image');
			$this->assertEquals($mediaPicture->description, 'Elle se regarde');
			$this->assertEquals($mediaPicture->url, 'http://www.unsite.fr/picture.jpg');
			$this->assertEquals($mediaPicture->width, 400);
			$this->assertEquals($mediaPicture->height, 300);
			$this->assertEquals($mediaPicture->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaPicture->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaPicture->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaPicture->isValid());

			$mediaSound = $medias['Sound'][0];
			$this->assertTrue($mediaSound instanceof Vo_Media_Sound);
			$this->assertEquals($mediaSound->id, 1);
			$this->assertEquals($mediaSound->title, 'Un son');
			$this->assertEquals($mediaSound->description, 'il s ecoute');
			$this->assertEquals($mediaSound->url, 'http://www.unsite.fr/sound.mp3');
			$this->assertEquals($mediaSound->totalTime, 180);
			$this->assertEquals($mediaSound->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaSound->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaSound->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaSound->isValid());

			$mediaText = $medias['Text'][0];
			$this->assertTrue($mediaText instanceof Vo_Media_Text);
			$this->assertEquals($mediaText->id, 1);
			$this->assertEquals($mediaText->title, 'Un texte');
			$this->assertEquals($mediaText->description, 'il se lit');
			$this->assertEquals($mediaText->content, 'Un texte');
			$this->assertEquals($mediaText->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaText->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaText->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaText->isValid());

			$mediaVideo = $medias['Video'][0];
			$this->assertTrue($mediaVideo instanceof Vo_Media_Video);
			$this->assertEquals($mediaVideo->id, 1);
			$this->assertEquals($mediaVideo->title, 'Une video');
			$this->assertEquals($mediaVideo->description, 'il s ecoute et se regarde');
			$this->assertEquals($mediaVideo->url, 'http://www.unsite.fr/video.flv');
			$this->assertEquals($mediaVideo->width, 400);
			$this->assertEquals($mediaVideo->height, 300);
			$this->assertEquals($mediaVideo->totalTime, 180);
			$this->assertEquals($mediaVideo->preview, 'http://www.unsite.fr/preview.jpg');
			$this->assertEquals($mediaVideo->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertEquals($mediaVideo->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
			$this->assertTrue($mediaVideo->isValid());
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}

