<?php

	/**
	 * ValueObject d'un média vidéo
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
	 * ValueObject d'un média vidéo
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */
	class Vo_Media_Video extends Vo_Media_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Url de la vidéo
	     *
	     * @access public
	     * @var string
	     */
	    public $url = '';

	    /**
	     * Largeur de la vidéo
	     *
	     * @access public
	     * @var float
	     */
	    public $width = 0.0;

	    /**
	     * Hauteur de la vidéo
	     *
	     * @access public
	     * @var float
	     */
	    public $height = 0.0;

	    /**
	     * Temps total de la vidéo (en seconde)
	     *
	     * @access public
	     * @var int
	     */
	    public $totalTime = 0;

	    // --- OPERATIONS ---

	    /**
	     * Constructeur de la classe
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  mixed video array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
	     * @return mixed
	     */
	    public function __construct($video = array())
	    {
	    	parent::__construct($video);
	    }

	    public function getType()
	    {
	    	return 'Video';
	    }

	} /* end of class Vo_Media_Video */