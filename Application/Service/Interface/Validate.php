<?php

  /**
   * Interface de service ayant des fonctions de modŽration
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   * @subpackage Interface
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface de service ayant des fonctions de modŽration
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   * @subpackage Interface
   */
  interface Service_Interface_Validate
  {


      // --- OPERATIONS ---

      /**
       * Valide ou invalide un Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int voId Identifiant du Value Object
       * @param  bool trueOrFalse True pour valide et false pour invalide
       * @param  bool all Si true, alors tous les enfants du Value Object seront validŽs ou invalidŽs
       * @return void
       */
      public function validateVo($voId, $trueOrFalse, $all = false);

  } /* end of interface Service_Interface_Validate */