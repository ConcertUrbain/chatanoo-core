<?php

	/**
	 * Value Object d'un utilisateur
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
	 * Value Object d'un utilisateur
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */
	class Vo_User extends Vo_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Identifiant de l'utilisateur
	     *
	     * @access public
	     * @var int
	     */
	    public $id = 0;

	    /**
	     * Nom de l'utilisateur
	     *
	     * @access public
	     * @var string
	     */
	    public $firstName = '';

	    /**
	     * Prénom de l'utilisateur
	     *
	     * @access public
	     * @var string
	     */
	    public $lastName = '';

	    /**
	     * Pseudo de l'utilisateur
	     *
	     * @access public
	     * @var string
	     */
	    public $pseudo = '';

	    /**
	     * Password de l'utilisateur
	     *
	     * @access public
	     * @var string
	     */
	    public $password = '';

	    /**
	     * Email de l'utilisateur
	     *
	     * @access public
	     * @var string
	     */
	    public $email = '';

	    /**
	     * Role de l'utilisateur (utilisé pour les droits d'aministration)
	     *
	     * @access public
	     * @var string
	     */
	    public $role = '';

	    /**
	     * Date d'ajout de l'utilisateur
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_addDate = null;

	    /**
	     * Date de modification de l'utilisateur
	     *
	     * @access public
	     * @var Zend_Date
	     */
	    protected $_setDate = null;

	    /**
	     * Booléan permettant le banissement de l'utilisateur
	     *
	     * @access private
	     * @var bool
	     */
	    protected $_isBan = false;

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed user array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($user = array())
	    {
	    	parent::__construct($user);
	    	// DENIS : $this->password = "";
	    }

	    protected function _getKey($key)
	    {
	    	switch($key)
	    	{
	    		case 'isBan':
	    			return '_' . $key;
	    		case 'sessions_id':
	    		case '__className':
	    			return null;
	    		default:
	    			return $key;
	    	}
	    	return $key;
	    }

	    /**
	     * Retourne un booléan indiquant si l'utilisateur est bannis
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return bool
	     */
	    public function isBan()
	    {
	        return (bool) $this->_isBan;
	    }

	    /**
	     * Permet de bannir un utilisateur
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  bool trueOrFalse True pour bannir l'utilisateur et false pour le débannir
	     * @return void
	     */
	    public function ban($trueOrFalse)
	    {
	        $this->_isBan = $trueOrFalse;
	    }

	    public function __set($variableName, $value)
	    {
			switch($variableName)
			{
				case 'addDate':
				case 'setDate':
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
				case "isBan":
					$variableName = '_' . $variableName;

					if(is_null($value))
					{
						$this->$variableName = null;
						return;
					}

					$this->$variableName = $value;
					return;

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
				case 'isBan':
					$variableName = '_' . $variableName;
					return $this->$variableName;
					break;
			}
	    	parent::__get($variableName);
	    }

	    public function toArray()
	    {
	    	$returnValue = parent::toArray();
	    	$returnValue['_isBan'] = $this->_isBan;
	    	$returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');

	    	return (array) $returnValue;
	    }

	    public function toRowArray()
	    {
	    	$returnValue = parent::toRowArray();

	    	$returnValue['isBan'] = $this->_isBan;
	    	$returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
	    	$returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');

	    	return (array) $returnValue;
	    }

	    public function getType()
	    {
	    	return 'User';
	    }

	} /* end of class Vo_User */