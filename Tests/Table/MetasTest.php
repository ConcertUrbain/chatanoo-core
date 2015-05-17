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

  class Table_MetasTest extends PHPUnit_Extensions_Database_TestCase
  {
    /**
     * Table users
     *
     * @var Table_Metas
     */
    private $_metasTable;

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
      $this->_metasTable = new Table_Metas();
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
      $metaRowArray = $this->_metasTable->createRow()->toArray();
      $this->assertArrayHasKey('id', $metaRowArray);
      $this->assertArrayHasKey('name', $metaRowArray);
      $this->assertArrayHasKey('content', $metaRowArray);
    }

    public function testFind()
    {
      $metaRow = $this->_metasTable->find(1)->current();
      $this->assertEquals($metaRow->id, 1);
      $this->assertEquals($metaRow->name, 'keyword');
      $this->assertEquals($metaRow->content, 'Un meta');
    }

    public function testAddItem()
    {
      $metaArray = array(
        'name' => 'keyword',
        'content' => 'Un meta 2',
        'sessions_id' => 1
      );
      $metaRow = $this->_metasTable->createRow($metaArray);
      $metaRow->save();

      $insertXml = dirname(__FILE__) . '/Flats/metas-insert-data.xml';
      $this->assertDataSetsEqual(
        $this->createFlatXMLDataSet($insertXml),
        $this->getConnection()->createDataSet()
      );
    }

    public function testGetParentSessions()
    {
      $db = Zend_Registry::get('db');
      $select = $db->select();
      $select->from('metas', 'sessions.*')
          ->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
          ->join('sessions', 'metas_assoc.assoc_id = sessions.id')
          ->where('metas.id = 1')
          ->where("metas_assoc.assocType = 'Session'");

      $sessionsArray = $db->fetchAll($select);

      $this->assertEquals($sessionsArray[0]['id'], 1);
      $this->assertEquals($sessionsArray[0]['title'], "Ma session");
      $this->assertEquals($sessionsArray[0]['description'], "Ma description");
      $this->assertEquals($sessionsArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($sessionsArray[0]['setDate'], "2009-04-15 23:55:36");
      $this->assertEquals($sessionsArray[0]['publishDate'], "2009-04-15 23:55:36");
      $this->assertEquals($sessionsArray[0]['endDate'], "2009-04-15 23:55:36");
      $this->assertEquals($sessionsArray[0]['users_id'], 1);
    }

    public function testGetParentQueries()
    {
      $db = Zend_Registry::get('db');
      $select = $db->select();
      $select->from('metas', 'queries.*')
          ->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
          ->join('queries', 'metas_assoc.assoc_id = queries.id')
          ->where('metas.id = 1')
          ->where("metas_assoc.assocType = 'Query'");

      $queriesArray = $db->fetchAll($select);

      $this->assertEquals($queriesArray[0]['id'], 1);
      $this->assertEquals($queriesArray[0]['content'], "Une question ?");
      $this->assertEquals($queriesArray[0]['description'], "bah une question");
      $this->assertEquals($queriesArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($queriesArray[0]['setDate'], "2009-04-15 23:55:36");
      $this->assertEquals($queriesArray[0]['publishDate'], "2009-04-15 23:55:36");
      $this->assertEquals($queriesArray[0]['endDate'], "2009-04-15 23:55:36");
      $this->assertEquals($queriesArray[0]['users_id'], 1);
    }

    public function testGetParentItems()
    {
      $db = Zend_Registry::get('db');
      $select = $db->select();
      $select->from('metas', 'items.*')
          ->join('metas_assoc', 'metas_assoc.metas_id = metas.id')
          ->join('items', 'metas_assoc.assoc_id = items.id')
          ->where('metas.id = 1')
          ->where("metas_assoc.assocType = 'Item'");

      $itemsArray = $db->fetchAll($select);

      $this->assertEquals($itemsArray[0]['id'], 1);
      $this->assertEquals($itemsArray[0]['title'], "Mon Item");
      $this->assertEquals($itemsArray[0]['description'], "Ma description");
      $this->assertEquals($itemsArray[0]['addDate'], "2009-04-15 23:55:36");
      $this->assertEquals($itemsArray[0]['setDate'], "2009-04-15 23:55:36");
      $this->assertEquals($itemsArray[0]['isValid'], 1);
      $this->assertEquals($itemsArray[0]['users_id'], 1);
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

    public function testDeleteComment()
    {
      $metaRow = $this->_metasTable->find(1)->current();
      $metaRow->delete();
      $this->assertEquals($this->_metasTable->fetchAll()->count(), 0);
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }