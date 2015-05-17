<?php

  /**
   * Interface des Values Objects pour tre modŽrŽs
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Interface
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface des Values Objects pour tre modŽrŽs
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
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
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  bool trueOrFalse True pour valide et false pour invalide
       * @return void
       */
      public function validate($trueOrFalse);

      /**
       * Retourne un boolŽan indiquant l'Žtat de validation du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return bool
       */
      public function isValid();

  } /* end of interface Vo_Interface_Validate */