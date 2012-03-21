<?php

	/**
	 * Permet d'interagir avec les datas et la base de données
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */

	/**
	 * Classes d'abstraction des services
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Permet d'interagir avec les datas et la base de données
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Datas  extends Service_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Tableau contenant les passerelles vers les différentes tables de data
	     *
	     * @access protected
	     * @var array
	     */
	    protected $_datasTables = array();

	    /**
	     * Passerelles vers la table de liaison des datas
	     *
	     * @access private
	     * @var Table_Datas_Assoc
	     */
	    private $_datasAssocTable = null;

	    /**
	     * Configuration
	     *
	     * @var Zend_Config
	     */
	    private $_config;

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

			foreach($this->_config->datas->data as $data)
			{
				$className = $data->tableClass;
				$this->_datasTables[$data->type] = new $className;
			}
			$this->_datasAssocTable = new Table_Datas_Assoc();
	    }

	    /**
	     * Retourne toutes les datas contenues dans la base de données en fonction
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
	    public function getDatas($options = array())
	    {
	        $datas = array();
			foreach($this->_datasTables as $dataTable)
			{
		    	$select = $dataTable->select();
		    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	if(count($options))
		    	{
		    		foreach($options['where'] as $key=>$where)
		    			$select->where($where[0], $where[1]);
		    		if(isset($options['order']))
		    			$select->order($options['order']);
		    		if(isset($options['limit']))
		    			$select->limit($options['limit'][0], $options['limit'][1]);
					$datasRowset = $dataTable->fetchAll($select);
		    	}
				$datasRowset = $dataTable->fetchAll($select);
				if($datasRowset->count())
					$datas[$dataTable->getDataType()] = Vo_Data_Factory::getInstance()->rowsetToVoArray($dataTable->getDataVoClass(), $datasRowset);
			}
	        return (array) $datas;
	    }

	    /**
	     * Retourne une data de la base de données en fonction de son identifiant
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int dataId Identifiant d'une data
	     * @param  string dataType Type de la data
	     * @return Vo_Data_Abstract
	     */
	    public function getDatasById($dataId, $dataType)
	    {
	    	$data = null;
	    	
		    $select = $this->_datasTables[$dataType]->select();
		    $select->where('id = ?', $dataId);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$dataRow = $this->_datasTables[$dataType]->fetchRow($select);
	    	if(!is_null($dataRow))
	    		$data =  Vo_Data_Factory::getInstance()->factory($dataType, $dataRow);
	        return $data;
	    }

	    private function _getDatasByVo($voId, $voType)
	    {
	        $datas = array();
			foreach($this->_datasTables as $dataTable)
			{
	    		$select = Zend_Registry::get('db')->select();
	    		$table = $dataTable->getTableName();
				$select->from('datas_assoc', null)
						->join($table, 'datas_assoc.datas_id = '.$table.'.id')
						->where('datas_assoc.dataType = ?', $dataTable->getDataType())
						->where('datas_assoc.assoc_id = ?', $voId)
						->where("datas_assoc.assocType = ?", $voType)
						->where($table . ".sessions_id = ?", Zend_Registry::get('sessionID'));
				$datasRows = Zend_Registry::get('db')->fetchAll($select);
				if(count($datasRows))
					$datas[$dataTable->getDataType()] = Vo_Data_Factory::getInstance()->rowsToVoArray($dataTable->getDataVoClass(), $datasRows);
			}
	        return (array) $datas;
	    }

	    /**
	     * Short description of method getDatasByItemId
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int itemId
	     * @return array
	     */
	    public function getDatasByItemId($itemId)
	    {
	        return (array) $this->_getDatasByVo($itemId, 'Item');
	    }

	    /**
	     * Short description of method getDatasByCommentId
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int commentId
	     * @return array
	     */
	    public function getDatasByCommentId($commentId)
	    {
	        return (array) $this->_getDatasByVo($commentId, 'Comment');
	    }

	    /**
	     * Short description of method getDatasByMediaId
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int mediaId
	     * @param  string mediaType
	     * @return array
	     */
	    public function getDatasByMediaId($mediaId, $mediaType)
	    {
	        return (array) $this->_getDatasByVo($mediaId, 'Media_'.$mediaType);
	    }

	    /**
	     * Short description of method getDatasByUserId
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId
	     * @return array
	     */
	    public function getDatasByUserId($userId)
	    {
	        return (array) $this->_getDatasByVo($userId, 'User');
	    }

	    /**
	     * Short description of method getDatasByQueryId
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int queryId
	     * @return array
	     */
	    public function getDatasByQueryId($queryId)
	    {
	        return (array) $this->_getDatasByVo($queryId, 'Query');
	    }

	    /**
	     * Ajoute une data à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Data_Abstract data Une data
	     * @return int Identifiant de la nouvelle data
	     */
	    public function addData( Vo_Data_Abstract $data)
	    {
	    	$dataRow = $this->_datasTables[$data->getType()]->createRow($data->toRowArray());
			$dataRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$dataRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$dataRow->sessions_id = Zend_Registry::get('sessionID');
			return $dataRow->save();
	    }

	    /**
	     * Modifie une data dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Data_Abstract data Une data
	     * @return void
	     */
	    public function setData( Vo_Data_Abstract $data)
	    {
		    $select = $this->_datasTables[$data->getType()]->select();
		    $select->where('id = ?', $data->id);
		    $select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
		    	
	    	$dataRow = $this->_datasTables[$data->getType()]->fetchRow($select);
	    	$dataRowArray = $data->toRowArray();
			foreach($dataRowArray as $key=>$value)
			{
				if($dataRowArray[$key] != $dataRow->$key)
					$dataRow->$key = $value;
			}
			$dataRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
			$dataRow->save();
	    }

	    /**
	     * Supprime une data de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int dataId Identifiant d'une data
	     * @param  string dataType Type d'une data
	     * @return void
	     */
	    public function deleteData($dataId, $dataType)
	    {
			if($this->_datasTables[$dataType]->delete(array('id = ' . $dataId, 'sessions_id = ' . Zend_Registry::get('sessionID'))))
				$this->_datasAssocTable->delete(array("datas_id = " . $dataId, "dataType = '" . $dataType . "'"));
	    }

	} /* end of class Service_Datas */