<?php

  /**
   * Interface de table de datas
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Table
   * @subpackage Datas
   */

  /* user defined includes */

  /* user defined constants */

  /**
   * Interface de table de datas
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Table
   * @subpackage Datas
   */
  interface Table_Datas_Interface
  {
    /**
       * Retourne le type des datas de la table
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
    public function getDataType();

    /**
       * Retourne le type des Value Object de datas de la table
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
    public function getDataVoClass();

    /**
       * Retourne le nom de la table
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
    public function getTableName();

  }