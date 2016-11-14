<?php

  // Define environment vars
  defined('DATABASE_HOST') || define('DATABASE_HOST', getenv('DATABASE_HOST'));
  defined('DATABASE_USER') || define('DATABASE_USER', getenv('DATABASE_USER'));
  defined('DATABASE_PASS') || define('DATABASE_PASS', getenv('DATABASE_PASS'));
  defined('DATABASE_NAME') || define('DATABASE_NAME', getenv('DATABASE_NAME'));

  defined('MEMCACHED_HOST') || define('MEMCACHED_HOST', getenv('MEMCACHED_HOST'));
  defined('MEMCACHED_PORT') || define('MEMCACHED_PORT', getenv('MEMCACHED_PORT'));

  defined('NOTIFY_TOPIC') || define('NOTIFY_TOPIC', getenv('NOTIFY_TOPIC'));
  defined('LOGS_STREAM') || define('LOGS_STREAM', getenv('LOGS_STREAM'));

  set_include_path(implode(PATH_SEPARATOR, array(
      dirname(__FILE__) . '/../Library',
      dirname(__FILE__) . '/core',
      dirname(__FILE__),
      get_include_path(),
  )));
  require 'vendor/autoload.php';

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
