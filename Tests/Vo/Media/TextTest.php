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

  class Vo_Media_TextTest extends PHPUnit_Extensions_Database_TestCase
  {

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;

    /**
     * Table MediasText
     *
     * @var Table_Medias_Text
     */
    private $_mediasTextTable;

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
      $this->_mediasTextTable = new Table_Medias_Text();
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
      $textArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'content' => 'un texte'
      );
      $text = new Vo_Media_Text($textArray);

      $this->assertEquals($text->id, $textArray['id']);
      $this->assertEquals($text->title, $textArray['title']);
      $this->assertEquals($text->description, $textArray['description']);
      $this->assertEquals($text->preview, $textArray['preview']);

      $this->assertTrue($text->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($text->addDate, new Zend_Date($textArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($text->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($text->setDate, new Zend_Date($textArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($text->content, $textArray['content']);
    }

    public function testConstructObject()
    {
      $textArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'content' => 'un texte'
      );
      $textObject = new Vo_Media_Text($textArray);

      $text = new Vo_Media_Text($textObject);

      $this->assertEquals($text->id, $textArray['id']);
      $this->assertEquals($text->title, $textArray['title']);
      $this->assertEquals($text->description, $textArray['description']);
      $this->assertEquals($text->preview, $textArray['preview']);

      $this->assertTrue($text->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($text->addDate, new Zend_Date($textArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($text->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($text->setDate, new Zend_Date($textArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($text->content, $textArray['content']);
    }

    public function testConstructZendDbTableRow()
    {
      $textRow = $this->_mediasTextTable->find(1)->current();
      $text = new Vo_Media_Text($textRow);

      $this->assertEquals($text->id, $textRow->id);
      $this->assertEquals($text->title, $textRow->title);
      $this->assertEquals($text->description, $textRow->description);
      $this->assertEquals($text->preview, $textRow->preview);

      $this->assertTrue($text->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
      $this->assertEquals($text->addDate, new Zend_Date($textRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertTrue($text->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
      $this->assertEquals($text->setDate, new Zend_Date($textRow->setDate, 'YYYY.MM.dd HH:mm:ss'));

      $this->assertEquals($text->content, $textRow->content);
    }

    public function testException()
    {
      $textArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'content' => 'un texte'
      );
      $text = new Vo_Media_Text($textArray);

      try {
        $text->bou = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 1);
      }

      try {
        $bou = $text->bou;
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 2);
      }

      try {
        $textString = new Vo_Media_Text('bou');
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 3);
      }

      try {
        $text->addDate = 'bou';
      }
      catch(Vo_Exception $e)
      {
        $this->assertEquals($e->getCode(), 4);
      }
    }

    public function testToArray()
    {
      $textArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => '2007.03.10 00:00:00',
        '_isValid' => true,
        'content' => 'un texte'
      );
      $text = new Vo_Media_Text($textArray);

      $this->assertEquals($text->toArray(), $textArray);
    }

    public function testGetType()
    {
      $text = new Vo_Media_Text();
      $this->assertEquals($text->getType(), 'Text');
    }

    public function testValidate()
    {
      $textArray = array(
        'id' => 1,
        '_user'    => 1,
        'title' => 'mon mŽdia',
        'description' => 'une description',
        'preview' => 'http://www.unsite.fr/preview.jpg',
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        '_isValid' => true,
        'content' => 'un texte'
      );
      $text = new Vo_Media_Text($textArray);

      $this->assertTrue($text->isValid());
      $text->validate(false);
      $this->assertFalse($text->isValid());
      $text->validate(true);
      $this->assertTrue($text->isValid());
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }
