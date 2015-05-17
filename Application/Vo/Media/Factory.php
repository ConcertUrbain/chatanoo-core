<?php

  /**
   * Classe permettant de crŽer des mŽdias (patern Factory et Singleton)
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
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
   * Classe permettant de crŽer des mŽdias (patern Factory et Singleton)
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */
  class Vo_Media_Factory extends Vo_Factory_Abstract
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
       * Constance donnant le type du mŽdia vidŽo
       *
       * @access public
       * @var string
       */
      public static $VIDEO_TYPE = 'Video';

      /**
       * Constance donnant le type du mŽdia son
       *
       * @access public
       * @var string
       */
      public static $SOUND_TYPE = 'Sound';

      /**
       * Constance donnant le type du mŽdia image
       *
       * @access public
       * @var string
       */
      public static $PICTURE_TYPE = 'Picture';

      /**
       * Constance donnant le type du mŽdia texte
       *
       * @access public
       * @var string
       */
      public static $TEXT_TYPE = 'Text';

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
       * CrŽe et retourne un mŽdia
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  string type Type du mŽdia
       * @param  mixed media
       * @return Vo_Abstract
       */
      public function factory($type, $media = array())
      {
          $className = 'Vo_Media_' . $type;
          return new $className($media);
      }

  } /* end of class Vo_Media_Factory */