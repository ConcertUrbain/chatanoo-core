<?php

	/**
	 * Interface des médias
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface des médias
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Media
	 */
	interface Vo_Media_Interface
	{


	    // --- OPERATIONS ---

	    /**
	     * Renvoi le type du média
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return string
	     */
	    public function getType();

	} /* end of interface Vo_Media_Interface */