<?php

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Vo_CommentTest extends PHPUnit_Extensions_Database_TestCase
	{

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Table Comments
		 *
		 * @var Table_Comments
		 */
		private $_commentsTable;

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
			$this->_commentsTable = new Table_Comments();
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
			$commentArray = array(
				'id'		=> 1,
				'item'		=> 1,
				'_user'		=> 1,
				'content'	=> 'content',
				'addDate'	=> '2007.03.10 00:00:00',
				'setDate'	=> Zend_Date::now(),
				'_isValid'	=> true
			);

			$comment = new Vo_Comment($commentArray);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'content');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, new Zend_Date($commentArray['addDate'], 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, new Zend_Date($commentArray['setDate'], 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testConstructObject()
		{
			$commentArray = array(
				'id'		=> 1,
				'_item'		=> 1,
				'_user'		=> 1,
				'content'	=> 'content',
				'addDate'	=> '2007.03.10 00:00:00',
				'setDate'	=> Zend_Date::now(),
				'_isValid'	=> true
			);
			$commentObject = new Vo_Comment($commentArray);

			$comment = new Vo_Comment($commentObject);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'content');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, $commentObject->addDate);

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, $commentObject->setDate);
		}

		public function testConstructZendDbTableRow()
		{
			$commentRow = $this->_commentsTable->find(1)->current();
			$comment = new Vo_Comment($commentRow);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'Un commentaire');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, new Zend_Date($commentRow->addDate, 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, new Zend_Date($commentRow->setDate, 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testException()
		{
			$commentArray = array(
				'id'		=> 1,
				'_item'		=> 1,
				'_user'		=> 1,
				'content'	=> 'content',
				'addDate'	=> '2007.03.10 00:00:00',
				'setDate'	=> Zend_Date::now(),
				'_isValid'	=> true
			);
			$comment = new Vo_Comment($commentArray);

			try {
				$comment->bou = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 1);
			}

			try {
				$bou = $comment->bou;
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 2);
			}

			try {
				$commentString = new Vo_Comment('bou');
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 3);
			}

			try {
				$comment->addDate = 'bou';
			}
			catch(Vo_Exception $e)
			{
				$this->assertEquals($e->getCode(), 4);
			}
		}

		public function testToArray()
		{
			$commentArray = array(
				'id'		=> 1,
				'_item'		=> 1,
				'_user'		=> 1,
				'content'	=> 'content',
				'addDate'	=> '2007.03.10 00:00:00',
				'setDate'	=> '2007.03.10 00:00:00',
				'_isValid'	=> true
			);
			$comment = new Vo_Comment($commentArray);

			$this->assertEquals($comment->toArray(), $commentArray);
		}

		public function testGetType()
		{
			$commentArray = array(
				'id'		=> 1,
				'_item'		=> 1,
				'_user'		=> 1,
				'content'	=> 'content',
				'addDate'	=> '2007.03.10 00:00:00',
				'setDate'	=> Zend_Date::now(),
				'_isValid'	=> true
			);
			$comment = new Vo_Comment($commentArray);

			$this->assertEquals($comment->getType(), 'Comment');
		}

		public function testValidate()
		{
			$commentArray = array(
				'id'		=> 1,
				'_item'		=> 1,
				'_user'		=> 1,
				'content'	=> 'content',
				'addDate'	=> '2007.03.10 00:00:00',
				'setDate'	=> Zend_Date::now(),
				'_isValid'	=> true
			);
			$comment = new Vo_Comment($commentArray);

			$this->assertTrue($comment->isValid());
			$comment->validate(false);
			$this->assertFalse($comment->isValid());
			$comment->validate(true);
			$this->assertTrue($comment->isValid());
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}
