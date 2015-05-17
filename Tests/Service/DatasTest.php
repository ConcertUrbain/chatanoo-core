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

  class Service_DatasTest extends PHPUnit_Extensions_Database_TestCase
  {

    /**
     * PDO connection
     *
     * @var PDO
     */
    private $_pdo;

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
      $this->_datasService = new Service_Datas();
      $this->_datasAssocTable = new Table_Datas_Assoc();
      $this->_datasTables = array(
        'Adress' => new Table_Datas_Adress(),
        'Carto' => new Table_Datas_Carto(),
        'Vote' => new Table_Datas_Vote()
      );
    }

    public function testInitialisation()
    {
      $this->assertDataSetsEqual(
        $this->getDataSet(),
        $this->getConnection()->createDataSet()
      );
    }

    public function testGetDatas()
    {
      $datas = $this->_datasService->getDatas();

      $this->assertTrue(is_array($datas));
      $this->assertEquals(count($datas), 3);

      $dataAdress = $datas['Adress'][0];
      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 1);
      $this->assertEquals($dataAdress->adress, '8, rue de la Marne');
      $this->assertEquals($dataAdress->zipCode, 94500);
      $this->assertEquals($dataAdress->city, 'Champigny');
      $this->assertEquals($dataAdress->country, 'France');
      $this->assertEquals($dataAdress->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataAdress->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataCarto = $datas['Carto'][0];
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataVote = $datas['Vote'][0];
      $this->assertTrue($dataVote instanceof Vo_Data_Vote);
      $this->assertEquals($dataVote->id, 1);
      $this->assertEquals($dataVote->rate, 1);
      $this->assertEquals($dataVote->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataVote->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetDataById()
    {
      $dataAdress = $this->_datasService->getDatasById(1, 'Adress');

      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 1);
      $this->assertEquals($dataAdress->adress, '8, rue de la Marne');
      $this->assertEquals($dataAdress->zipCode, 94500);
      $this->assertEquals($dataAdress->city, 'Champigny');
      $this->assertEquals($dataAdress->country, 'France');
      $this->assertEquals($dataAdress->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataAdress->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataCarto = $this->_datasService->getDatasById(1, 'Carto');

      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataVote = $this->_datasService->getDatasById(1, 'Vote');

      $this->assertTrue($dataVote instanceof Vo_Data_Vote);
      $this->assertEquals($dataVote->id, 1);
      $this->assertEquals($dataVote->rate, 1);
      $this->assertEquals($dataVote->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataVote->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataNotExist = $this->_datasService->getDatasById(2, 'Vote');
      $this->assertNull($dataNotExist);
    }

    public function testGetDatasByItemId()
    {
      $datas = $this->_datasService->getDatasByItemId(1);

      $this->assertTrue(is_array($datas));
      $this->assertEquals(count($datas), 3);

      $dataAdress = $datas['Adress'][0];
      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 1);
      $this->assertEquals($dataAdress->adress, '8, rue de la Marne');
      $this->assertEquals($dataAdress->zipCode, 94500);
      $this->assertEquals($dataAdress->city, 'Champigny');
      $this->assertEquals($dataAdress->country, 'France');
      $this->assertEquals($dataAdress->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataAdress->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataCarto = $datas['Carto'][0];
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataVote = $datas['Vote'][0];
      $this->assertTrue($dataVote instanceof Vo_Data_Vote);
      $this->assertEquals($dataVote->id, 1);
      $this->assertEquals($dataVote->rate, 1);
      $this->assertEquals($dataVote->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataVote->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetDatasByCommentId()
    {
      $datas = $this->_datasService->getDatasByCommentId(1);

      $this->assertTrue(is_array($datas));
      $this->assertEquals(count($datas), 2);

      $dataCarto = $datas['Carto'][0];
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataVote = $datas['Vote'][0];
      $this->assertTrue($dataVote instanceof Vo_Data_Vote);
      $this->assertEquals($dataVote->id, 1);
      $this->assertEquals($dataVote->rate, 1);
      $this->assertEquals($dataVote->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataVote->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetDatasByMediaId()
    {
      $datas = $this->_datasService->getDatasByMediaId(1, 'Picture');

      $this->assertTrue(is_array($datas));
      $this->assertEquals(count($datas), 2);

      $dataAdress = $datas['Adress'][0];
      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 1);
      $this->assertEquals($dataAdress->adress, '8, rue de la Marne');
      $this->assertEquals($dataAdress->zipCode, 94500);
      $this->assertEquals($dataAdress->city, 'Champigny');
      $this->assertEquals($dataAdress->country, 'France');
      $this->assertEquals($dataAdress->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataAdress->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataCarto = $datas['Carto'][0];
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetDatasByUserId()
    {
      $datas = $this->_datasService->getDatasByUserId(1);

      $this->assertTrue(is_array($datas));
      $this->assertEquals(count($datas), 2);

      $dataAdress = $datas['Adress'][0];
      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 1);
      $this->assertEquals($dataAdress->adress, '8, rue de la Marne');
      $this->assertEquals($dataAdress->zipCode, 94500);
      $this->assertEquals($dataAdress->city, 'Champigny');
      $this->assertEquals($dataAdress->country, 'France');
      $this->assertEquals($dataAdress->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataAdress->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataCarto = $datas['Carto'][0];
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testGetDatasByQueryId()
    {
      $datas = $this->_datasService->getDatasByQueryId(1);

      $this->assertTrue(is_array($datas));
      $this->assertEquals(count($datas), 2);

      $dataAdress = $datas['Adress'][0];
      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 1);
      $this->assertEquals($dataAdress->adress, '8, rue de la Marne');
      $this->assertEquals($dataAdress->zipCode, 94500);
      $this->assertEquals($dataAdress->city, 'Champigny');
      $this->assertEquals($dataAdress->country, 'France');
      $this->assertEquals($dataAdress->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataAdress->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

      $dataCarto = $datas['Carto'][0];
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 1);
      $this->assertEquals($dataCarto->x, 1.89489e+06);
      $this->assertEquals($dataCarto->y, 7881);
      $this->assertEquals($dataCarto->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataCarto->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
    }

    public function testAddData()
    {
      $date = Zend_Date::now();
      $adressArray = array(
        'adress' => '75, avenue de Lattre de Tassigny',
        'zipCode' => 94100,
        'city' => 'Saint-Maur',
        'country' => 'France'
      );
      $adress = new Vo_Data_Adress($adressArray);
      $adress->id = $this->_datasService->addData($adress);
      $this->assertEquals($adress->id, 2);
      $dataAdress = $this->_datasService->getDatasById(2, 'Adress');
      $this->assertTrue($dataAdress instanceof Vo_Data_Adress);
      $this->assertEquals($dataAdress->id, 2);
      $this->assertEquals($dataAdress->adress, $adressArray['adress']);
      $this->assertEquals($dataAdress->zipCode, $adressArray['zipCode']);
      $this->assertEquals($dataAdress->city, $adressArray['city']);
      $this->assertEquals($dataAdress->country, $adressArray['country']);
      $this->assertEquals($dataAdress->addDate, $date);
      $this->assertEquals($dataAdress->setDate, $date);

      $date = Zend_Date::now();
      $cartoArray = array(
        'x' => 94500,
        'y' => 94100
      );
      $carto = new Vo_Data_Carto($cartoArray);
      $carto->id = $this->_datasService->addData($carto);
      $this->assertEquals($carto->id, 2);
      $dataCarto = $this->_datasService->getDatasById(2, 'Carto');
      $this->assertTrue($dataCarto instanceof Vo_Data_Carto);
      $this->assertEquals($dataCarto->id, 2);
      $this->assertEquals($dataCarto->x, $cartoArray['x']);
      $this->assertEquals($dataCarto->y, $cartoArray['y']);
      $this->assertEquals($dataCarto->addDate, $date);
      $this->assertEquals($dataCarto->setDate, $date);

      $date = Zend_Date::now();
      $voteArray = array(
        'rate' => -1,
        'users_id' => 1
      );
      $vote = new Vo_Data_Vote($voteArray);
      $vote->id = $this->_datasService->addData($vote);
      $this->assertEquals($vote->id, 2);
      $dataVote = $this->_datasService->getDatasById(2, 'Vote');
      $this->assertTrue($dataVote instanceof Vo_Data_Vote);
      $this->assertEquals($dataVote->id, 2);
      $this->assertEquals($dataVote->rate, $voteArray['rate']);
      $this->assertEquals($dataVote->addDate, $date);
      $this->assertEquals($dataVote->setDate, $date);
    }

    public function testSetData()
    {
      $dataAdress = $this->_datasService->getDatasById(1, 'Adress');
      $dataAdress->adress = 'bou';
      $date = Zend_Date::now();
      $this->_datasService->setData($dataAdress);
      $dataA = $this->_datasService->getDatasById(1, 'Adress');
      $this->assertTrue($dataA instanceof Vo_Data_Adress);
      $this->assertEquals($dataA->id, 1);
      $this->assertEquals($dataA->adress, 'bou');
      $this->assertEquals($dataA->zipCode, $dataAdress->zipCode);
      $this->assertEquals($dataA->city, $dataAdress->city);
      $this->assertEquals($dataA->country, $dataAdress->country);
      $this->assertEquals($dataA->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataA->setDate, $date);

      $dataCarto = $this->_datasService->getDatasById(1, 'Carto');
      $dataCarto->x = 94500;
      $date = Zend_Date::now();
      $this->_datasService->setData($dataCarto);
      $dataC = $this->_datasService->getDatasById(1, 'Carto');
      $this->assertTrue($dataC instanceof Vo_Data_Carto);
      $this->assertEquals($dataC->id, 1);
      $this->assertEquals($dataC->x, 94500);
      $this->assertEquals($dataC->y, $dataCarto->y);
      $this->assertEquals($dataC->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataC->setDate, $date);

      $dataVote = $this->_datasService->getDatasById(1, 'Vote');
      $dataVote->rate = -1;
      $date = Zend_Date::now();
      $this->_datasService->setData($dataVote);
      $dataV = $this->_datasService->getDatasById(1, 'Vote');
      $this->assertTrue($dataV instanceof Vo_Data_Vote);
      $this->assertEquals($dataV->id, 1);
      $this->assertEquals($dataV->rate, -1);
      $this->assertEquals($dataV->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
      $this->assertEquals($dataV->setDate, $date);
    }

    public function testDeleteData()
    {
      foreach($this->_datasTables as $type=>$dataTable)
      {
        $this->assertEquals($dataTable->fetchAll()->count(), 1);
        $this->_datasService->deleteData(1, $type);
        $this->assertEquals($dataTable->fetchAll()->count(), 0);
        $select = $this->_datasAssocTable->select();
        $select->where('datas_id = ?', 1)
            ->where('dataType = ?', $type);
        $rowset = $this->_datasAssocTable->fetchAll($select);
        $this->assertEquals($rowset->count(), 0);
      }

      $datas = $this->_datasService->getDatas();
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasById(1, 'Adress');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasById(1, 'Carto');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasById(1, 'Vote');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByCommentId(1);
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByItemId(1);
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByMediaId(1, 'Picture');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByMediaId(1, 'Sound');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByMediaId(1, 'Text');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByMediaId(1, 'Video');
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByQueryId(1);
      $this->assertEquals(count($datas), 0);

      $datas = $this->_datasService->getDatasByUserId(1);
      $this->assertEquals(count($datas), 0);
    }

    public function getTearDownOperation()
    {
      return $this->getOperations()->TRUNCATE();
    }

  }

