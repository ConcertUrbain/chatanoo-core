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

	class Vo_Media_VideoTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table MediasVideo
		 *
		 * @var Table_Medias_Video
		 */
		private $_mediasVideoTable;

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
			$this->_mediasVideoTable = new Table_Medias_Video();
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
			$videoArray = array(
				'id' => 1,
				'_user'		=> 1,
				'title' => 'mon m仕ia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78,
				'totalTime' => 180
			);
			$video = new Vo_Media_Video($videoArray);

			$this->assertEquals($video->id, $videoArray['id']);
			$this->assertEquals($video->title, $videoArray['title']);
			$this->assertEquals($video->description, $videoArray['description']);
			$this->assertEquals($video->preview, $videoArray['preview']);

			$this->assertTrue($video->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($video->addDate, new Zend_Date($videoArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($video->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($video->setDate, new Zend_Date($videoArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($video->url, $videoArray['url']);
			$this->assertEquals($video->width, $videoArray['width']);
			$this->assertEquals($video->height, $videoArray['height']);
			$this->assertEquals($video->totalTime, $videoArray['totalTime']);
		}

		public function testConstructObject()
		{
			$videoArray = array(
				'id' => 1,
				'_user'		=> 1,
				'title' => 'mon m仕ia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78,
				'totalTime' => 180
			);
			$videoObject = new Vo_Media_Video($videoArray);

			$video = new Vo_Media_Video($videoObject);

			$this->assertEquals($video->id, $videoArray['id']);
			$this->assertEquals($video->title, $videoArray['title']);
			$this->assertEquals($video->description, $videoArray['description']);
			$this->assertEquals($video->preview, $videoArray['preview']);

			$this->assertTrue($video->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($video->addDate, new Zend_Date($videoArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($video->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($video->setDate, new Zend_Date($videoArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($video->url, $videoArray['url']);
			$this->assertEquals($video->width, $videoArray['width']);
			$this->assertEquals($video->height, $videoArray['height']);
			$this->assertEquals($video->totalTime, $videoArray['totalTime']);
		}

		public function testConstructZendDbTableRow()
		{
			$videoRow = $this->_mediasVideoTable->find(1)->current();
			$video = new Vo_Media_Video($videoRow);

			$this->assertEquals($video->id, $videoRow->id);
			$this->assertEquals($video->title, $videoRow->title);
			$this->assertEquals($video->description, $videoRow->description);
			$this->assertEquals($video->preview, $videoRow->preview);

			$this->assertTrue($video->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($video->addDate, new Zend_Date($videoRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($video->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($video->setDate, new Zend_Date($videoRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($video->url, $videoRow->url);
			$this->assertEquals($video->width, $videoRow->width);
			$this->assertEquals($video->height, $videoRow->height);
			$this->assertEquals($video->totalTime, $videoRow->totalTime);
		}

		public function testException()
		{
			$videoArray = array(
				'id' => 1,
				'_user'		=> 1,
				'title' => 'mon m仕ia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78,
				'totalTime' => 180
			);
			$video = new Vo_Media_Video($videoArray);

			try {
				$video->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $video->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$videoString = new Vo_Media_Video('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$video->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$videoArray = array(
				'id' => 1,
				'_user'		=> 1,
				'title' => 'mon m仕ia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78,
				'totalTime' => 180
			);
			$video = new Vo_Media_Video($videoArray);

			$this->assertEquals($video->toArray(), $videoArray);
		}

		public function testGetType()
		{
			$video = new Vo_Media_Video();
			$this->assertEquals($video->getType(), 'Video');
		}

		public function testValidate()
		{
			$videoArray = array(
				'id' => 1,
				'_user'		=> 1,
				'title' => 'mon m仕ia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78,
				'totalTime' => 180
			);
			$video = new Vo_Media_Video($videoArray);

			$this->assertTrue($video->isValid());
			$video->validate(false);
			$this->assertFalse($video->isValid());
			$video->validate(true);
			$this->assertTrue($video->isValid());
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
