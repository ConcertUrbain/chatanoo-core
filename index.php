<?php

  // Define path to application directory
  defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/Application'));

  // Define application environment
  defined('APPLICATION_ENV') || define('APPLICATION_ENV', getenv('APPLICATION_ENV'));

  // Define environment vars
  defined('DATABASE_HOST') || define('DATABASE_HOST', getenv('DATABASE_HOST'));
  defined('DATABASE_USER') || define('DATABASE_USER', getenv('DATABASE_USER'));
  defined('DATABASE_PASS') || define('DATABASE_PASS', getenv('DATABASE_PASS'));
  defined('DATABASE_NAME') || define('DATABASE_NAME', getenv('DATABASE_NAME'));

  defined('MEMCACHED_HOST') || define('MEMCACHED_HOST', getenv('MEMCACHED_HOST'));
  defined('MEMCACHED_PORT') || define('MEMCACHED_PORT', getenv('MEMCACHED_PORT'));
  
  defined('NOTIFY_TOPIC') || define('NOTIFY_TOPIC', getenv('NOTIFY_TOPIC'));

  // Typically, you will also want to add your library/ directory
  // to the include_path, particularly if it contains your ZF install
  set_include_path(implode(PATH_SEPARATOR, array(
      APPLICATION_PATH . '/../Library',
      APPLICATION_PATH . '/core',
      get_include_path(),
  )));
  require 'vendor/autoload.php';

  /** Zend_Application */
  require_once 'Zend/Application.php';

  // Create application, bootstrap, and run
  $application = new Zend_Application(
      APPLICATION_ENV,
      APPLICATION_PATH . '/etc/init.xml'
  );
  $application->bootstrap()
        ->run();
