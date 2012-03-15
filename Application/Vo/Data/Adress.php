<?php

	/**
	 * Value Object de la data adresse
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Data
	 */

	/**
	 * Classe d'abstraction de data
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/../Data/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Value Object de la data adresse
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Data
	 */
	class Vo_Data_Adress extends Vo_Data_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Adresse
	     *
	     * @access public
	     * @var string
	     */
	    public $adress = '';

	    /**
	     * Code postal
	     *
	     * @access public
	     * @var int
	     */
	    public $zipCode = 0;

	    /**
	     * Ville
	     *
	     * @access public
	     * @var string
	     */
	    public $city = '';

	    /**
	     * Pays
	     *
	     * @access public
	     * @var string
	     */
	    public $country = '';

	    // --- OPERATIONS ---

	    /**
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  array|object|Zend_Db_Table_Row_Abstract comment Object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($adress = array())
	    {
	        parent::__construct($adress);
	    }

	    public function getType()
	    {
	    	return 'Adress';
	    }

	} /* end of class Vo_Data_Adress */