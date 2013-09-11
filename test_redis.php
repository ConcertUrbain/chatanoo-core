<?php
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	require "Library/predis/autoload.php";
	//PredisAutoloader::register();
	 
	// since we connect to default setting localhost
	// and 6379 port there is no need for extra
	// configuration. If not then you can specify the
	// scheme, host and port to connect as an array
	// to the constructor.
	try {
	    $redis = new Predis\Client();
	/*
	    $redis = new PredisClient(array(
	        "scheme" => "tcp",
	        "host" => "127.0.0.1",
	        "port" => 6379));
	*/
	    echo "Successfully connected to Redis\n\r";
	}
	catch (Exception $e) {
	    echo "Couldn't connected to Redis\n\r";
	    echo $e->getMessage();
	}

	$redis->set('foo', 'bar');
	$value = $redis->get('foo');
	echo 'Foo value: ' . $value;

?>