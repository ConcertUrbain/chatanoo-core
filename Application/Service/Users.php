<?php

	/**
	 * Permet d'interagir avec les users et la base de données
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */

	/**
	 * Interface de service ayant des datas
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface/Data.php');

	/**
	 * Interface de service ayant des métadonnées
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface/Meta.php');

	/**
	 * Classes d'abstraction des services
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Permet d'interagir avec les users et la base de données
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Users extends Service_Abstract implements Service_Interface_Data
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Passerelles vers la table des utilisateurs
	     *
	     * @access protected
	     * @var Table_Users
	     */
	    protected $_usersTable = null;

	    /**
	     * Passerelles vers la table de liaison des datas
	     *
	     * @access protected
	     * @var Table_Datas_Assoc
	     */
	    protected $_datasAssocTable = null;

	    /**
	     * Service des datas
	     *
	     * @access protected
	     * @var Service_Datas
	     */
	    protected $_datasService = null;

	    /**
	     * Passerelles vers la table de liaison des metas
	     *
	     * @access protected
	     * @var Table_MetasAssoc
	     */
	    protected $_metasAssocTable = null;

	    /**
	     * Service de recherche
	     *
	     * @access protected
	     * @var Service_Search
	     */
	    protected $_searchService = null;

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
	        $this->_metasAssocTable = new Table_MetasAssoc();
	        $this->_searchService = new Service_Search();
	        $this->_datasAssocTable = new Table_Datas_Assoc();
	        $this->_datasService = new Service_Datas();
	    }

	    /**
	     * Retourne toutes les utilisateurs contenus dans la base de données en
	     * de options
	     * Options:
	     *  - where -> array(array('cond', 'value'))
	     *  - order	-> string
	     *  - limit	-> array(count, offset)
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  array options Options pour le retour de la fonction
	     * @return array
	     */
	    public function getUsers($options = array())
	    {
	    	$users = array();
	    	$select = $this->_usersTable->select();
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	if(count($options))
	    	{
	    		foreach($options['where'] as $key=>$where)
	    			$select->where($where[0], $where[1]);
	    		if(isset($options['order']))
	    			$select->order($options['order']);
	    		if(isset($options['limit']))
	    			$select->limit($options['limit'][0], $options['limit'][1]);
	        	$usersRowset = $this->_usersTable->fetchAll($select);
	    	}
	        $usersRowset = $this->_usersTable->fetchAll($select);
			if($usersRowset->count())
	        	$users = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$USER_TYPE, $usersRowset);
	        return $users;
	    }

	    /**
	     * Retourne un utilisateur de la base de données en fonction de son
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @return Vo_User
	     */
	    public function getUserById($userId)
	    {
	    	$user = null;
	    	
	    	$select = $this->_usersTable->select();
	    	$select->where('id = ?', $userId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	
	        $userRow = $this->_usersTable->fetchRow($select);
	    	if(!is_null($userRow))
	        	$user = Vo_Factory::getInstance()->factory(Vo_Factory::$USER_TYPE, $userRow);
	        return $user;
	    }

	    /**
	     * Retourne un utilisateur de la base de données en fonction de son
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string pseudo pseudo d'un utilisateur
	     * @param  string pass mot de passe d'un utilisateur
	     * @return Vo_User
	     */
	    public function getUserByLogin($login, $pass)
	    {
	    	$user = null;
	    	
	    	$select = $this->_usersTable->select();
	    	$select->where("pseudo = ?", $login)
	    			->where("password = ?", sha1($pass))
	    			->limit(1);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));

	        $userRow = $this->_usersTable->fetchRow($select);
	    	if(!is_null($userRow))
	        	$user = Vo_Factory::getInstance()->factory(Vo_Factory::$USER_TYPE, $userRow);
	        return $user;
	    }


	    /**
	     * Ajoute un utilisateur à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_User user Un utilisateur
	     * @return int Identifiant du nouvel utilisateur
	     */
	    public function addUser( Vo_User $user)
	    {
	    	$user->password = sha1($user->password);

			$userRow = $this->_usersTable->createRow($user->toRowArray());
			$userRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$userRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$userRow->sessions_id = Zend_Registry::get('sessionID');
			return $userRow->save();
	    }

	    /**
	     * Modifie un utilisateur dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_User user Un utilisateur
	     * @return void
	     */
	    public function setUser( Vo_User $user)
	    {
	    	$select = $this->_usersTable->select();
	    	$select->where('id = ?', $user->id);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $userRow = $this->_usersTable->fetchRow($select);
	    	$userRowArray = $user->toRowArray();
			foreach($userRowArray as $key=>$value)
			{
				if($userRowArray[$key] != $userRow->$key && $key != "password")
					$userRow->$key = $value;
			}
			//$userRow->password = sha1($userRow->password);
			$userRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			return $userRow->save();
	    }
	
		/**
	     * Modifie le mot de passe de l'utilisateur dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId L'indentifiant d'un utilisateur
	     * @param  string password Le nouveau mot de passe de l'utilisateur
	     * @return void
	     */
	    public function setUserPassword($userId, $password)
	    {
	    	$select = $this->_usersTable->select();
	    	$select->where('id = ?', $userId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $userRow = $this->_usersTable->fetchRow($select);
			$userRow->password = sha1($password);
			$userRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			return $userRow->save();
	    }

	    /**
	     * Supprime un utilisateur de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @return void
	     */
	    public function deleteUser($userId)
	    {
			if($this->_usersTable->delete(array('id = ' . $userId, 'sessions_id = ' . Zend_Registry::get('sessionID'))))
				$this->_datasAssocTable->delete("assoc_id = " . $userId . " AND assocType = 'User'");
			return true;
	    }

	    /**
	     * Bannir ou débannir un utilisateur
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @param  bool trueOrFalse True pour bannir, false pour débannir
	     * @return void
	     */
	    public function banUser($userId, $trueOrFalse)
	    {
	    	$select = $this->_usersTable->select();
	    	$select->where('id = ?', $userId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $userRow = $this->_usersTable->fetchRow($select);
			$userRow->isBan = $trueOrFalse ? 1 : 0;
			return $userRow->save();
	    }

		/**
		 * Ajoute une métadonnées dans le Value Object
		 *
		 * @access public
		 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
		 * @param  Vo_Meta meta Un métadonnée
		 * @param  int voId Identifiant du Value Object
		 * @return int Identifiant de la nouvelle métadonnée
		 */
		public function addMetaIntoVo( Vo_Meta $meta, $voId)
		{
		    if(!$meta->id)
		    	$meta->id = $this->_searchService->addMeta($meta);
        
	    	$linkRow = $this->_metasAssocTable->createRow();
	    	$linkRow->metas_id = $meta->id;
	    	$linkRow->assoc_id = $voId;
	    	$linkRow->assocType = 'User';
	    	$linkRow->save();
	    	return $meta->id;
		}
        
		/**
		 * Retire une métadonnée du Value Object
		 *
		 * @access public
		 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
		 * @param  int metaId Identifiant d'une métadonnée
		 * @param  int voId Identifiant du Value Object
		 * @return void
		 */
		public function removeMetaFromVo($metaId, $voId)
		{
			$where = array(
				'metas_id = ' . $metaId,
				'assoc_id = ' . $voId,
				"assocType = 'User'"
			);
		    $this->_metasAssocTable->delete($where);
			return true;
		}

	    /**
	     * Ajoute une data dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Data_Abstract data Une data
	     * @param  int voId Identifiant du Value Object
	     * @return int Identifiant de la nouvelle data
	     */
	    public function addDataIntoVo( Vo_Data_Abstract $data, $voId)
	    {
			if(!$data->id)
				$data->id = $this->_datasService->addData($data);
			$linkRow = $this->_datasAssocTable->createRow();
			$linkRow->datas_id = $data->id;
			$linkRow->dataType = $data->getType();
			$linkRow->assoc_id = $voId;
			$linkRow->assocType = 'User';
			$linkRow->save();
			return $data->id;
	    }

	    /**
	     * Retire une data du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int dataId Identifaint d'une data
	     * @param  string dataType Type de la data
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function removeDataFromVo($dataId, $dataType, $voId)
	    {
			$this->_datasAssocTable->delete("datas_id = " . $dataId . " AND dataType = " . $this->_datasAssocTable->getAdapter()->quote($dataType) . " AND assoc_id = " . $voId . " AND assocType = 'User'");
			return true;
	    }

	} /* end of class Service_Users */