<?php

	/**
	 * Permet d'interagir avec les commentaires et la base de données
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
	require_once(dirname(__FILE__) . '/../Table/Comments.php');
	require_once(dirname(__FILE__) . '/../Vo/Comment.php');

	/* user defined constants */

	/**
	 * Permet d'interagir avec les commentaires et la base de données
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Comments extends Service_Abstract implements 	Service_Interface_User,
	                   											Service_Interface_Validate,
	                   											Service_Interface_Data
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Passerelles vers la table des commentaires
	     *
	     * @access protected
	     * @var Table_Comments
	     */
	    protected $_commentsTable = null;

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
			$this->_commentsTable = new Table_Comments();
			$this->_datasService = new Service_Datas();
			$this->_datasAssocTable = new Table_Datas_Assoc();
	    }

	    /**
	     * Retourne toutes les commentaires contenus dans la base de données en
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
	    public function getComments($options = array())
	    {
	    	$comments = array();
	    	$select = $this->_commentsTable->select();
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
	        $commentsRowset = $this->_commentsTable->fetchAll($select);
			if($commentsRowset->count())
	        	$comments = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$COMMENT_TYPE, $commentsRowset);
	        return $comments;
	    }

	    /**
	     * Retourne un commentaire de la base de données en fonction de son
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int commentId Identifiant d'un commentaire
	     * @return Vo_Comment
	     */
	    public function getCommentById($commentId)
	    {
	    	$comment = null;
	    	
	    	$select = $this->_commentsTable->select();
	    	$select->where('id = ?', $commentId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $commentRow = $this->_commentsTable->fetchRow($select);
	    	if(!is_null($commentRow) && $commentRow)
	        	$comment = Vo_Factory::getInstance()->factory(Vo_Factory::$COMMENT_TYPE, $commentRow);
	        return $comment;
	    }

	    /**
	     * Retourne tous les commentaire contenus dans un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int itemId Identifiant d'un item
	     * @return array
	     */
	    public function getCommentsByItemId($itemId)
	    {
	    	$select = $this->_commentsTable->select();
	    	$select->where('items_id = ?', $itemId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));

	    	$comments = array();
			$commentsRowset = $this->_commentsTable->fetchAll($select);
			if($commentsRowset->count())
	        	$comments = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$COMMENT_TYPE, $commentsRowset);
	        return $comments;
	    }

	    /**
	     * Ajoute un commentaire à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Comment comment Un commentaire
	     * @return int Identifiant du nouveau commentaire
	     */
	    public function addComment( Vo_Comment $comment)
	    {
			$commentRow = $this->_commentsTable->createRow($comment->toRowArray());
			$commentRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$commentRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$commentRow->sessions_id = Zend_Registry::get('sessionID');
			return $commentRow->save();
	    }

	    /**
	     * Modifie un commentaire dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Comment comment Un commentaire
	     * @return void
	     */
	    public function setComment( Vo_Comment $comment)
	    {
			$commentRow = $this->_commentsTable->find($comment->id)->current();
	    	$commentRowArray = $comment->toRowArray();
			foreach($commentRowArray as $key=>$value)
			{
				if($commentRowArray[$key] != $commentRow->$key)
					$commentRow->$key = $value;
			}
			$commentRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$commentRow->save();
			//$comment = new Vo_Comment($commentRow);
	    }

	    /**
	     * Modifie le lien entre un commentaire et un item
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Comment comment Un commentaire
	     * @param  int itemId Identifiant d'un item
	     * @return void
	     */
	    public function setItemOfComment( Vo_Comment $comment, $itemId)
	    {
			$commentRow = $this->_commentsTable->find($comment->id)->current();
			$commentRow->items_id = $itemId;
			$commentRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$commentRow->save();
	    }

	    /**
	     * Supprime un commentaire de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int commentId Identifiant d'un commentaire
	     * @return void
	     */
	    public function deleteComment($commentId)
	    {
			if($this->_commentsTable->delete(array('id = ' . $commentId, 'sessions_id = ' . Zend_Registry::get('sessionID'))) == 0)
				return;
			$this->_datasAssocTable->delete("assoc_id = " . $commentId . " AND assocType = 'Comment'");
	    }

	    /**
	     * Retourne l'utilisateur dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function getUserFromVo($voId)
	    {
	    	$select = $this->_commentsTable->select();
	    	$select->where('id = ?', $voId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $commentRow = $this->_commentsTable->fetchRow($select);
			$userRow = $commentRow->findParentTable_Users();
			$user = new Vo_User($userRow);
			return $user;
	    }

	    /**
	     * Change l'utilisateur du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function setUserOfVo($userId, $voId)
	    {
	    	$select = $this->_commentsTable->select();
	    	$select->where('id = ?', $voId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $commentRow = $this->_commentsTable->fetchRow($select);
			$commentRow->users_id = $userId;
			$commentRow->save();
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
	    	$select = $this->_commentsTable->select();
	    	$select->where('users_id = ?', $userId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));

			$commentsRowset = $this->_commentsTable->fetchAll($select);
	        $comments = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$COMMENT_TYPE, $commentsRowset);
	        return $comments;
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
	    	$select = $this->_commentsTable->select();
	    	$select->where('id = ?', $voId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $commentRow = $this->_commentsTable->fetchRow($select);
			$commentRow->isValid = $trueOrFalse;
			$commentRow->save();
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
			$linkRow->assocType = 'Comment';
			$linkRow->save();
			return $data->id;
	    }

	    /**
	     * Ajoute un vote au commentaire
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int rate Vote
	     * @param  int userId Identifiant du votant
	     * @param  int commentId Identifiant du commentaire
	     * @return void
	     */
	    public function addVoteIntoItemPatch($rate, $userId, $commentId)
	    {
	    	$vote = new Vo_Data_Vote();
	    	$vote->rate = $rate;
	    	$vote->user = $userId;

	    	$vote->id = $this->_datasService->addData($vote);
	    	$this->addDataIntoVo($vote, $commentId);
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
			$wheres = array(
				'datas_id = ' . $dataId,
				'dataType = ' . $this->_datasAssocTable->getAdapter()->quote($dataType),
				'assoc_id = ' . $voId,
				"assocType = 'Comment'"
			);
			$this->_datasAssocTable->delete($wheres);
	    }

	} /* end of class Service_Comments */