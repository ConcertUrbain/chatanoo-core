<?php

  /**
   * Abstract ValueObject
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   */

  /**
   * Interface des Value Object
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Interface.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Abstract ValueObject
   *
   * @abstract
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Vo
   */
  abstract class Vo_Abstract implements Vo_Interface
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      // --- OPERATIONS ---

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  array|object|Zend_Db_Table_Row_Abstract|string vo Object permettant de remplire l'instance
       * @return mixed
       */
      public function __construct($vo = array())
      {
        if($vo instanceof Zend_Db_Table_Row_Abstract)
          $vo = $vo->toArray();

        if(is_string($vo) && preg_match('/^\{("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?\}$/', $vo))
          $vo = Zend_Json_Decoder::decode($vo, Zend_Json::TYPE_ARRAY);

        if(is_array($vo))
        {
          foreach($vo as $key=>$value)
          {
            $key = $this->_getKey($key);
            if($key)
              $this->$key = $value;
          }
        }
        elseif(is_object($vo))
        {
          foreach($vo as $key=>$value)
          {
            $key = $this->_getKey($key);
            if($key)
              $this->$key = $value;
          }
        }
        else
        {
          throw new Vo_Exception('Invalide type of comment parameter in contructor.', 3);
        }
      }

      protected function _getKey($key)
      {
        return $key;
      }

     /**
       * Converti le Value Object en tableau
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return array
       */
      public function toArray()
      {
          $returnValue = array();

          foreach($this as $key=>$value)
          {
            if(preg_match('/^_(.*)$/', $key) == 0) // exclut toutes les variables commencant par un '_', par convention toutes les variables privŽes
              $returnValue[$key] = $value;
          }

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
          $returnValue = array();

          foreach($this as $key=>$value)
          {
            switch($key)
            {
              case 'id':
                if($value != 0)
                  $returnValue[$key] = $value;
                break;
              default:
                if(preg_match('/^_(.*)$/', $key) == 0) // exclut toutes les variables commencant par un '_', par convention toutes les variables privŽes
                  $returnValue[$key] = $value;
                break;
            }
          }

          return (array) $returnValue;
      }


     /**
       * Converti le Value Object en JSON
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function toJson()
      {
        $arr = $this->toArray();
        $arr['__className'] = get_class($this);
        foreach ($arr as $key => $value) {
          if (is_string($value))
            $arr[$key] = $value;
        }
        return json_encode($arr);
      }

      /**
       * Retourne le type du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getType()
      {
        throw new Vo_Exception('Les classes filles de Vo_Abstract doivent redŽfinir la mŽthode getType.');
      }

      /**
       * Short description of method __set
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  string variableName
       * @param  mixed value
       * @return void
       */
      public function __set($variableName, $value)
      {
          throw new Vo_Exception("Vous tentez de modifier une propriŽtŽ qui n'existe pas : '$variableName'.", 1);
      }

      /**
       * Short description of method __get
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  string variableName
       * @return mixed
       */
      public function __get($variableName)
      {
          throw new Vo_Exception("Vous tentez d'accder a une propriŽtŽ qui n'existe pas : '$variableName'.", 2);
      }

  } /* end of abstract class Vo_Abstract */
