<?php
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	$memcache = new Memcache;
	$memcache->connect('localhost', 11211) or die ("Connexion impossible");
	
	$version = $memcache->getVersion();
	echo "Version du serveur : ".$version."<br/>\n";
	
	$tmp_object = new stdClass;
	$tmp_object->str_attr = 'test';
	$tmp_object->int_attr = 123;
	
	$memcache->set('key', $tmp_object, false, 10) or die ("Echec de la sauvegarde des données sur le serveur");
	echo "Les données ont été stockées dans le cache (les données expireront dans 10 secondes)<br/>\n";
	
	$get_result = $memcache->get('key');
	echo "Données depuis le cache :<br/>\n";
	
	var_dump($get_result);
	var_dump(sha1('desperados'));

?>	