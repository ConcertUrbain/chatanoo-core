<?php

  /**
   * Interface des mŽdias
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface des mŽdias
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */
  interface Vo_Media_Interface
  {


      // --- OPERATIONS ---

      /**
       * Renvoi le type du mŽdia
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getType();

  } /* end of interface Vo_Media_Interface */