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
	    	$select = $this->_apiKeysTable->select();
	    	$select->where("api_key = ?", $apiKey)
	    			//->where("host LIKE ?", "%" . Zend_Registry::get('host') . "%") //BUG sur flash pas de http_origin
	    			->limit(1);
	    	if(!($row = $this->_apiKeysTable->fetchRow($select))) 
	    		return false;
	
			$select = $this->_usersTable->select();
	    	$select->where("pseudo = ?", $login)
	    			->where("password = ?", sha1($pass))
			    	->where("sessions_id = ?", $row->sessions_id)
	    			->limit(1);
	    	if(!($row = $this->_usersTable->fetchRow($select)))
	    		return false;
	    		
	    	$user = Vo_Factory::factory(Vo_Factory::$USER_TYPE, $row);
	    		
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

		public function getCurrentUser()
		{
			$userId = Zend_Registry::get('userID');
			
			$user = null;
	    	
	    	$select = $this->_usersTable->select();
	    	$select->where('id = ?', $userId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	
	        $userRow = $this->_usersTable->fetchRow($select);
	    	if(!is_null($userRow))
	        	$user = Vo_Factory::getInstance()->factory(Vo_Factory::$USER_TYPE, $userRow);
	        return $user;
		}
	    
	    public function logout()
	    {
	    	$sessionKey = Zend_Registry::get('sessionKey');
	    	Zend_Registry::get('cache')->remove($sessionKey);
	    	Zend_Registry::isRegistered('sessionKey');
	    }
	}