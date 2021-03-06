<?php

  /**
   * Classe d'abstraction de data
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */
  /**
   * Interface des datas
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/../Data/Interface.php');

  /**
   * Abstract ValueObject
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/../Abstract.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Classe d'abstraction de data
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   * @subpackage Data
   */
  class Vo_Data_Abstract extends Vo_Abstract implements Vo_Data_Interface
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * Identifiant de la data
       *
       * @access public
       * @var int
       */
      public $id = 0;

      /**
       * Date d'ajout de la data
       *
       * @access public
       * @var Zend_Date
       */
      protected $_addDate = null;

      /**
       * Date de modification de la data
       *
       * @access public
       * @var Zend_Date
       */
      protected $_setDate = null;

      // --- OPERATIONS ---

    /**
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  array|object|Zend_Db_Table_Row_Abstract comment Object permettant de remplire l'instance
       * @return mixed
       */
      public function __construct($data = array())
      {
        parent::__construct($data);
      }

      protected function _getKey($key)
      {
        switch($key)
        {
          case 'sessions_id':
          case '__className':
            return null;
          default:
            return $key;
        }
        return $key;
      }

      /**
       * Renvoi le type de la data
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getType()
      {
        throw new Vo_Exception('Les classes filles de Vo_Data_Abstract doivent redŽfinir la mŽthode getType.');
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
      }
        parent::__set($variableName, $value);
      }

      public function __get($variableName)
      {
      switch($variableName)
      {
        case 'addDate':
        case 'setDate':
          $variableName = '_' . $variableName;
          return $this->$variableName;
          break;
      }
        parent::__get($variableName);
      }

      public function toArray()
      {
        $returnValue = parent::toArray();

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

        $returnValue['addDate'] = is_null($this->addDate)?null:$this->addDate->toString('YYYY.MM.dd HH:mm:ss');
        $returnValue['setDate'] = is_null($this->setDate)?null:$this->setDate->toString('YYYY.MM.dd HH:mm:ss');

        return (array) $returnValue;
      }

  } /* end of class Vo_Data_Abstract */