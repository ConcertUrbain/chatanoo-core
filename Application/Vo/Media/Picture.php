<?php

	/**
	 * ValueObject d'un média image
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */

	/**
	 * Classe d'abstract de ValueObject des médias
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Abstract.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * ValueObject d'un média image
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */
	class Vo_Media_Picture extends Vo_Media_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Url de l'image
	     *
	     * @access public
	     * @var string
	     */
	    public $url = '';

	    /**
	     * Largeur de l'image
	     *
	     * @access public
	     * @var float
	     */
	    public $width = 0.0;

	    /**
	     * Hauteur de l'image
	     *
	     * @access public
	     * @var float
	     */
	    public $height = 0.0;

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed picture array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($picture = array())
	    {
	        parent::__construct($picture);
	    }

	    public function getType()
	    {
	    	return 'Picture';
	    }

	} /* end of class Vo_Media_Picture */