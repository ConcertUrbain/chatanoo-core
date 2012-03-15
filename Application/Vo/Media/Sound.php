<?php

	/**
	 * ValueObject d'un média son
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
	 * ValueObject d'un média son
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */
	class Vo_Media_Sound
	    extends Vo_Media_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Url du son
	     *
	     * @access public
	     * @var string
	     */
	    public $url = '';

	    /**
	     * Temps total du son
	     *
	     * @access public
	     * @var string
	     */
	    public $totalTime = '';

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed sound array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($sound = array())
	    {
	        parent::__construct($sound);
	    }

	    public function getType()
	    {
	    	return 'Sound';
	    }

	} /* end of class Vo_Media_Sound */