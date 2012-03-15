<?php

	set_include_path(dirname(__FILE__) . '/../Library' . PATH_SEPARATOR . dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);


	if (!defined('PHPUnit_MAIN_METHOD')) {
	    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
	}

	/**
	 * @see Vo_AllTests
	 */
	require_once 'Vo/AllTests.php';

	/**
	 * @see Table_AllTests
	 */
	require_once 'Table/AllTests.php';

	/**
	 * @see Service_AllTests
	 */
	require_once 'Service/AllTests.php';

	class AllTests
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
	        $suite = new PHPUnit_Framework_TestSuite('TourATour');

	        $suite->addTestSuite(Vo_AllTests::suite());
	        $suite->addTestSuite(Table_AllTests::suite());
	        $suite->addTestSuite(Service_AllTests::suite());

	        return $suite;
	    }
	}

	if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
	    Vo_AllTests::main();
	}