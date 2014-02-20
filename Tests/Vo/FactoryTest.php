<?php

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

	class Vo_FactoryTest extends PHPUnit_Framework_TestCase
	{

		public function setUp()
		{

		}

		public function testInstance()
		{
			$factory = Vo_Factory::getInstance();
			$this->assertSame($factory, Vo_Factory::getInstance());
		}

		public function testFactory()
		{
			$userArray = array(
				'id' => 1,
				'firstName' => 'DesvŽ',
				'lastName' => 'Mathieu',
				'pseudo' => 'mŠzrtŽ',
				//'password' => 'desperados',
				'email' => 'mathieu.desve@unflux.fr',
				'role' => 'admin',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => '2007.03.10 00:00:00',
				'_isBan' => true
			);

			$user = new Vo_User($userArray);
			$userByFactory = Vo_factory::getInstance()->factory(Vo_Factory::$USER_TYPE, $userArray);

			$this->assertEquals($userByFactory, $user);
		}

	}
