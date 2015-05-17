<?php

  /**
   * ValueObject d'un mŽdia texte
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */

  /**
   * Classe d'abstract de ValueObject des mŽdias
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Abstract.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * ValueObject d'un mŽdia texte
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */
  class Vo_Media_Text extends Vo_Media_Abstract
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * Contenu du texte
       *
       * @access public
       * @var string
       */
      public $content = '';

      // --- OPERATIONS ---

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  mixed text array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
       * @return mixed
       */
      public function __construct($text = array())
      {
        parent::__construct($text);
      }

      public function getType()
      {
        return 'Text';
      }

  } /* end of class Vo_Media_Text */