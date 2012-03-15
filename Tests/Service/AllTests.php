<?php

	if (!defined('PHPUnit_MAIN_METHOD')) {
	    define('PHPUnit_MAIN_METHOD', 'Service_AllTests::main');
	}

	/**
	 * @see Service_CommentsTest
	 */
	require_once 'Service/CommentsTest.php';

	/**
	 * @see Service_DatasTest
	 */
	require_once 'Service/DatasTest.php';

	/**
	 * @see Service_ItemsTest
	 */
	require_once 'Service/ItemsTest.php';

	/**
	 * @see Service_MediasTest
	 */
	require_once 'Service/MediasTest.php';

	/**
	 * @see Service_QueriesTest
	 */
	require_once 'Service/QueriesTest.php';

	/**
	 * @see Service_SearchTest
	 */
	require_once 'Service/SearchTest.php';

	/**
	 * @see Service_SessionsTest
	 */
	require_once 'Service/SessionsTest.php';

	/**
	 * @see Service_UsersTest
	 */
	require_once 'Service/UsersTest.php';

	class Service_AllTests
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
	        $suite = new PHPUnit_Framework_TestSuite('TourATour - Service');

	        $suite->addTestSuite('Service_CommentsTest');
	        $suite->addTestSuite('Service_DatasTest');
	        $suite->addTestSuite('Service_ItemsTest');
	        $suite->addTestSuite('Service_MediasTest');
	        $suite->addTestSuite('Service_QueriesTest');
	        $suite->addTestSuite('Service_SearchTest');
	        $suite->addTestSuite('Service_SessionsTest');
	        $suite->addTestSuite('Service_UsersTest');

	        return $suite;
	    }
	}

	if (PHPUnit_MAIN_METHOD == 'Service_AllTests::main') {
	    Service_AllTests::main();
	}