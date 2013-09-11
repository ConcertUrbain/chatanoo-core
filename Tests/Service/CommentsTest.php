<?php
	defined('APPLICATION_ENVIRONMENT')
	    or define('APPLICATION_ENVIRONMENT', 'test');

	require_once('PHPUnit/Extensions/Database/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Service_CommentsTest extends PHPUnit_Extensions_Database_TestCase
	{
		/**
		 * Table users
		 *
		 * @var Table_Comments
		 */
		private $_commentsTable;

		/**
		 * PDO connection
		 *
		 * @var PDO
		 */
		private $_pdo;

		/**
		 * Service Comments
		 *
		 * @var Service_Comments
		 */
		private $_commentsService;

		/**
		 * Service Datas
		 *
		 * @var Service_Datas
		 */
		private $_datasService;

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
			$this->_commentsTable = new Table_Comments();
			$this->_commentsService = new Service_Comments();
			$this->_datasService = new Service_Datas();
			$this->_usersService = new Service_Users();
		}

		public function testInitialisation()
		{
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testGetComments()
		{
			$comments = $this->_commentsService->getComments();

			$this->assertTrue(is_array($comments));
			$this->assertEquals(count($comments), 1);

			$comment = $comments[0];
			$this->assertTrue($comment instanceof Vo_Comment);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'Un commentaire');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testGetCommentById()
		{
			$comment = $this->_commentsService->getCommentById(1);
			$this->assertTrue($comment instanceof Vo_Comment);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'Un commentaire');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testGetCommentsByItemId()
		{
			$comments = $this->_commentsService->getCommentsByItemId(1);

			$this->assertTrue(is_array($comments));
			$this->assertEquals(count($comments), 1);

			$comment = $comments[0];
			$this->assertTrue($comment instanceof Vo_Comment);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'Un commentaire');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testAddComment()
		{
			$commentArray = array(
				'content' => 'Un commentaire 2',
				'isValid' => 1,
				'users_id' => 1,
				'items_id' => 1
			);
			//$this->_commentsService->addComment(new Vo_Comment($commentArray));
			// $comment = $this->_commentsService->getCommentById(2);
			// $this->assertEquals($comment->id, 2);
			// $this->assertEquals($comment->content, 'Un commentaire 2');
			// $this->assertEquals($comment->addDate, Zend_Date::now());
			// $this->assertEquals($comment->setDate, Zend_Date::now());
			// $this->assertTrue($comment->isValid());
		}

		public function testSetComment()
		{
			$comment = $this->_commentsService->getCommentById(1);
			$comment->content = 'Un commentaire mais modifie!';
			$this->_commentsService->setComment($comment);

			$settedComment = $this->_commentsService->getCommentById(1);
			$this->assertEquals($settedComment->id, $comment->id);
			$this->assertEquals($settedComment->content, $comment->content);
			$this->assertEquals($settedComment->addDate, $comment->addDate);
			$this->assertEquals($settedComment->setDate, Zend_Date::now());
			$this->assertEquals($settedComment->isValid(), $comment->isValid());
		}

		public function testDeleteComment()
		{
			$this->assertEquals($this->_commentsTable->fetchAll()->count(), 1);
			$this->_commentsService->deleteComment(1);
			$this->assertEquals($this->_commentsTable->fetchAll()->count(), 0);
			$comments = $this->_commentsService->getComments();
			$this->assertEquals(count($comments), 0);
			$comment = $this->_commentsService->getCommentById(1);
			$this->assertEquals(count($comments), 0);

			$datas = $this->_datasService->getDatasByCommentId(1);
			$this->assertEquals(count($datas), 0);
		}

		public function testGetUserOfVo()
		{
			$user = $this->_commentsService->getUserFromVo(1);

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
			$this->markTestSkipped(
              'testSetUserOfVo not tested.'
            );

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
			$this->_commentsService->setUserOfVo($u->id, 1);

			$user = $this->_commentsService->getUserFromVo(1);

			$this->assertTrue($user instanceof Vo_User);

			$this->assertEquals($user->id, 2);
			$this->assertEquals($user->firstName, 'Tsolova');
			$this->assertEquals($user->lastName, 'Irina');
			$this->assertEquals($user->pseudo, 'Irina');
			$this->assertEquals($user->password, 'irinator');
			$this->assertEquals($user->email, 'tsolova_irina@yahoo.com');
			$this->assertEquals($user->role, 'user');
		}

		public function testGetVosByUserId()
		{
			$comments = $this->_commentsService->getVosByUserId(1);

			$this->assertTrue(is_array($comments));
			$this->assertEquals(count($comments), 1);

			$comment = $comments[0];
			$this->assertTrue($comment instanceof Vo_Comment);

			$this->assertEquals($comment->id, 1);
			$this->assertEquals($comment->content, 'Un commentaire');

			$this->assertTrue($comment->addDate instanceof Zend_Date, "addDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->addDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));

			$this->assertTrue($comment->setDate instanceof Zend_Date, "setDate isn't instance of Zend_Date.");
			$this->assertEquals($comment->setDate, new Zend_Date('2009-04-15 23:55:36', 'YYYY.MM.dd HH:mm:ss'));
		}

		public function testValidateVo()
		{
			$comment = $this->_commentsService->getCommentById(1);
			$this->assertTrue($comment->isValid());

			$this->_commentsService->validateVo(1, false);
			$comment = $this->_commentsService->getCommentById(1);
			$this->assertFalse($comment->isValid());
			$insertXml = dirname(__FILE__) . '/Flats/comments-update2-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);

			$this->_commentsService->validateVo(1, true);
			$comment = $this->_commentsService->getCommentById(1);
			$this->assertTrue($comment->isValid());
			$this->assertDataSetsEqual(
				$this->getDataSet(),
				$this->getConnection()->createDataSet()
			);
		}

		public function testAddDataIntoVo()
		{
			$data = $this->_datasService->getDataById(1, 'Adress');
			$data->id = $this->_commentsService->addDataIntoVo($data, 1);

			$insertXml = dirname(__FILE__) . '/Flats/comments-insert2-data.xml';
			$this->assertDataSetsEqual(
				$this->createFlatXMLDataSet($insertXml),
				$this->getConnection()->createDataSet()
			);
		}

		public function testRemoveDataFromVo()
		{
			$this->_commentsService->removeDataFromVo(1, 'Carto', 1);
			$datas = $this->_datasService->getDatasByCommentId(1);
			$this->assertArrayNotHasKey('Carto', $datas);
		}

		public function getTearDownOperation()
		{
			return $this->getOperations()->TRUNCATE();
		}

	}

