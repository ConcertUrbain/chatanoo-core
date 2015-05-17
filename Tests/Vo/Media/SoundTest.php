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

  class Vo_Media_SoundTest extends PHPUnit_Extensions_Database_TestCase
  {

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;

    /**
     * Table MediasSound
     *
     * @var Table_Medias_Sound
     */
    private $_mediasSoundTable;

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
      $this->_mediasSoundTable = new Table_Medias_Sound();
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
      $soundArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'url' => 'http://www.unsite.fr/sound.jpg',
        'totalTime' => 180
      );
      $sound = new Vo_Media_Sound($soundArray);

      $this->assertEquals($sound->id, $soundArray['id']);
      $this->assertEquals($sound->title, $soundArray['title']);
      $this->assertEquals($sound->description, $soundArray['description']);
      $this->assertEquals($sound->preview, $soundArray['preview']);

      $this->assertTrue($sound->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($sound->addDate, new Zend_Date($soundArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($sound->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($sound->setDate, new Zend_Date($soundArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($sound->url, $soundArray['url']);
      $this->assertEquals($sound->totalTime, $soundArray['totalTime']);
    }

    public function testConstructObject()
    {
      $soundArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'url' => 'http://www.unsite.fr/sound.jpg',
        'totalTime' => 180
      );
      $soundObject = new Vo_Media_Sound($soundArray);

      $sound = new Vo_Media_Sound($soundObject);

      $this->assertEquals($sound->id, $soundArray['id']);
      $this->assertEquals($sound->title, $soundArray['title']);
      $this->assertEquals($sound->description, $soundArray['description']);
      $this->assertEquals($sound->preview, $soundArray['preview']);

      $this->assertTrue($sound->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($sound->addDate, new Zend_Date($soundArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($sound->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($sound->setDate, new Zend_Date($soundArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($sound->url, $soundArray['url']);
      $this->assertEquals($sound->totalTime, $soundArray['totalTime']);
    }

    public function testConstructZendDbTableRow()
    {
      $soundRow = $this->_mediasSoundTable->find(1)->current();
      $sound = new Vo_Media_Sound($soundRow);

      $this->assertEquals($sound->id, $soundRow->id);
      $this->assertEquals($sound->title, $soundRow->title);
      $this->assertEquals($sound->description, $soundRow->description);
      $this->assertEquals($sound->preview, $soundRow->preview);

      $this->assertTrue($sound->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($sound->addDate, new Zend_Date($soundRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($sound->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($sound->setDate, new Zend_Date($soundRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($sound->url, $soundRow->url);
      $this->assertEquals($sound->totalTime, $soundRow->totalTime);
    }

    public function testException()
    {
      $soundArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'url' => 'http://www.unsite.fr/sound.jpg',
        'totalTime' => 180
      );
      $sound = new Vo_Media_Sound($soundArray);

      try {
        $sound->bou = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 1);
      }

      try {
        $bou = $sound->bou;
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 2);
      }

      try {
        $soundString = new Vo_Media_Sound('bou');
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 3);
      }

      try {
        $sound->addDate = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 4);
      }
    }

    public function testToArray()
    {
      $soundArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => '2007.03.10 00:00:00',
        '_isValid' => true,
        'url' => 'http://www.unsite.fr/sound.jpg',
        'totalTime' => 180
      );
      $sound = new Vo_Media_Sound($soundArray);

      $this->assertEquals($sound->toArray(), $soundArray);
    }

    public function testGetType()
    {
      $sound = new Vo_Media_Sound();
      $this->assertEquals($sound->getType(), 'Sound');
    }

    public function testValidate()
    {
      $soundArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'url' => 'http://www.unsite.fr/sound.jpg',
        'totalTime' => 180
      );
      $sound = new Vo_Media_Sound($soundArray);

      $this->assertTrue($sound->isValid());
      $sound->validate(false);
      $this->assertFalse($sound->isValid());
      $sound->validate(true);
      $this->assertTrue($sound->isValid());
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }
