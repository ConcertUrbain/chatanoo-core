<?php

	/**
	 * Interface de service ayant des auteurs
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 * @subpackage Interface
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface de service ayant des auteurs
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Service
	 * @subpackage Interface
	 */
	interface Service_Interface_User
	{


	    // --- OPERATIONS ---

	    /**
	     * Retourne l'utilisateur dans le Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function getUserFromVo($voId);

	    /**
	     * Retire un utilisateur du Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @param  int voId Identifiant du Value Object
	     * @return void
	     */
	    public function setUserOfVo($userId, $voId);

	    /**
	     * Retourne tous les Values Objects du type du service ayant pour
	     * celui précisé
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  int userId Identifiant d'un utilisateur
	     * @return array
	     */
	    public function getVosByUserId($userId);

	} /* end of interface Service_Interface_User */
