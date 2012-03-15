<?php

	require('PHPUnit/Framework/TestCase.php');

	set_include_path(dirname(__FILE__) . '/../../../Library' . PATH_SEPARATOR . dirname(__FILE__) . '/../../../Application' . PATH_SEPARATOR . get_include_path());

	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	class Vo_Media_FactoryTest extends PHPUnit_Framework_TestCase
	{

		public function setUp()
		{

		}

		public function testInstance()
		{
			$factory = Vo_Media_Factory::getInstance();
			$this->assertSame($factory, Vo_Media_Factory::getInstance());
		}

		public function testFactory()
		{
			$videoArray = array(
				'id' => 1,
				'_user'		=> 1,
				'title' => 'mon mŽdia',
				'description' => 'une description',
				'preview' => 'http://www.unsite.fr/preview.jpg',
				'addDate' => '2007.03.10 00:00:00',
				'setDate' => Zend_Date::now(),
				'_isValid' => true,
				'url' => 'http://www.unsite.fr/picture.jpg',
				'width' => 150.15,
				'height' => 156.78,
				'totalTime' => 180
			);
			$video = new Vo_Media_Video($videoArray);
			$videoByFactory = Vo_Media_Factory::getInstance()->factory(Vo_Media_Factory::$VIDEO_TYPE, $videoArray);

			$this->assertEquals($videoByFactory, $video);
		}

	}
