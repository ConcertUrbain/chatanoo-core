<?php

	/**
	 * Interface de service ayant des métadonnées
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 * @subpackage Interface
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface de service ayant des métadonnées
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 * @subpackage Interface
	 */
	interface Service_Interface_Meta
	{


	    // --- OPERATIONS ---

	    /**
	     * Ajoute une métadonnées dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  Vo_Meta meta Un métadonnée
	     * @param  int voId Identifiant du Value Object
	     * @return int Identifiant de la nouvelle métadonnée
	     */
	    public function addMetaIntoVo( Vo_Meta $meta, $voId);

	    /**
	     * Retire une métadonnée du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int metaId Identifiant d'une métadonnée
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function removeMetaFromVo($metaId, $voId);

	} /* end of interface Service_Interface_Meta */