<?php

	/**
	 * Permet d'interagir avec les sessions et la base de données
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */

	/**
	 * Interface de service ayant des métadonnées
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface/Meta.php');

	/**
	 * Interface de service ayant des auteurs
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface/User.php');

	/**
	 * Classes d'abstraction des services
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '//Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Permet d'interagir avec les sessions et la base de données
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Sessions extends Service_Abstract implements 	Service_Interface_User,
	                  											Service_Interface_Meta
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Passerelles vers la table des sessions
	     *
	     * @access protected
	     * @var Table_Sessions
	     */
	    protected $_sessionsTable = null;

	    /**
	     * Passerelles vers la table des sessions
	     *
	     * @access protected
	     * @var Table_Queries
	     */
	    protected $_queriesTable = null;

	    /**
	     * Passerelles vers la table de liaison entre les sessions et les questions
	     *
	     * @access protected
	     * @var Table_SessionsAssocQueries
	     */
	    protected $_sessionsAssocQueriesTable = null;

	    /**
	     * Passerelles vers la table de liaison des datas
	     *
	     * @access protected
	     * @var Table_Datas_Assoc
	     */
	    protected $_datasAssocTable = null;

	    /**
	     * Passerelles vers la table de liaison des metas
	     *
	     * @access protected
	     * @var Table_MetasAssoc
	     */
	    protected $_metasAssocTable = null;

	    /**
	     * Service des questions
	     *
	     * @access protected
	     * @var Service_Queries
	     */
	    protected $_queriesService = null;

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
	        $this->_sessionsTable = new Table_Sessions();
	        $this->_queriesTable = new Table_Queries();
	        $this->_sessionsAssocQueriesTable = new Table_SessionsAssocQueries();
	        $this->_queriesService = new Service_Queries();
	        $this->_searchService = new Service_Search();
	        $this->_metasAssocTable = new Table_MetasAssoc();
	    }

	    /**
	     * Retourne toutes les sessions contenus dans la base de données en fonction
	     * options
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
	    public function getSessions($options = array())
	    {
	    	$sessions = array();
	    	$select = $this->_sessionsTable->select();
	    	$select->where('id = ?', Zend_Registry::get('sessionID'));
	    	if(count($options))
	    	{
	    		foreach($options['where'] as $key=>$where)
	    			$select->where($where[0], $where[1]);
	    		if(isset($options['order']))
	    			$select->order($options['order']);
	    		if(isset($options['limit']))
	    			$select->limit($options['limit'][0], $options['limit'][1]);
	        	$sessionsRowset = $this->_sessionsTable->fetchAll($select);
	    	}
	        $sessionsRowset = $this->_sessionsTable->fetchAll($select);
			if($sessionsRowset->count())
	        	$sessions = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$SESSION_TYPE, $sessionsRowset);
	        return $sessions;
	    }

	    /**
	     * Retourne une session de la base de données en fonction de son identifiant
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int sessionId Identifiant d'une session
	     * @return Vo_Session
	     */
	    public function getSessionById($sessionId)
	    {
	    	if($sessionId != Zend_Registry::get('sessionID'))
	    		return;
	    	
	    	$session = null;	    	
	        $sessionRow = $this->_sessionsTable->find($sessionId)->current();
	    	if(!is_null($sessionRow))
	        	$session = Vo_Factory::getInstance()->factory(Vo_Factory::$SESSION_TYPE, $sessionRow);
	        return $session;
	    }

	    /**
	     * Retourne toutes les sessions contenant une question
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int queryId Identifiant d'une question
	     * @return array
	     */
	    public function getSessionsByQueryId($queryId)
	    {
	    	$sessions = array();
	    	$queryRow = $this->_queriesTable->find($queryId)->current();
	    	if($queryRow)
	    	{
				$sessionsRowset = $queryRow->findManyToManyRowset('Table_Sessions', 'Table_SessionsAssocQueries');
				if($sessionsRowset->count())
		        	$sessions = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$SESSION_TYPE, $sessionsRowset);
	    	}
	        return $sessions;
	    }

	    /**
	     * Ajoute une session à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Session session Une session
	     * @return int Identifiant de la nouvelle session
	     */
	    public function addSession( Vo_Session $session)
	    {
			$sessionRow = $this->_sessionsTable->createRow($session->toRowArray());
			$sessionRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$sessionRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			return $sessionRow->save();
	    }

	    /**
	     * Modifie une session dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Session session Une session
	     * @return void
	     */
	    public function setSession( Vo_Session $session)
	    {
	    	if($session->id != Zend_Registry::get('sessionID'))
	    		return;
	    	
			$sessionRow = $this->_sessionsTable->find($session->id)->current();
	    	$sessionRowArray = $session->toRowArray();
			foreach($sessionRowArray as $key=>$value)
			{
				if($sessionRowArray[$key] != $sessionRow->$key)
					$sessionRow->$key = $value;
			}
			$sessionRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			return $sessionRow->save();
	    }

	    /**
	     * Supprime une session de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int sessionId Identifiant d'une session
	     * @return void
	     */
	    public function deleteSession($sessionId)
	    {
	    	if($sessionId != Zend_Registry::get('sessionID'))
	    		return;
	    		
			$this->_sessionsTable->delete('id = ' . $sessionId);
			$this->_sessionsAssocQueriesTable->delete('sessions_id = ' . $sessionId);
			$where = array(
				"assoc_id = " . $sessionId,
				"assocType = 'Session'"
			);
			$this->_metasAssocTable->delete($where);
			return true;
	    }

	    /**
	     * Ajoute une question à une session dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Query query Une question
	     * @param  int sessionId Identifiant d'une session
	     * @return int Identifiant de la nouvelle question
	     */
	    public function addQueryIntoSession( Vo_Query $query, $sessionId)
	    {
	    	if($sessionId != Zend_Registry::get('sessionID'))
	    		return;
	    		
	    	if(!$query->id)
	    		$query->id = $this->_queriesService->addQuery($query);

    		/*$linkRow = $this->_sessionsAssocQueriesTable->createRow();
    		$linkRow->sessions_id = $sessionId;
    		$linkRow->queries_id = $query->id;
    		$linkRow->save();*/
    		return $query->id;
	    }

	    /**
	     * Retire une question à une session dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int queryId Identifiant d'une question
	     * @param  int sessionId Identifiant d'une session
	     * @return void
	     */
	    public function removeQueryFromSession($queryId, $sessionId)
	    {
	    	if($sessionId != Zend_Registry::get('sessionID'))
	    		return;
	    		
			$where = array(
				"sessions_id = " . $sessionId,
				"queries_id = " . $queryId
			);
	        $this->_sessionsAssocQueriesTable->delete($where);
			return true;
	    }

	    /**
	     * Ajoute un utilisateur dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function getUserFromVo($voId)
	    {
	    	if($voId != Zend_Registry::get('sessionID'))
	    		return;
	    		
			$sessionRow = $this->_sessionsTable->find($voId)->current();
			$userRow = $sessionRow->findParentTable_Users();
			$user = new Vo_User($userRow);
			return $user;
	    }

	    /**
	     * Retire un utilisateur du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function setUserOfVo($userId, $voId)
	    {
	    	if($voId != Zend_Registry::get('sessionID'))
	    		return;
	    		
			$sessionRow = $this->_sessionsTable->find($voId)->current();
			$sessionRow->users_id = $userId;
			return $sessionRow->save();
	    }

	    /**
	     * Retourne tous les Values Objects du type du service ayant pour
	     * celui précisé
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @return array
	     */
	    public function getVosByUserId($userId)
	    {
	    	$select = $this->_sessionsTable->select();
	    	$select->where('users_id = ?', $userId);
	    	$select->where('id = ?', Zend_Registry::get('sessionID'));

	    	$sessions = array();
			$sessionsRowset = $this->_sessionsTable->fetchAll($select);
	        $sessions = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$SESSION_TYPE, $sessionsRowset);
	        return $sessions;
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
	    	if($voId != Zend_Registry::get('sessionID'))
	    		return;
	    		
	        if(!$meta->id)
	        	$meta->id = $this->_searchService->addMeta($meta);

        	$linkRow = $this->_metasAssocTable->createRow();
        	$linkRow->metas_id = $meta->id;
        	$linkRow->assoc_id = $voId;
        	$linkRow->assocType = 'Session';
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
	    	if($voId != Zend_Registry::get('sessionID'))
	    		return;
	    		
	    	$where = array(
	    		'metas_id = ' . $metaId,
	    		'assoc_id = ' . $voId,
	    		"assocType = 'Session'"
	    	);
	        $this->_metasAssocTable->delete($where);
			return true;
	    }

	} /* end of class Service_Sessions */