<?php

	/**
	 * Permet d'interagir avec les metas et la base de données aussi que faire des
	 * dans celle-ci
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
	 * Permet d'interagir avec les metas et la base de données aussi que faire des
	 * dans celle-ci
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 */
	class Service_Search extends Service_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Passerelles vers la table des métadonnées
	     *
	     * @access protected
	     * @var Table_Metas
	     */
	    protected $_metasTable = null;

	    /**
	     * Passerelles vers la table des métadonnées
	     *
	     * @access protected
	     * @var Table_MetasAssoc
	     */
	    protected $_metasAssocTable = null;

	    /**
	     * Passerelles vers la table de liaison des médtadonnées
	     *
	     * @access protected
	     * @var array
	     */
	    protected $_searchTables = array();

	    /**
	     * Adapter de base de données
	     *
	     * @var Zend_Db_Adapter_Abstract
	     */
	    protected $_db;

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
	    	$this->_db = Zend_Registry::get('db');
	        $this->_metasTable = new Table_Metas();
	        $this->_metasAssocTable = new Table_MetasAssoc();
	    }

	    /**
	     * Retourne toutes les métadonnées contenues dans la base de données en
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
	    public function getMetas($options = array())
	    {
	    	$metas = array();
	    	$select = $this->_metasTable->select();
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	if(count($options))
	    	{
	    		foreach($options['where'] as $key=>$where)
	    			$select->where($where[0], $where[1]);
	    		foreach($options['orWhere'] as $key=>$orWhere)
	    			$select->orWhere($orWhere[0], $orWhere[1]);
	    		if(isset($options['order']))
	    			$select->order($options['order']);
	    		if(isset($options['limit']))
	    			$select->limit($options['limit'][0], $options['limit'][1]);
	    	}
	        $metasRowset = $this->_metasTable->fetchAll($select);
			if($metasRowset->count())
	        	$metas = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$META_TYPE, $metasRowset);
	        return $metas;
	    }


	    /**
	     * Retourne toutes les métadonnées d'un ValueObject
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int voId Identifiant d'un VO
	     * @param  int voType Type du VO
	     * @return array(Vo_Meta)
	     */
	    public function getMetasByVo($voId, $voType)
	    {
	    	$metas = array();
    		$select = Zend_Registry::get('db')->select();
			$select->from('metas_assoc', null)
					->join('metas', 'metas_assoc.metas_id = metas.id')
					->where('metas_assoc.assoc_id = ?', $voId)
					->where("metas_assoc.assocType = ?", $voType)
					->where("metas.sessions_id = ?", Zend_Registry::get('sessionID'));
			$metasRow = Zend_Registry::get('db')->fetchAll($select);
			if(count($metasRow))
				$metas = Vo_Factory::getInstance()->rowsToVoArray(Vo_Factory::$META_TYPE, $metasRow);
	    	return $metas;
	    }

	    /**
	     * Retourne une métadonnées de la base de données en fonction de son
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int metaId Identifiant d'une métadonnée
	     * @return Vo_Meta
	     */
	    public function getMetaById($metaId)
	    {
	    	$meta = null;
	    	
	    	$select = $this->_metasTable->select();
	    	$select->where('id = ?', $metaId);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $metaRow = $this->_metasTable->fetchRow($select);
	    	if(!is_null($metaRow))
	        	$meta = Vo_Factory::getInstance()->factory(Vo_Factory::$META_TYPE, $metaRow);
	        return $meta;
	    }

	    /**
	     * Retourne une métadonnées de la base de données en fonction de son contenu
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string metaContent Contenu de la métadonnée
	     * @return Vo_Meta
	     */
	    public function getMetaByContent($metaContent)
	    {
	    	$meta = null;
	    	$select = $this->_metasTable->select();
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	$select->where('content = ?', $metaContent);
	        $metaRow = $this->_metasTable->fetchRow($select);
	    	if(!is_null($metaRow))
	        	$meta = Vo_Factory::getInstance()->factory(Vo_Factory::$META_TYPE, $metaRow);
	        return $meta;
	    }

	    /**
	     * Ajoute une métadonnée à la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Meta meta Une métadonnée
	     * @return int Identifiant de la nouvelle métadonnée
	     */
	    public function addMeta( Vo_Meta $meta)
	    {
			$metaRow = $this->_metasTable->createRow($meta->toRowArray());
			$metaRow->sessions_id = Zend_Registry::get('sessionID');
			return $metaRow->save();
	    }

	    /**
	     * Modifie une métadonnée dans la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Meta meta Une métadonnée
	     * @return void
	     */
	    public function setMeta( Vo_Meta $meta)
	    {
	    	$select = $this->_metasTable->select();
	    	$select->where('id = ?', $meta->id);
	    	$select->where('sessions_id = ?', Zend_Registry::get('sessionID'));
	    	
	        $metaRow = $this->_metasTable->fetchRow($select);
	    	$metaRowArray = $meta->toRowArray();
			foreach($metaRowArray as $key=>$value)
			{
				if($metaRowArray[$key] != $metaRow->$key)
					$metaRow->$key = $value;
			}
			return $metaRow->save();
	    }

	    /**
	     * Supprime une métadonnée de la base de données
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int metaId Identifiant d'une métadonnée
	     * @return void
	     */
	    public function deleteMeta($metaId)
	    {
	        if($this->_metasTable->delete(array('id = ' . $metaId, 'sessions_id = ' . Zend_Registry::get('sessionID'))))
	        	$this->_metasAssocTable->delete('metas_id = ' . $metaId);
			return true;
	    }

	    //////////////////////////////////////////////////////////////////////////////////

	    /**
	     * Retourne tous les Value Object répondant à la requête
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string request Requête à éxécuter sur la base de données
	     * @param  string section type de la recherche spécifié dans le fichier de config
	     * @return array
	     */
	    public function search($request, $section = 'Default')
	    {
			$config = $this->_getSearchConfig($section);
			$search = $this->_getSearch($request);

			$result = array();
			foreach($config as $searchTable)
			{
				if($searchTable->resultAssoc)
					$result = $this->_merge($result, $this->_getResultForAssocSearch($searchTable, $search));
				else
					$result = $this->_merge($result, $this->_getResultForSimpleSearch($searchTable, $search));
			}
			$result = $this->_orderByPertinence($result, $config, $search);
			//Zend_Debug::dump($result);

	        return (array) $result;
	    }

	    //////////////////////////////////////////////////////////////////////////////////

	    private function _getSearchConfig($section)
	    {
			$config = array();

			foreach(Zend_Registry::get('config')->searchEngine->search as $key=>$search)
			{
				if($search->name == $section)
				{
					foreach($search->searchTable as $key=>$searchTable)
						$config[$searchTable->id] = new Search_SearchTable($searchTable->toArray());
				}
			}

			return (array) $config;
	    }

	    private function _getSearch($request)
	    {
			return explode(' ', $request);
	    }

	    /**
	     * Enter description here...
	     *
	     * @param Search_SearchTable $searchTable
	     * @return array
	     */
	    private function _getResultForAssocSearch(Search_SearchTable $searchTable, array $search)
	    {
	    	return array();
	    }

	    /**
	     * Enter description here...
	     *
	     * @param Search_SearchTable $searchTable
	     * @return array
	     */
	    private function _getResultForSimpleSearch(Search_SearchTable $searchTable, array $search)
	    {
	    	$result = array();
	    	$table = new $searchTable->tableClass();
			$resultRow = $this->_db->fetchAll($this->_getSelect($table, $searchTable->searchFields, $search));
			if($resultRow)
				$result = Vo_Factory::getInstance()->rowsToVoArray($searchTable->voClass, $resultRow);
	    	return $result;
	    }

	    /**
	     * @param Zend_Db_Table_Abstract $table
	     * @param array $searchFields
	     * @param array $search
	     * @return Zend_Db_Select
	     */
	    private function _getSelect(Zend_Db_Table_Abstract $table, array $searchFields, array $search)
	    {
			$select = $table->select();
			$tableInfo = $table->info();
			$orCloseArray = array();
			foreach($searchFields as $key=>$searchField)
			{
				$secondOrCloseArray = array();
				foreach($search as $s)
					array_push($secondOrCloseArray, $this->_getCondition($tableInfo[Zend_Db_Table_Abstract::NAME], $searchField->name, $searchField->type, $s));
				array_push($orCloseArray, implode(' '.Zend_Db_Select::SQL_OR.' ', $secondOrCloseArray));

				if($searchField->required)
					$select->where($this->_getCondition($tableInfo[Zend_Db_Table_Abstract::NAME], $searchField->name, $searchField->type, $searchField->value));
			}
			$select->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $orCloseArray));
			return $select;
	    }

	    private function _getCondition($tableName, $field, $operator, $value)
	    {
	    	switch($operator)
	    	{
	    		case 'LIKE':
	    			return $this->_db->quoteInto($tableName . '.' . $field . ' = ?', $value);
	    		case '%LIKE%':
	    			return $this->_db->quoteInto($tableName . '.' . $field . ' LIKE ?', '%'.$value.'%');
	    		case 'NOT LIKE':
	    			return $this->_db->quoteInto($tableName . '.' . $field . ' != ?', $value);
	    	}
	    	return '';
	    }

	    /**
	     * Trie les resultats par ordre de pertinence
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  array results Resultats
	     * @param  array config Configuration de la requête
	     * @param  array search Elements recherchés
	     * @return array
	     */
	    private function _orderByPertinence($results, $config, $search)
	    {
	    	$pertinences = array();
	    	$resultAndPertinences = array();
			foreach($config as $searchTable)
			{
				foreach($results as $result)
				{
					if(get_class($result) == 'Vo_'.$searchTable->voClass)
					{
						$pertinence = 0;
						foreach($searchTable->searchFields as $searchField)
						{
							foreach($search as $value)
							{
								if($this->_isSearchResult($result, $searchField->name, $searchField->type, $value))
									$pertinence += $searchField->pertinence;
							}
						}

						array_push($pertinences, $pertinence);

						array_push($resultAndPertinences, array(
							'pertinence' => $pertinence,
							'object' =>		$result
						));
					}
				}
			}

	    	array_multisort($pertinences, SORT_DESC, $results, SORT_ASC, $resultAndPertinences);

			$return = array();
			foreach($resultAndPertinences as $resultAndPertience)
				array_push($return, $resultAndPertience['object']);

			return $return;
	    }

	    private function _isSearchResult($object, $property, $searchType, $search)
	    {
	    	switch($searchType)
	    	{
	    		case 'LIKE':
	    			return $object->$property == $search;
	    		case '%LIKE%':
	    			return preg_match("/\b" . $search . "\b/i", $object->$property);
	    		case 'NOT LIKE':
	    			return $object->$property != $search;
	    		default:
	    			return false;
	    	}
	    }

	    private function _merge($array1, $array2)
	    {
	    	$array = array();
	    	for($i = 0; $i < func_num_args(); $i++)
	    	{
	    		$a = func_get_arg($i);
	    		foreach($a as $value)
	    			array_push($array, $value);
	    	}
	    	return $array;
	    }

	} /* end of class Service_Search */