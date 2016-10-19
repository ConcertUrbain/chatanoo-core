<?php

  class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
  {
    protected function _initAccessControl()
    {
      header("Access-Control-Allow-Origin: *");
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      header('Access-Control-Allow-Headers: *');
    }

    protected function _initAutoload()
    {
      $autoloader = Zend_Loader_Autoloader::getInstance();
      $autoloader->setFallbackAutoloader(true);
    }

    protected function _initConfig()
    {
      $config = new Zend_Config_Xml(APPLICATION_PATH.'/etc/config.xml', APPLICATION_ENV);
      Zend_Registry::set('config', $config);
    }

    protected function _initDb()
    {
      $dbAdapter = Zend_Db::factory(Zend_Registry::get('config')->database);
      Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
      Zend_Registry::set('db', $dbAdapter);
    }

    protected function _initCache()
    {
      $cache = Zend_Cache::factory(
        Zend_Registry::get('config')->cache->frontend->adapter,
        Zend_Registry::get('config')->cache->backend->adapter,
        Zend_Registry::get('config')->cache->frontend->options->toArray(),
        Zend_Registry::get('config')->cache->backend->options->toArray()
      );
      Zend_Registry::set('cache', $cache);
    }

    protected function _initLog()
    {
      /*if(APPLICATION_ENV == 'development')
      {
        $writer = new Zend_Log_Writer_Firebug();
      }
      else
      {*/
        //$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/Logs/log.txt');
      //}
      //$logger = new Zend_Log($writer);
      //Zend_Registry::set('logger', $logger);

      //$logger->info('Log engine start');

      /*include 'FirePHPCore/fb.php';
      FB::info("test");*/
    }
  }
