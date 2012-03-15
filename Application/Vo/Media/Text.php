<?php

	/**
	 * ValueObject d'un média texte
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
	 * ValueObject d'un média texte
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */
	class Vo_Media_Text extends Vo_Media_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Contenu du texte
	     *
	     * @access public
	     * @var string
	     */
	    public $content = '';

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed text array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($text = array())
	    {
	    	parent::__construct($text);
	    }

	    public function getType()
	    {
	    	return 'Text';
	    }

	} /* end of class Vo_Media_Text */