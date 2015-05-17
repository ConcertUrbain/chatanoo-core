<?php

  /**
   * Interface des datas
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface des datas
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */
  interface Vo_Data_Interface
  {


      // --- OPERATIONS ---

      /**
       * Renvoi le type de la data
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getType();

  } /* end of interface Vo_Data_Interface */