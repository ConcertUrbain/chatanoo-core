<?php

  /**
   * Class for SQL table interface.
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Table
   * @subpackage Datas
   */

  /**
   * Interface de table de datas
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Interface.php');


  /* user defined includes */

  /* user defined constants */

  /**
   * Class for SQL table interface.
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Table
   * @subpackage Datas
   */
  class Table_Datas_Vote extends Zend_Db_Table_Abstract implements Table_Datas_Interface
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * Table Name
       *
       * @access protected
       * @var string
       */
      protected $_name = 'datas_vote';

      /**
       * The primary key column or columns.
       * A compound key should be declared as an array.
       * You may declare a single-column primary key
       * as a string.
       *
       * @access protected
       * @var mixed
       */
      protected $_primary = 'id';

      /**
       * Simple array of class names of tables that are "children" of the current
       * table, in other words tables that contain a foreign key to this one.
       * Array elements are not table names; they are class names of classes that
       * extend Zend_Db_Table_Abstract.
       *
       * @access protected
       * @var array
       */
      protected $_referenceMap = array(
        'auteur' => array(
          'columns'    => 'users_id',
          'refTableClass'  => 'Table_Users',
          'refColumns'  => 'id'
        )
      );

    /**
       * Retourne le type des datas de la table
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getDataType()
      {
        return 'Vote';
      }

    /**
       * Retourne le type des Value Object de datas de la table
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
      public function getDataVoClass()
      {
        return 'Vote';
      }

    /**
       * Retourne le nom de la table
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return string
       */
    public function getTableName()
    {
      return $this->_name;
    }

      // --- OPERATIONS ---

  } /* end of class Table_Datas_Vote */