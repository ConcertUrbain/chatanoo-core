<?php

  /**
   * Classe d'abstract de ValueObject des mŽdias
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */

  /**
   * Classe d'abstraction de Value Object
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/../Abstract.php');

  /**
   * Interface des mŽdias
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once('Interface.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Classe d'abstract de ValueObject des mŽdias
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Media
   */
  class Vo_Media_Abstract extends Vo_Abstract implements Vo_Media_Interface,
                                   Vo_Interface_Validate
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * Identifiant du mŽdia
       *
       * @access public
       * @var int
       */
      public $id = 0;

      /**
       * Titre du mŽdia
       *
       * @access public
       * @var string
       */
      public $title = '';

      /**
       * description du mŽdia
       *
       * @access public
       * @var string
       */
      public $description = '';

      /**
       * Url de la preview du mŽdia
       *
       * @access public
       * @var string
       */
      public $preview = '';

      /**
       * Date d'ajout du mŽdia
       *
       * @access public
       * @var Zend_Date
       */
      protected $_addDate = null;

      /**
       * Date de modification du mŽdia
       *
       * @access public
       * @var Zend_Date
       */
      protected $_setDate = null;

      /**
       * BoolŽen permettant la modŽration
       *
       * @access public
       * @var bool
       */
      protected $_isValid = false;

      /**
       * Identifiant de auteur du mŽdia
       *
       * @access protected
       * @var int
       */
      protected $_user = 0;

      // --- OPERATIONS ---

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  mixed picture array|object|Zend_Db_Table_Row_Abstract object permettant de remplire l'instance
       * @return mixed
       */
      public function __construct($media = array())
      {
        parent::__construct($media);
      }

      protected function _getKey($key)
      {
        switch($key)
        {
          case 'user':
          case 'datas':
          case 'metas':
          case 'isValid':
          case 'parents':
            return '_' . $key;
          case 'users_id';
            return '_user';
          case 'sessions_id':
          case '__className':
            return null;
          default:
            return $key;
        }
        return $key;
      }

      /**
       * Renvoi le type du mŽdia
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getType()
      {
        throw new Vo_Exception('Les classes filles de Vo_Meta_Abstract doivent redŽfinir la mŽthode getType.');
      }

      /**
       * Permet de valider et d'invalider le Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  bool trueOrFalse True pour valide et false pour invalide
       * @return void
       */
      public function validate($trueOrFalse)
      {
        $this->_isValid = (bool) $trueOrFalse;
      }

      /**
       * Retourne un boolŽan indiquant l'Žtat de validation du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return bool
       */
      public function isValid()
      {
          return (bool) $this->_isValid;
      }

      public function __set($variableName, $value)
      {
      switch($variableName)
      {
        case 'addDate':
        case 'setDate':
          $variableName = '_' . $variableName;

          if(is_null($value))
          {
            $this->$variableName = null;
            return;
          }

          if($value instanceof Zend_Date)
          {
            $this->$variableName = $value;
            return;
          }

          if($value && strlen($value) == 19 /*&& Zend_Date::isDate($value, 'YYYY.MM.dd HH:mm:ss')*/)
          {  
            $pattern = '/([0-9]{4}).([0-9]{2}).([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/';
            $matches = array();
            preg_match($pattern, $value, $matches);

            $date = new Zend_Date();
            $date->setYear($matches[1]);
            $date->setMonth($matches[2]);
            $date->setDay($matches[3]);
            $date->setHour($matches[4]);
            $date->setMinute($matches[5]);
            $date->setSecond($matches[6]);

            $this->$variableName = $date;
            return;
          }

          throw new Vo_Exception("La chaine de caractres n'est pas une date ou n'est pas au format ISO 8601 ('$value')", 4);
          break;
        case 'user':
          $variableName = '_' . $variableName;

          if(is_null($value))
          {
            $this->$variableName = null;
            return;
          }

          $this->$variableName = $value;
          return;

          break;
        case "isValid":
          $this->validate($value);
          return;
          break;
        case 'users_id':
          $this->_user = $value;
          break;
      }
        parent::__set($variableName, $value);
      }

      public function __get($variableName)
      {
      switch($variableName)
      {
        case 'addDate':
        case 'setDate':
        case 'user':
          $variableName = '_' . $variableName;
          return $this->$variableName;
          break;
        case 'isValid':
          return $this->isValid();
        case 'users_id':
          return $this->_user;
          break;
      }
        parent::__get($variableName);
      }

      public function toArray()
      {
        $returnValue = parent::toArray();

        $returnValue['_isValid'] = $this->_isValid;
        $returnValue['_user'] = $this->_user;
        $returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
        $returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');

        return (array) $returnValue;
      }

     /**
       * Converti le Value Object en tableau compatible avec Zend_Db_Table_Row_Abstract
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return array
       */
      public function toRowArray()
      {
        $returnValue = parent::toRowArray();

        $returnValue['isValid'] = $this->_isValid ? 1 : 0;
        $returnValue['users_id'] = $this->_user;
        $returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
        $returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');

        return (array) $returnValue;
      }

  } /* end of class Vo_Media_Abstract */