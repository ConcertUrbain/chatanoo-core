<?php

  set_include_path(implode(PATH_SEPARATOR, array(
      dirname(__FILE__) . '/../../../Library',
      dirname(__FILE__) . '/../../../Application',
      dirname(__FILE__) . '/../../core',
      dirname(__FILE__),
      get_include_path(),
  )));
  require 'vendor/autoload.php';

  $autoloader = Zend_Loader_Autoloader::getInstance();
  $autoloader->setFallbackAutoloader(true);

  class Vo_Data_FactoryTest extends PHPUnit_Framework_TestCase
  {

    public function setUp()
    {

    }

    public function testInstance()
    {
      $factory = Vo_Data_Factory::getInstance();
      $this->assertSame($factory, Vo_Data_Factory::getInstance());
    }

    public function testFactory()
    {
      $adressArray = array(
        'id' => 1,
        'addDate' => '2007.03.10 00:00:00',
        'setDate' => Zend_Date::now(),
        'adress' => '8, rue de la Marne',
        'zipCode' => 94500,
        'city' => 'Champigny',
        'country' => 'France'
      );
      $adress = new Vo_Data_Adress($adressArray);
      $adressByFactory = Vo_Data_Factory::getInstance()->factory(Vo_Data_Factory::$ADRESS_TYPE, $adressArray);

      $this->assertEquals($adressByFactory, $adress);
    }

  }
