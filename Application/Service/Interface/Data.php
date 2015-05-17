<?php

  /**
   * Interface de service ayant des datas
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   * @subpackage Interface
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface de service ayant des datas
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   * @subpackage Interface
   */
  interface Service_Interface_Data
  {


      // --- OPERATIONS ---

      /**
       * Ajoute une data dans le Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Data_Abstract data Une data
       * @param  int voId Identifiant du Value Object
       * @return int Identifiant de la nouvelle data
       */
      public function addDataIntoVo( Vo_Data_Abstract $data, $voId);

      /**
       * Retire une data du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int dataId Identifaint d'une data
       * @param  string dataType Type de la data
       * @param  int voId Identifiant du Value Object
       * @return void
       */
      public function removeDataFromVo($dataId, $dataType, $voId);

  } /* end of interface Service_Interface_Data */