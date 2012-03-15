<?php

	if (!defined('PHPUnit_MAIN_METHOD')) {
	    define('PHPUnit_MAIN_METHOD', 'Vo_AllTests::main');
	}

	/**
	 * @see Table_CommentsTest
	 */
	require_once 'Table/CommentsTest.php';

	/**
	 * @see Table_Datas_AdressTest
	 */
	require_once 'Table/Datas/AdressTest.php';

	/**
	 * @see Table_Datas_CartoTest
	 */
	require_once 'Table/Datas/CartoTest.php';

	/**
	 * @see Table_Datas_AssocTest
	 */
	require_once 'Table/Datas/AssocTest.php';

	/**
	 * @see Table_Datas_VoteTest
	 */
	require_once 'Table/Datas/VoteTest.php';

	/**
	 * @see Table_Medias_AssocTest
	 */
	require_once 'Table/Medias/AssocTest.php';

	/**
	 * @see Table_Medias_PictureTest
	 */
	require_once 'Table/Medias/PictureTest.php';

	/**
	 * @see Table/_Medias_SoundTest
	 */
	require_once 'Table/Medias/SoundTest.php';

	/**
	 * @see Table_Medias_TextTest
	 */
	require_once 'Table/Medias/TextTest.php';

	/**
	 * @see Table_Medias_VideoTest
	 */
	require_once 'Table/Medias/VideoTest.php';

	/**
	 * @see Table_ItemsTest
	 */
	require_once 'Table/ItemsTest.php';

	/**
	 * @see Table_MetasTest
	 */
	require_once 'Table/MetasTest.php';

	/**
	 * @see Table_QueriesTest
	 */
	require_once 'Table/QueriesTest.php';

	/**
	 * @see Table_SessionsTest
	 */
	require_once 'Table/SessionsTest.php';

	/**
	 * @see Table_UsersTest
	 */
	require_once 'Table/UsersTest.php';

	/**
	 * @see Table_MetasAssocTest
	 */
	require_once 'Table/MetasAssocTest.php';

	/**
	 * @see Table_QueriesAssocItemsTest
	 */
	require_once 'Table/QueriesAssocItemsTest.php';

	/**
	 * @see Table_SessionsAssocQueriesTest
	 */
	require_once 'Table/SessionsAssocQueriesTest.php';

	class Table_AllTests
	{
	    /**
	     * Runs this test suite
	     *
	     * @return void
	     */
	    public static function main()
	    {
	        PHPUnit_TextUI_TestRunner::run(self::suite());
	    }

	    /**
	     * Creates and returns this test suite
	     *
	     * @return PHPUnit_Framework_TestSuite
	     */
	    public static function suite()
	    {
			if(!Zend_Registry::isRegistered('db'))
			{
				$config = new Zend_Config_Xml(dirname(__FILE__) . '/../../Application/etc/config.xml', 'test');
				Zend_Registry::set('config', $config);

				$db = Zend_Db::factory($config->database);
				Zend_Registry::set('db', $db);

				Zend_Db_Table_Abstract::setDefaultAdapter($db);
			}

	        $suite = new PHPUnit_Framework_TestSuite('TourATour - Table');

	        $suite->addTestSuite('Table_CommentsTest');
	        $suite->addTestSuite('Table_Datas_AdressTest');
	        $suite->addTestSuite('Table_Datas_CartoTest');
	        $suite->addTestSuite('Table_Datas_AssocTest');
	        $suite->addTestSuite('Table_Datas_VoteTest');
	        $suite->addTestSuite('Table_Medias_AssocTest');
	        $suite->addTestSuite('Table_Medias_PictureTest');
	        $suite->addTestSuite('Table_Medias_SoundTest');
	        $suite->addTestSuite('Table_Medias_TextTest');
	        $suite->addTestSuite('Table_Medias_VideoTest');
	        $suite->addTestSuite('Table_ItemsTest');
	        $suite->addTestSuite('Table_MetasTest');
	        $suite->addTestSuite('Table_QueriesTest');
	        $suite->addTestSuite('Table_SessionsTest');
	        $suite->addTestSuite('Table_UsersTest');
	        $suite->addTestSuite('Table_MetasAssocTest');
	        $suite->addTestSuite('Table_QueriesAssocItemsTest');
	        $suite->addTestSuite('Table_SessionsAssocQueriesTest');

	        return $suite;
	    }
	}

	if (PHPUnit_MAIN_METHOD == 'Table_AllTests::main') {
	    Vo_AllTests::main();
	}
