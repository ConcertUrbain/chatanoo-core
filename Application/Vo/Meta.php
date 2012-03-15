<?php

	/**
	 * Value Object d'une metadonnée
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
	 * Value Object d'une metadonnée
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */
	class Vo_Meta extends Vo_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Identifiant de la métadonné
	     *
	     * @access public
	     * @var int
	     */
	    public $id = 0;

	    /**
	     * Type de la metadonné
	     *
	     * @access public
	     * @var string
	     */
	    public $name = 'keyword';

	    /**
	     * Contenu de la metadonné
	     *
	     * @access public
	     * @var int
	     */
	    public $content = 0;


	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  array|object|Zend_Db_Table_Row_Abstract meta Object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($meta = array())
	    {
	    	parent::__construct($meta);
	    }

	    protected function _getKey($key)
	    {
	    	switch($key)
	    	{
	    		case 'sessions_id':
	    		case '__className':
	    			return null;
	    		default:
	    			return $key;
	    	}
	    	return $key;
	    }

	    public function getType()
	    {
	    	return 'Meta';
	    }

	} /* end of class Vo_Meta */