<?php

  /**
   * Classe permettant de crŽer des datas (patern Factory et Singleton)
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */

  /**
   * Class d'abstraction des factories
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/../Factory/Abstract.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Classe permettant de crŽer des datas (patern Factory et Singleton)
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */
  class Vo_Data_Factory extends Vo_Factory_Abstract
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * Instance de la classe
       *
       * @access private
       * @var Factory
       */
      private static $_instance = null;

      /**
       * Constance donnant le type d'une data vote
       *
       * @access public
       * @var string
       */
      public static $VOTE_TYPE = 'Vote';

      /**
       * Constance donnant le type d'une data adresse
       *
       * @access public
       * @var string
       */
      public static $ADRESS_TYPE = 'Adress';

      /**
       * Constance donnant le type d'une data cartographique
       *
       * @access public
       * @var string
       */
      public static $CARTO_TYPE = 'Carto';

      // --- OPERATIONS ---

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed
       */
      private function __construct(){}

      /**
       * Permet de rŽccupŽrer la seule instance de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return Vo_Media_Factory
       */
      public static function getInstance()
      {
          if(self::$_instance === null){
                self::$_instance = new self();
           }
           return self::$_instance;
      }

      /**
       * CrŽe et retourne une data
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  string type Type de la Data
       * @param  mixed data
       * @return Vo_Data_Abstract
       */
      public function factory($type, $data = array())
      {
          $className = 'Vo_Data_' . $type;
          return new $className($data);
      }

  } /* end of class Vo_Data_Factory */