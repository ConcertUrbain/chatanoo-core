<?php

	/**
	 * Interface de service ayant des m�tadonn�es
	 *
	 * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	 * @package Service
	 * @subpackage Interface
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface de service ayant des m�tadonn�es
	 *
	 * @access public
	 * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	 * @package Service
	 * @subpackage Interface
	 */
	interface Service_Interface_Meta
	{


	    // --- OPERATIONS ---

	    /**
	     * Ajoute une m�tadonn�es dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	     * @param  Vo_Meta meta Un m�tadonn�e
	     * @param  int voId Identifiant du Value Object
	     * @return int Identifiant de la nouvelle m�tadonn�e
	     */
	    public function addMetaIntoVo( Vo_Meta $meta, $voId);

	    /**
	     * Retire une m�tadonn�e du Value Object
	     *
	     * @access public
	     * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	     * @param  int metaId Identifiant d'une m�tadonn�e
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function removeMetaFromVo($metaId, $voId);

	} /* end of interface Service_Interface_Meta */