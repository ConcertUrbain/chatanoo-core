<?php

  if (!defined('PHPUnit_MAIN_METHOD')) {
      define('PHPUnit_MAIN_METHOD', 'Vo_AllTests::main');
  }

  /**
   * @see Vo_CommentTest
   */
  require_once 'Vo/CommentTest.php';

  /**
   * @see Vo_Data_AdressTest
   */
  require_once 'Vo/Data/AdressTest.php';

  /**
   * @see Vo_Data_CartoTest
   */
  require_once 'Vo/Data/CartoTest.php';

  /**
   * @see Vo_Data_FactoryTest
   */
  require_once 'Vo/Data/FactoryTest.php';

  /**
   * @see Vo_Data_VoteTest
   */
  require_once 'Vo/Data/VoteTest.php';

  /**
   * @see Vo_Media_FactoryTest
   */
  require_once 'Vo/Media/FactoryTest.php';

  /**
   * @see Vo_Media_PictureTest
   */
  require_once 'Vo/Media/PictureTest.php';

  /**
   * @see Vo_Media_SoundTest
   */
  require_once 'Vo/Media/SoundTest.php';

  /**
   * @see Vo_Media_TextTest
   */
  require_once 'Vo/Media/TextTest.php';

  /**
   * @see Vo_Media_VideoTest
   */
  require_once 'Vo/Media/VideoTest.php';

  /**
   * @see Vo_FactoryTest
   */
  require_once 'Vo/FactoryTest.php';

  /**
   * @see Vo_ItemTest
   */
  require_once 'Vo/ItemTest.php';

  /**
   * @see Vo_MetaTest
   */
  require_once 'Vo/MetaTest.php';

  /**
   * @see Vo_QueryTest
   */
  require_once 'Vo/QueryTest.php';

  /**
   * @see Vo_SessionTest
   */
  require_once 'Vo/SessionTest.php';

  /**
   * @see Vo_UserTest
   */
  require_once 'Vo/UserTest.php';

  class Vo_AllTests
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
          $suite = new PHPUnit_Framework_TestSuite('TourATour - Vo');

          $suite->addTestSuite('Vo_CommentTest');
          $suite->addTestSuite('Vo_Data_AdressTest');
          $suite->addTestSuite('Vo_Data_CartoTest');
          $suite->addTestSuite('Vo_Data_FactoryTest');
          $suite->addTestSuite('Vo_Data_VoteTest');
          $suite->addTestSuite('Vo_Media_FactoryTest');
          $suite->addTestSuite('Vo_Media_PictureTest');
          $suite->addTestSuite('Vo_Media_SoundTest');
          $suite->addTestSuite('Vo_Media_TextTest');
          $suite->addTestSuite('Vo_Media_VideoTest');
          $suite->addTestSuite('Vo_FactoryTest');
          $suite->addTestSuite('Vo_ItemTest');
          $suite->addTestSuite('Vo_MetaTest');
          $suite->addTestSuite('Vo_QueryTest');
          $suite->addTestSuite('Vo_SessionTest');
          $suite->addTestSuite('Vo_UserTest');

          return $suite;
      }
  }

  if (PHPUnit_MAIN_METHOD == 'Vo_AllTests::main') {
      Vo_AllTests::main();
  }
