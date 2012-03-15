<?php

	/**
	 * Interface des m�dias
	 *
	 * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface des m�dias
	 *
	 * @access public
	 * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */
	interface Vo_Media_Interface
	{


	    // --- OPERATIONS ---

	    /**
	     * Renvoi le type du m�dia
	     *
	     * @access public
	     * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	     * @return string
	     */
	    public function getType();

	} /* end of interface Vo_Media_Interface */