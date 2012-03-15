<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Vo_Media_PictureTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table MediasPicture
		 *
		 * @var Table_Medias_Picture
		 */
		private $_mediasPictureTable;

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
			$this->_mediasPictureTable = new Table_Medias_Picture();
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
			$pictureArray = array(
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
				'height' => 156.78
			);
			$picture = new Vo_Media_Picture($pictureArray);

			$this->assertEquals($picture->id, $pictureArray['id']);
			$this->assertEquals($picture->title, $pictureArray['title']);
			$this->assertEquals($picture->description, $pictureArray['description']);
			$this->assertEquals($picture->preview, $pictureArray['preview']);

			$this->assertTrue($picture->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($picture->addDate, new Zend_Date($pictureArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($picture->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($picture->setDate, new Zend_Date($pictureArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($picture->url, $pictureArray['url']);
			$this->assertEquals($picture->width, $pictureArray['width']);
			$this->assertEquals($picture->height, $pictureArray['height']);
		}

		public function testConstructObject()
		{
			$pictureArray = array(
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
				'height' => 156.78
			);
			$pictureObject = new Vo_Media_Picture($pictureArray);

			$picture = new Vo_Media_Picture($pictureObject);

			$this->assertEquals($picture->id, $pictureArray['id']);
			$this->assertEquals($picture->title, $pictureArray['title']);
			$this->assertEquals($picture->description, $pictureArray['description']);
			$this->assertEquals($picture->preview, $pictureArray['preview']);

			$this->assertTrue($picture->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($picture->addDate, new Zend_Date($pictureArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($picture->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($picture->setDate, new Zend_Date($pictureArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($picture->url, $pictureArray['url']);
			$this->assertEquals($picture->width, $pictureArray['width']);
			$this->assertEquals($picture->height, $pictureArray['height']);
		}

		public function testConstructZendDbTableRow()
		{
			$pictureRow = $this->_mediasPictureTable->find(1)->current();
			$picture = new Vo_Media_Picture($pictureRow);

			$this->assertEquals($picture->id, $pictureRow->id);
			$this->assertEquals($picture->title, $pictureRow->title);
			$this->assertEquals($picture->description, $pictureRow->description);
			$this->assertEquals($picture->preview, $pictureRow->preview);

			$this->assertTrue($picture->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($picture->addDate, new Zend_Date($pictureRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($picture->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($picture->setDate, new Zend_Date($pictureRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertEquals($picture->url, $pictureRow->url);
			$this->assertEquals($picture->width, $pictureRow->width);
			$this->assertEquals($picture->height, $pictureRow->height);
		}

		public function testException()
		{
			$pictureArray = array(
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
				'height' => 156.78
			);
			$picture = new Vo_Media_Picture($pictureArray);

			try {
				$picture->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $picture->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$pictureString = new Vo_Data_Adress('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$picture->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$pictureArray = array(
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
				'height' => 156.78
			);
			$picture = new Vo_Media_Picture($pictureArray);

			$this->assertEquals($picture->toArray(), $pictureArray);
		}

		public function testGetType()
		{
			$picture = new Vo_Media_Picture();
			$this->assertEquals($picture->getType(), 'Picture');
		}

		public function testValidate()
		{

			$pictureArray = array(
				'id' => 1,
				'title' => 'mon m仕ia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78
			);
			$picture = new Vo_Media_Picture($pictureArray);

			$this->assertTrue($picture->isValid());
			$picture->validate(false);
			$this->assertFalse($picture->isValid());
			$picture->validate(true);
			$this->assertTrue($picture->isValid());
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
