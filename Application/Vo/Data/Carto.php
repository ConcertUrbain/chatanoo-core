<?php

  /**
   * Value Object de la data cartographique
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */

  /**
   * Classe d'abstraction de data
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/../Data/Abstract.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Value Object de la data cartographique
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */
  class Vo_Data_Carto extends Vo_Data_Abstract
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * CoordonnŽe de longitude
       *
       * @access public
       * @var float
       */
      public $x = 0.0;

      /**
       * CoordonnŽe de latitude
       *
       * @access public
       * @var float
       */
      public $y = 0.0;

      // --- OPERATIONS ---

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  mixed carto array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
       * @return mixed
       */
      public function __construct($carto = array())
      {
          parent::__construct($carto);
      }

      public function getType()
      {
        return 'Carto';
      }

  } /* end of class Vo_Data_Carto */