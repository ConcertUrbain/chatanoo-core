<?php

	/**
	 * Interface des Values Objects pour �tre mod�r�s
	 *
	 * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Interface
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Interface des Values Objects pour �tre mod�r�s
	 *
	 * @access public
	 * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Interface
	 */
	interface Vo_Interface_Validate
	{


	    // --- OPERATIONS ---

	    /**
	     * Permet de valider et d'invalider le Value Object
	     *
	     * @access public
	     * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	     * @param  bool trueOrFalse True pour valide et false pour invalide
	     * @return void
	     */
	    public function validate($trueOrFalse);

	    /**
	     * Retourne un bool�an indiquant l'�tat de validation du Value Object
	     *
	     * @access public
	     * @author Mathieu Desv�, <mathieu.desve@unflux.fr>
	     * @return bool
	     */
	    public function isValid();

	} /* end of interface Vo_Interface_Validate */