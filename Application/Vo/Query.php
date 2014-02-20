<?php

	/**
	 * ValueObject d'une question
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */

	/**
	 * Classe d'abstraction de Value Object
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	/**
	 * Interface des Values Objects pour être modérés
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface/Validate.php');

	/* user defined includes */
	// section 10-0-2-1-a0a9b9d:120a39f0fcd:-8000:0000000000000F25-includes begin
	// section 10-0-2-1-a0a9b9d:120a39f0fcd:-8000:0000000000000F25-includes end

	/* user defined constants */
	// section 10-0-2-1-a0a9b9d:120a39f0fcd:-8000:0000000000000F25-constants begin
	// section 10-0-2-1-a0a9b9d:120a39f0fcd:-8000:0000000000000F25-constants end

	/**
	 * ValueObject d'une question
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */
	class Vo_Query extends Vo_Abstract implements Vo_Interface_Validate
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Identifiant de la question dans la base de données
	     *
	     * @access public
	     * @var int
	     */
	    public $id = 0;

	    /**
	     * Tableau contenant tous les identifiants de sessions qui contiennent cette
	     *
	     * @access private
	     * @var array
	     */
	    protected $_sessions = array();

	    /**
	     * Identifiant de auteur de la question
	     *
	     * @access private
	     * @var int
	     */
	    protected $_user = 0;

	    /**
	     * Contenu de la question
	     *
	     * @access public
	     * @var string
	     */
	    public $content = '';

	    /**
	     * Description de la question
	     *
	     * @access public
	     * @var string
	     */
	    public $description = '';

	    /**
	     * Date d'ajout de la question
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_addDate = null;

	    /**
	     * Date de modification de la question
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_setDate = null;

	    /**
	     * Date de publication de la question
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_publishDate = null;

	    /**
	     * Date de fin de publication de la question
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_endDate = null;

	    /**
	     * Booléen permettant la modération
	     *
	     * @access private
	     * @var bool
	     */
	    protected $_isValid = false;

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed query array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($query = array())
	    {
	    	parent::__construct($query);
	    }

	    protected function _getKey($key)
	    {
	    	switch($key)
	    	{
	    		case 'user':
	    		case 'items':
	    		case 'sessions':
	    		case 'datas':
	    		case 'metas':
	    		case 'isValid':
	    			return '_' . $key;
	    		case 'users_id':
	    			return '_user';
	    		case 'queries_id':
	    		case 'items_id':
	    		case 'sessions_id':
	    		case '__className':
	    			return null;
	    		default:
	    			return $key;
	    	}
	    	return $key;
	    }

	    /**
	     * Permet de valider et d'invalider le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  bool trueOrFalse True pour valide et false pour invalide
	     * @return void
	     */
	    public function validate($trueOrFalse)
	    {
	    	$this->_isValid = (bool) $trueOrFalse;
	    }

	    /**
	     * Retourne un booléan indiquant l'état de validation du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return bool
	     */
	    public function isValid()
	    {
	        return (bool) $this->_isValid;
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

					if($value && strlen($value) == 19 /*&& Zend_Date::isDate($value, 'YYYY.MM.dd HH:mm:ss')*/)
					{	
						$pattern = '/([0-9]{4}).([0-9]{2}).([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/';
						$matches = array();
						preg_match($pattern, $value, $matches);

						$date = new Zend_Date();
						$date->setYear($matches[1]);
						$date->setMonth($matches[2]);
						$date->setDay($matches[3]);
						$date->setHour($matches[4]);
						$date->setMinute($matches[5]);
						$date->setSecond($matches[6]);

						$this->$variableName = $date;
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
				case "isValid":
					$this->validate($value);
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
				case 'isValid':
					return $this->isValid();
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
	    	$returnValue['_isValid'] = $this->_isValid;
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
	    	$returnValue['isValid'] = $this->_isValid ? 1 : 0;
	    	$returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['publishDate'] = is_null($this->publishDate)?null:$this->publishDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['endDate'] = is_null($this->endDate)?null:$this->endDate->toString('YYYY.MM.dd HH:mm:ss');

	    	return (array) $returnValue;
	    }

	    public function getType()
	    {
	    	return 'Query';
	    }

	} /* end of class Vo_Query */