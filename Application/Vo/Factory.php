<?php

  /**
   * Classe permettant de crŽer des Value Object (patern Factory et Singleton)
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   */

  /**
   * Class d'abstraction des factories
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Factory/Abstract.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Classe permettant de crŽer des Value Object (patern Factory et Singleton)
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   */
  class Vo_Factory extends Vo_Factory_Abstract
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
       * Constance donnant le type d'une session
       *
       * @access public
       * @var string
       */
      public static $SESSION_TYPE = 'Session';

      /**
       * Constance donnant le type d'une question
       *
       * @access public
       * @var string
       */
      public static $QUERY_TYPE = 'Query';

      /**
       * Constance donnant le type d'un item
       *
       * @access public
       * @var string
       */
      public static $ITEM_TYPE = 'Item';

      /**
       * Constance donnant le type d'un commentaire
       *
       * @access public
       * @var string
       */
      public static $COMMENT_TYPE = 'Comment';

      /**
       * Constance donnant le type d'une mŽtadonnŽe
       *
       * @access public
       * @var string
       */
      public static $META_TYPE = 'Meta';

      /**
       * Constance donnant le type d'un utilisateur
       *
       * @access public
       * @var string
       */
      public static $USER_TYPE = 'User';

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
       * CrŽe et retourne un Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  string type
       * @param  mixed vo
       * @return Vo_Abstract
       */
      public function factory($type, $vo = array())
      {
      // remove password information
      if( isset($vo['password']) )
        $vo['password'] = null;

        $className = 'Vo_' . $type;
        return new $className($vo);
      }

  } /* end of class Vo_Factory */
