<?php

	/**
	 * ValueObject d'une session
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */

	/**
	 * Abstract ValueObject
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * ValueObject d'une session
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */
	class Vo_Session extends Vo_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Identifiant de session dans la base de données
	     *
	     * @access public
	     * @var int
	     */
	    public $id = 0;

	    /**
	     * Identifiant de l'auteur dans la base de données
	     *
	     * @access private
	     * @var int
	     */
	    protected $_user = 0;

	    /**
	     * Titre de la session
	     *
	     * @access public
	     * @var string
	     */
	    public $title = '';

	    /**
	     * description de la session
	     *
	     * @access public
	     * @var string
	     */
	    public $description = '';

	    /**
	     * Date d'ajoute de la session
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_addDate = null;

	    /**
	     * Date de modification de la session
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_setDate = null;

	    /**
	     * Date de publication de la session
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_publishDate = null;

	    /**
	     * Date de fin de publication de la session
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_endDate = null;

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed session array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($session = array())
	    {
	    	parent::__construct($session);
	    }

	    protected function _getKey($key)
	    {
	    	switch($key)
	    	{
	    		case 'user':
	    		case 'queries':
	    			return '_' . $key;
	    		case 'users_id':
	    			return '_user';
	    		case 'sessions_id':
	    		case 'queries_id':
	    		case '__className':
	    			return null;
	    		default:
	    			return $key;
	    	}
	    	return $key;
	    }

	    public function __set($variableName, $value)
	    {
			switch($variableName)
			{
				case 'addDate':
				case 'setDate':
				case 'publishDate':
				case 'endDate':
					$variableName = '_' . $variableName;

					if(is_null($value))
					{
						$this->$variableName = null;
						return;
					}

					if($value instanceof Zend_Date)
					{
						$this->$variableName = $value;
						return;
					}

					if(Zend_Date::isDate($value, 'YYYY.MM.dd HH:mm:ss'))
					{
						$this->$variableName = new Zend_Date($value, 'YYYY.MM.dd HH:mm:ss');
						return;
					}

					throw new Vo_Exception("La chaine de caractères n'est pas une date ou n'est pas au format ISO 8601 ('$value')", 4);
					break;
				case 'user':
					$variableName = '_' . $variableName;

					if(is_null($value))
					{
						$this->$variableName = null;
						return;
					}

					$this->$variableName = $value;
					return;

					break;
				case 'users_id':
					$this->_user = $value;
					break;
			}
	    	parent::__set($variableName, $value);
	    }

	    public function __get($variableName)
	    {
			switch($variableName)
			{
				case 'addDate':
				case 'setDate':
				case 'publishDate':
				case 'endDate':
				case 'user':
					$variableName = '_' . $variableName;
					return $this->$variableName;
					break;
				case 'users_id':
					return $this->_user;
					break;
			}
	    	parent::__get($variableName);
	    }

	    public function toArray()
	    {
	    	$returnValue = parent::toArray();

	    	$returnValue['_user'] = $this->_user;
	    	$returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['publishDate'] = is_null($this->publishDate)?null:$this->publishDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['endDate'] = is_null($this->endDate)?null:$this->endDate->toString('YYYY.MM.dd HH:mm:ss');

	    	return (array) $returnValue;
	    }

	 	/**
	     * Converti le Value Object en tableau compatible avec Zend_Db_Table_Row_Abstract
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return array
	     */
	    public function toRowArray()
	    {
	    	$returnValue = parent::toRowArray();

	    	$returnValue['users_id'] = $this->_user;
	    	$returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['publishDate'] = is_null($this->publishDate)?null:$this->publishDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['endDate'] = is_null($this->endDate)?null:$this->endDate->toString('YYYY.MM.dd HH:mm:ss');

	    	return (array) $returnValue;
	    }

	    public function getType()
	    {
	    	return 'Session';
	    }

	} /* end of class Vo_Session */