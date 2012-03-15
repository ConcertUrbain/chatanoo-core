<?php

	/**
	 * Interface des Value Object
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface des Value Object
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 */
	interface Vo_Interface
	{


	    // --- OPERATIONS ---

	    /**
	     * Converti le Value Object en tableau
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return array
	     */
	    public function toArray();

	    /**
	     * Converti le Value Object en tableau compatible avec Zend_Db_Table_Row_Abstract
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return array
	     */
	    public function toRowArray();

	    /**
	     * Retourne le type du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return string
	     */
	    public function getType();

	} /* end of interface Vo_Interface */