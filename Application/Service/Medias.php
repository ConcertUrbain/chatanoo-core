<?php

	/**
	 * Permet d'interagir avec les médias et la base de données
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
	require_once(dirname(__FILE__) . '/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Permet d'interagir avec les médias et la base de données
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Medias extends Service_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Passerelles vers la table de liaison des médias
	     *
	     * @access protected
	     * @var Assoc
	     */
	    protected $_mediasAssocTable = null;

	    /**
	     * Tableau contenant les passerelles vers les différentes tables de médias
	     *
	     * @access protected
	     * @var array
	     */
	    protected $_mediasTables = array();

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
	     * @var Service_Data
	     */
	    protected $_datasService = null;

	    /**
	     * Service de recherche
	     *
	     * @access protected
	     * @var Service_Search
	     */
	    protected $_searchService = null;

	    /**
	     * Passerelles vers la table de liaison des metas
	     *
	     * @access protected
	     * @var Table_MetasAssoc
	     */
	    protected $_metasAssocTable = null;

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
			if(Zend_Registry::isRegistered('config'))
			{
				$this->_config = Zend_Registry::get('config');
			}
			else
			{
				$this->_config = new Zend_Config_Xml(dirname(__FILE__) . '/../etc/config.xml', APPLICATION_ENVIRONMENT);
				Zend_Registry::set('config', $this->_config);
			}

			foreach($this->_config->medias->media as $media)
			{
				$className = $media->tableClass;
				$this->_mediasTables[$media->type] = new $className;
			}
			$this->_mediasAssocTable = new Table_Medias_Assoc();
			$this->_datasAssocTable = new Table_Datas_Assoc();
			$this->_datasService = new Service_Datas();
	        $this->_searchService = new Service_Search();
	        $this->_metasAssocTable = new Table_MetasAssoc();
	    }

	    /**
	     * Retourne toutes les médias contenus dans la base de données en fonction
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
	    public function getMedias($options = array())
	    {
	        $medias = array();
			foreach($this->_mediasTables as $mediaTable)
			{
		    	$select = $mediaTable->select();
		    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	if(count($options))
		    	{
		    		foreach($options['where'] as $key=>$where)
		    			$select->where($where[0], $where[1]);
		    		if(isset($options['order']))
		    			$select->order($options['order']);
		    		if(isset($options['limit']))
		    			$select->limit($options['limit'][0], $options['limit'][1]);
						$mediasRowset = $mediaTable->fetchAll($select);
		    	}
				$mediasRowset = $mediaTable->fetchAll($select);
				if($mediasRowset->count())
					$medias[$mediaTable->getMediaType()] = Vo_Media_Factory::getInstance()->rowsetToVoArray($mediaTable->getMediaVoClass(), $mediasRowset);
			}
	        return (array) $medias;
	    }

	    /**
	     * Retourne un média de la base de données en fonction de son identifiant
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId Identifiant d'un média
	     * @param  string mediaType Type du média
	     * @return Vo_Media_Abstract
	     */
	    public function getMediaById($mediaId, $mediaType)
	    {
	    	$media = null;
	    	
		    $select = $this->_mediasTables[$mediaType]->select();
		    $select->where('id = ?', $mediaId);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$mediaRow = $this->_mediasTables[$mediaType]->fetchRow($select);
	    	if(!is_null($mediaRow) && $mediaRow)
	    		$media =  Vo_Media_Factory::getInstance()->factory($mediaType, $mediaRow);
	        return $media;
	    }

	    private function _getMediaByVo($voId, $voType)
	    {
	        $medias = array();
			foreach($this->_mediasTables as $mediaTable)
			{
	    		$select = Zend_Registry::get('db')->select();
	    		$table = $mediaTable->getTableName();
				$select->from('medias_assoc', null)
						->join($table, 'medias_assoc.medias_id = '.$table.'.id')
						->where('medias_assoc.mediaType = ?', $mediaTable->getMediaType())
						->where('medias_assoc.assoc_id = ?', $voId)
						->where("medias_assoc.assocType = ?", $voType)
						->where($table . ".sessions_id = ?", Zend_Registry::get('sessionID'));
				$mediasRows = Zend_Registry::get('db')->fetchAll($select);
				if(count($mediasRows))
					$medias[$mediaTable->getMediaType()] = Vo_Media_Factory::getInstance()->rowsToVoArray($mediaTable->getMediaVoClass(), $mediasRows);
			}
	        return (array) $medias;
	    }

	    /**
	     * Retourne tous les média contenus dans un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int itemId Identifiant d'un item
	     * @return array
	     */
	    public function getMediasByItemId($itemId)
	    {
	        return $this->_getMediaByVo($itemId, 'Item');
	    }

	    /**
	     * Retourne tous les média contenus dans une question
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int queryId Identifiant d'une question
	     * @return array
	     */
	    public function getMediasByQueryId($queryId)
	    {
	        return $this->_getMediaByVo($queryId, 'Query');
	    }

	    /**
	     * Ajoute un média à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Media_Abstract media Un média
	     * @return int Identifiant du nouveau média
	     */
	    public function addMedia( Vo_Media_Abstract &$media)
	    {
	    	$mediaRow = $this->_mediasTables[$media->getType()]->createRow($media->toRowArray());
	    	$mediaRow->id = null;
			$mediaRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$mediaRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$mediaRow->sessions_id = Zend_Registry::get('sessionID');
			$mediaRow->users_id = Zend_Registry::get('userID');
			return $mediaRow->save();
	    }

	    /**
	     * Modifie un média dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Media_Abstract media Un média
	     * @return void
	     */
	    public function setMedia( Vo_Media_Abstract $media)
	    {
		    $select = $this->_mediasTables[$media->getType()]->select();
		    $select->where('id = ?', $media->id);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$mediaRow = $this->_mediasTables[$media->getType()]->fetchRow($select);
	    	$mediaRowArray = $media->toRowArray();
			foreach($mediaRowArray as $key=>$value)
			{
				if($mediaRowArray[$key] != $mediaRow->$key)
					$mediaRow->$key = $value;
			}
			$mediaRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			return $mediaRow->save();
	    }

	    /**
	     * Supprime un média de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId Identifiant d'un média
	     * @param  string mediaType Type du média
	     * @return void
	     */
	    public function deleteMedia($mediaId, $mediaType)
	    {
			if($this->_mediasTables[$mediaType]->delete(array('id = ' . $mediaId, 'sessions_id = ' . Zend_Registry::get('sessionID'))))
			{
				$where = array(
					"medias_id = " . $mediaId,
					"mediaType = '" . $mediaType . "'"
				);
				$this->_mediasAssocTable->delete($where);
				$where = array(
					"assoc_id = " . $mediaId,
					"assocType = 'Media_" . $mediaType . "'"
				);
				$this->_datasAssocTable->delete($where);
			}
			return true;
	    }

	    /**
	     * Ajoute un utilisateur au média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @return void
	     */
	    public function getUserFromMedia($mediaId, $mediaType)
	    {
		    $select = $this->_mediasTables[$mediaType]->select();
		    $select->where('id = ?', $mediaId);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$mediaRow = $this->_mediasTables[$mediaType]->fetchRow($select);
			$userRow = $mediaRow->findParentTable_Users();
			$user = new Vo_User($userRow);
			return $user;
	    }

	    /**
	     * Retire un utilisateur du média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @return void
	     */
	    public function setUserOfMedia($userId, $mediaId, $mediaType)
	    {
		    $select = $this->_mediasTables[$mediaType]->select();
		    $select->where('id = ?', $mediaId);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$mediaRow = $this->_mediasTables[$mediaType]->fetchRow($select);
			$mediaRow->users_id = $userId;
			return $mediaRow->save();
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
	    public function getMediasByUserId($userId)
	    {
	        $medias = array();
			foreach($this->_mediasTables as $mediaTable)
			{
				$select = $mediaTable->select();
	    		$select->where('users_id = ?', $userId);
	    		$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
				$mediasRowset = $mediaTable->fetchAll($select);
				if($mediasRowset->count())
					$medias[$mediaTable->getMediaType()] = Vo_Media_Factory::getInstance()->rowsetToVoArray($mediaTable->getMediaVoClass(), $mediasRowset);
			}
	        return (array) $medias;
	    }

	    /**
	     * Ajoute une métadonnées dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Meta meta Un métadonnée
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @return int Identifiant de la nouvelle métadonnée
	     */
	    public function addMetaIntoMedia( Vo_Meta $meta, $mediaId, $mediaType)
	    {
	        if(!$meta->id)
	        	$meta->id = $this->_searchService->addMeta($meta);

        	$linkRow = $this->_metasAssocTable->createRow();
        	$linkRow->metas_id = $meta->id;
        	$linkRow->assoc_id = $mediaId;
        	$linkRow->assocType = 'Media_' . $mediaType;
        	$linkRow->save();
        	return $meta->id;
	    }

	    /**
	     * Retire une métadonnée du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int metaId Identifiant d'une métadonnée
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @return void
	     */
	    public function removeMetaFromMedia($metaId, $mediaId, $mediaType)
	    {
	    	$where = array(
	    		'metas_id = ' . $metaId,
	    		'assoc_id = ' . $mediaId,
	    		"assocType = 'Media_" . $mediaType . "'"
	    	);
	        $this->_metasAssocTable->delete($where);
			return true;
	    }

	    /**
	     * Valide ou invalide un média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @param  bool trueOrFalse True pour valide et false pour invalide
	     * @return void
	     */
	    public function validateMedia($mediaId, $mediaType, $trueOrFalse)
	    {
		    $select = $this->_mediasTables[$mediaType]->select();
		    $select->where('id = ?', $mediaId);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$mediaRow = $this->_mediasTables[$mediaType]->fetchRow($select);
			$mediaRow->isValid = $trueOrFalse;
			return $mediaRow->save();
	    }

	    /**
	     * Ajoute une data au média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Data_Abstract data Une data
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @return int Identifiant de la nouvelle data
	     */
	    public function addDataIntoMedia( Vo_Data_Abstract $data, $mediaId, $mediaType)
	    {
			if(!$data->id)
				$data->id = $this->_datasService->addData($data);
			$linkRow = $this->_datasAssocTable->createRow();
			$linkRow->datas_id = $data->id;
			$linkRow->dataType = $data->getType();
			$linkRow->assoc_id = $mediaId;
			$linkRow->assocType = 'Media_'.$mediaType;
			$linkRow->save();
			return $data->id;
	    }

	    /**
	     * Retire une data du média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int dataId Identifaint d'une data
	     * @param  string dataType Type de la data
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média
	     * @return void
	     */
	    public function removeDataFromMedia($dataId, $dataType, $mediaId, $mediaType)
	    {
			$this->_datasAssocTable->delete("datas_id = " . $dataId . " AND dataType = " . $this->_datasAssocTable->getAdapter()->quote($dataType) . " AND assoc_id = " . $mediaId . " AND assocType = 'Media_" . $mediaType . "'");
			return true;
	    }

	} /* end of class Service_Medias */