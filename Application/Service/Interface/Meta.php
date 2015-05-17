<?php

  /**
   * Interface de service ayant des mŽtadonnŽes
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   * @subpackage Interface
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface de service ayant des mŽtadonnŽes
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   * @subpackage Interface
   */
  interface Service_Interface_Meta
  {


      // --- OPERATIONS ---

      /**
       * Ajoute une mŽtadonnŽes dans le Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Meta meta Un mŽtadonnŽe
       * @param  int voId Identifiant du Value Object
       * @return int Identifiant de la nouvelle mŽtadonnŽe
       */
      public function addMetaIntoVo( Vo_Meta $meta, $voId);

      /**
       * Retire une mŽtadonnŽe du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int metaId Identifiant d'une mŽtadonnŽe
       * @param  int voId Identifiant du Value Object
       * @return void
       */
      public function removeMetaFromVo($metaId, $voId);

  } /* end of interface Service_Interface_Meta */