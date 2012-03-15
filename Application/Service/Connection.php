<?php

	/**
	 * Classes d'abstraction des services
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	class Service_Connection extends Service_Abstract
	{

	    /**
	     * Passerelles vers la table des utilisateurs
	     *
	     * @access protected
	     * @var Table_Users
	     */
	    protected $_usersTable = null;

	    /**
	     * Passerelles vers la table des utilisateurs
	     *
	     * @access protected
	     * @var Table_ApiKeys
	     */
	    protected $_apiKeysTable = null;
	    

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return mixed
	     */
	    public function __construct()
	    {
	        $this->_usersTable = new Table_Users();
	        $this->_apiKeysTable = new Table_ApiKeys();
	    }
	    
	    public function login($login, $pass, $apiKey)
	    {
	    	$select = $this->_usersTable->select();
	    	$select->where("pseudo = ?", $login)
	    			->where("password = ?", sha1($pass))
	    			->limit(1);
	    	if(!($row = $this->_usersTable->fetchRow($select)))
	    		return false;
	    		
	    	$user = Vo_Factory::factory(Vo_Factory::$USER_TYPE, $row);
	    		
	    	$select = $this->_apiKeysTable->select();
	    	$select->where("api_key = ?", $apiKey)
	    			->where("host = ?", Zend_Registry::get('host'))
	    			->limit(1);
	    	if(!($row = $this->_apiKeysTable->fetchRow($select)))
	    		return false;
	    		
			$str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$caracs = str_split($str);
			$sessionKey = "";
			while(strlen($sessionKey) < 32)
				$sessionKey .= $caracs[rand(0, count($caracs) - 1)];
	    	
	    	Zend_Registry::get('cache')->save(array(
	    		"apiKey" 	=> $apiKey,
	    		"host"		=> Zend_Registry::get('host'),
	    		"userID" 	=> $user->id,
	    		"sessionID" => $row->sessions_id
	    	), $sessionKey);
	    	return $sessionKey;
	    }
	    
	    public function logout()
	    {
	    	$sessionKey = Zend_Registry::get('sessionKey');
	    	Zend_Registry::get('cache')->remove($sessionKey);
	    	Zend_Registry::isRegistered('sessionKey');
	    }
	}