<?php

	/**
	 * Permet d'interagir avec les items et la base de données
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
	 * Interface de service ayant des fonctions de modération
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface/Validate.php');

	/**
	 * Classes d'abstraction des services
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Permet d'interagir avec les items et la base de données
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Items extends Service_Abstract implements Service_Interface_Validate,
	                   										Service_Interface_User,
	                   										Service_Interface_Data
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Passerelles vers la table des items
	     *
	     * @access protected
	     * @var Table_Items
	     */
	    protected $_itemsTable = null;

	    /**
	     * Passerelles vers la table des questions
	     *
	     * @access protected
	     * @var Table_Queries
	     */
	    protected $_queriesTable = null;

	    /**
	     * Passerelles vers la table des commentaires
	     *
	     * @access protected
	     * @var Table_Comments
	     */
	    protected $_commentsTable = null;

	    /**
	     * Service des commentaires
	     *
	     * @access protected
	     * @var Service_Comments
	     */
	    protected $_commentsService = null;

	    /**
	     * Service des médias
	     *
	     * @access protected
	     * @var Service_Medias
	     */
	    protected $_mediasService = null;

	    /**
	     * Service des datas
	     *
	     * @access protected
	     * @var Service_Datas
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
	     * Passerelles vers la table de liaison entre les questions et les items
	     *
	     * @access protected
	     * @var Table_QueriesAssocItems
	     */
	    protected $_queriesAssocItemsTable = null;

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
	     * Passerelles vers la table de liaison des metas
	     *
	     * @access protected
	     * @var Table_MetasAssoc
	     */
	    protected $_mediasAssocTable = null;

	    /**
	     * Tableau contenant les passerelles vers les différentes tables de médias
	     *
	     * @access protected
	     * @var array
	     */
	    protected $_mediasTables = array();

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
	        $this->_itemsTable = new Table_Items();
	        $this->_queriesTable = new Table_Queries();
	        $this->_commentsTable = new Table_Comments();
	        $this->_queriesAssocItemsTable = new Table_QueriesAssocItems();
	        $this->_sessionsAssocQueriesTable = new Table_SessionsAssocQueries();
	        $this->_mediasAssocTable = new Table_Medias_Assoc();
	        $this->_datasAssocTable = new Table_Datas_Assoc();
	        $this->_commentsService = new Service_Comments();
	        $this->_mediasService = new Service_Medias();
	        $this->_datasService = new Service_Datas();
	        $this->_searchService = new Service_Search();
	        $this->_metasAssocTable = new Table_MetasAssoc();
	    }

	    /**
	     * Retourne toutes les items contenus dans la base de données en fonction de
	     * Options:
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
	    public function getItems($options = array())
	    {
	    	$items = array();
    		$select = $this->_itemsTable->select();
    		$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	if(count($options))
	    	{
	    		foreach($options['where'] as $key=>$where)
	    			$select->where($where[0], $where[1]);
	    		if(isset($options['order']))
	    			$select->order($options['order']);
	    		if(isset($options['limit']))
	    			$select->limit($options['limit'][0], $options['limit'][1]);
	    	}
	        $itemsRowset = $this->_itemsTable->fetchAll($select);
			if($itemsRowset->count())
	        	$items = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$ITEM_TYPE, $itemsRowset);
	        return $items;
	    }

	    /**
	     * Retourne un item de la base de données en fonction de son identifiant
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int itemId Identifiant d'un item
	     * @return Vo_Item
	     */
	    public function getItemById($itemId)
	    {
	    	$item = null;
	    	
    		$select = $this->_itemsTable->select();
    		$select->where('id = ?', $itemId);
    		$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
    		
	        $itemRow = $this->_itemsTable->fetchRow($select);
	    	if(!is_null($itemRow) && $itemRow)
	        	$item = Vo_Factory::getInstance()->factory(Vo_Factory::$ITEM_TYPE, $itemRow);
	        return $item;
	    }

	    /**
	     * Retourne tous les items contenu dans une question
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int queryId Identifiant d'une question
	     * @return array
	     */
	    public function getItemsByQueryId($queryId)
	    {
	    	$items = array();
	    	
	        $queryRow = $this->_queriesTable->find($queryId)->current();
	        if(!$queryRow)
	        	return array();
	        
	        $sessionsRowset = $queryRow->findManyToManyRowset('Table_Sessions', 'Table_SessionsAssocQueries');
	        if($sessionsRowset->count())
	        {
	        	$flag = false;
	        	foreach($sessionsRowset->toArray() as $key=>$value)
	        	{
	        		if($value['id'] == Zend_Registry::get('sessionID') && !$flag)
	        			$flag = true;
	        	}
	        	if(!$flag)
	        		return array();
	        } 	
	        else
	        	return array();
	        	
			$itemsRowset = $queryRow->findManyToManyRowset('Table_Items', 'Table_QueriesAssocItems');
			if($itemsRowset->count())
	        	$items = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$ITEM_TYPE, $itemsRowset);
	        return $items;
	    }

	    /**
	     * Retourne tous les items pour une metadonnÈe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int metaId Identifiant d'une mÈtadonnÈe
	     * @return array
	     */
	    public function getItemsByMetaId($metaId)
	    {
	    	$items = array();
	    	
	        $select = Zend_Registry::get('db')->select();
    		$table = 'items';
			$select->from('metas_assoc', null)
					->join($table, 'metas_assoc.assoc_id = '.$table.'.id')
					->where('metas_assoc.metas_id = ?', $metaId)
					->where("metas_assoc.assocType = ?", 'Item')
					->where($table . ".sessions_id = ?", Zend_Registry::get('sessionID'));
			$itemsRows = Zend_Registry::get('db')->fetchAll($select);
			if(count($itemsRows))
	        	$items = Vo_Factory::getInstance()->rowsToVoArray(Vo_Factory::$ITEM_TYPE, $itemsRows);
	        return $items;
	    }
	
		/**
	     * Retourne tous les items contenant un média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId Identifiant du média
	     * @param  string mediaType Type du média (Picture, Sound, Video, Text)
	     * @return array
	     */
	    public function getItemsByMediaId($mediaId, $mediaType)
	    {		
	    	$items = array();
	    	
	        $select = Zend_Registry::get('db')->select();
    		$table = 'items';
			$select->from('medias_assoc', null)
					->join($table, 'medias_assoc.assoc_id = '.$table.'.id')
					->where('medias_assoc.mediaType = ?', $mediaType)
					->where('medias_assoc.medias_id = ?', $mediaId)
					->where("medias_assoc.assocType = ?", 'Item')
					->where($table . ".sessions_id = ?", Zend_Registry::get('sessionID'));
			$itemsRows = Zend_Registry::get('db')->fetchAll($select);
			if(count($itemsRows))
	        	$items = Vo_Factory::getInstance()->rowsToVoArray(Vo_Factory::$ITEM_TYPE, $itemsRows);
	        return $items;
	    }

	    /**
	     * Ajoute un item à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Item item Un item
	     * @return void Identifiant du nouvel item
	     */
	    public function addItem( Vo_Item $item)
	    {
			$itemRow = $this->_itemsTable->createRow($item->toRowArray());
			$itemRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$itemRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$itemRow->sessions_id = Zend_Registry::get('sessionID');
			$itemRow->users_id = Zend_Registry::get('userID');
			return $itemRow->save();
	    }

	    /**
	     * Modifie un item dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Item item Un item
	     * @return void
	     */
	    public function setItem( Vo_Item $item)
	    {
    		$select = $this->_itemsTable->select();
    		$select->where('id = ?', $item->id);
    		$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
    		
	        $itemRow = $this->_itemsTable->fetchRow($select);
	    	$itemRowArray = $item->toRowArray();
			foreach($itemRowArray as $key=>$value)
			{
				if($itemRowArray[$key] != $itemRow->$key)
					$itemRow->$key = $value;
			}
			$itemRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			return $itemRow->save();
	    }

	    /**
	     * Supprime un item de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int itemId Identifiant d'un item
	     * @return void
	     */
	    public function deleteItem($itemId)
	    {
			if($this->_itemsTable->delete(array('id = ' . $itemId, 'sessions_id = ' . Zend_Registry::get('sessionID'))))
			{
				$this->_commentsTable->delete(array('items_id = ' . $itemId, 'sessions_id = ' . Zend_Registry::get('sessionID')));
				$this->_queriesAssocItemsTable->delete('items_id = ' . $itemId);
				$where = array(
					"assoc_id = " . $itemId,
					"assocType = 'Item'"
				);
				$this->_datasAssocTable->delete($where);
				$this->_metasAssocTable->delete($where);
				$this->_mediasAssocTable->delete($where);
			}
			return true;
	    }

	    /**
	     * Ajoute un comment à un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Comment comment Un commentaire
	     * @param  int itemId Identifiant d'un item
	     * @return int Identifiant du nouveau commentaire
	     */
	    public function addCommentIntoItem( Vo_Comment $comment, $itemId)
	    {
			if(!$comment->id)
			{
		    	$commentArray = $comment->toArray();
		    	$commentArray['item'] =  $itemId;
				$comment->id = $this->_commentsService->addComment(new Vo_Comment($commentArray));
			}
			else
				$comment->id = $this->_commentsService->setItemOfComment($comment, $itemId);
			return $comment->id;
	    }

	    /**
	     * Ajoute un comment à un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string contentOfComment Contenu du nouveau commentaire
	     * @param  int itemId Identifiant d'un item
	     * @return int Identifiant du nouveau commentaire
	     */
	    public function addCommentIntoItemPatch($contentOfComment, $itemId)
	    {
	    	$commentArray = array(
	    		'content' => $contentOfComment,
	    		'item' => $itemId
	    	);
			return $this->_commentsService->addComment(new Vo_Comment($commentArray));
	    }

	    /**
	     * Retire un commentaire à un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int commentId Identifiant d'un commentaire
	     * @param  int itemId Identifiant d'un item
	     * @return void
	     */
	    public function removeCommentFromItem($commentId, $itemId)
	    {
	    	$this->_commentsService->deleteComment($commentId);
			return true;
	    }

	    /**
	     * Ajoute un média à un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Media_Abstract media Un média
	     * @param  int itemId Identifiant d'un item
	     * @return int Identifiant du nouveau média
	     */
	    public function addMediaIntoItem( Vo_Media_Abstract $media, $itemId)
	    {
	    	if(!$media->id)
	    		$media->id = $this->_mediasService->addMedia($media);

    		$linkRow = $this->_mediasAssocTable->createRow();
    		$linkRow->medias_id = $media->id;
    		$linkRow->mediaType = $media->getType();
    		$linkRow->assoc_id = $itemId;
    		$linkRow->assocType = 'Item';
    		$linkRow->save();
    		return $media->id;
	    }

	    /**
	     * Retire un média d'un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId Identifiant d'un média
	     * @param  string mediaType Type du média
	     * @param  int itemId Identifiant d'un item
	     * @return void
	     */
	    public function removeMediaFromItem($mediaId, $mediaType, $itemId)
	    {
	    	$where = array(
	    		'medias_id = ' . $mediaId,
	    		"mediaType = '" . $mediaType . "'",
	    		'assoc_id = ' . $itemId,
	    		"assocType = 'Item'"
	    	);
	        $this->_mediasAssocTable->delete($where);
			return true;
	    }

	    /**
	     * Valide ou invalide un Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int voId Identifiant du Value Object
	     * @param  bool trueOrFalse True pour valide et false pour invalide
	     * @param  bool all Si true, alors tous les enfants du Value Object seront validés ou invalidés
	     * @return void
	     */
	    public function validateVo($voId, $trueOrFalse, $all = false)
	    {
			$itemRow = $this->_itemsTable->find($voId)->current();
			$itemRow->isValid = $trueOrFalse ? 1 : 0;
			return $itemRow->save();
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
        	$linkRow->assocType = 'Item';
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
	    		"assocType = 'Item'"
	    	);
	        $this->_metasAssocTable->delete($where);
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
			$itemRow = $this->_itemsTable->find($voId)->current();
			$userRow = $itemRow->findParentTable_Users();
			$user = null;
			if($userRow)
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
			$itemRow = $this->_itemsTable->find($voId)->current();
			$itemRow->users_id = $userId;
			return $itemRow->save();
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
	    	$select = $this->_itemsTable->select();
	    	$select->where('users_id = ?', $userId);

	    	$items = array();
			$itemRowset = $this->_itemsTable->fetchAll($select);
	        $items = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$ITEM_TYPE, $itemRowset);
	        return $items;
	    }

	    /**
	     * Ajoute une data dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Data_Abstract data Une data
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function addDataIntoVo( Vo_Data_Abstract $data, $voId)
	    {
			if(!$data->id)
				$data->id = $this->_datasService->addData($data);
			$linkRow = $this->_datasAssocTable->createRow();
			$linkRow->datas_id = $data->id;
			$linkRow->dataType = $data->getType();
			$linkRow->assoc_id = $voId;
			$linkRow->assocType = 'Item';
			$linkRow->save();

			if($data->getType() == 'Vote')
			{
		    	$redis = Zend_Registry::get('redis');
		    	$key = 'item-'.$voId.'-rate';
		    	$redis->del($key);
			}

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
			$this->_datasAssocTable->delete("datas_id = " . $dataId . " AND dataType = " . $this->_datasAssocTable->getAdapter()->quote($dataType) . " AND assoc_id = " . $voId . " AND assocType = 'Item'");
			return true;
	    }

	    /**
	     * Dans le total des votes d'un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int voId Identifiant de l'item
	     * @return int
	     */
	    public function getRateOfItem($itemId)
	    {
	    	$redis = Zend_Registry::get('redis');
	    	$key = 'item-'.$itemId.'-rate';
	    	$value = $redis->get($key);
	    	if($value) 
	    	{
	    		return $value;
	    	}

	    	$votes = array();
	    	$datas = $this->_datasService->getDatasByItemId($itemId);
	    	if(isset($datas['Vote']))
	    		$votes = $datas['Vote'];

	    	$comments = $this->_commentsService->getCommentsByItemId($itemId);
	    	foreach($comments as $comment)
	    	{
	    		$datas = $this->_datasService->getDatasByCommentId($comment->id);
	    		if(isset($datas['Vote']))
					$votes = array_merge($votes, $datas['Vote']);
	    	}

	    	$rate = 0;
	    	foreach($votes as $vote)
	    	{
	    		$rate += $vote->rate;
	    	}

	    	$redis->set($key, $rate);
	    	return $rate;
	    }

	} /* end of class Service_Items */