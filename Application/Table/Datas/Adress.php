<?php

	/**
	 * Class for SQL table interface.
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Table
	 * @subpackage Datas
	 */

	/**
	 * Interface de table de datas
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 */
	require_once(dirname(__FILE__) . '/Interface.php');

	/* user defined includes */

	/* user defined constants */

	/**
	 * Class for SQL table interface.
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Table
	 * @subpackage Datas
	 */
	class Table_Datas_Adress extends Zend_Db_Table_Abstract implements Table_Datas_Interface
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Table Name
	     *
	     * @access protected
	     * @var string
	     */
	    protected $_name = 'datas_adress';

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
	     * Retourne le type des datas de la table
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return string
	     */
	    public function getDataType()
	    {
	    	return 'Adress';
	    }

		/**
	     * Retourne le type des Value Object de datas de la table
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return string
	     */
	    public function getDataVoClass()
	    {
	    	return 'Adress';
	    }

		/**
	     * Retourne le nom de la table
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @return string
	     */
		public function getTableName()
		{
			return $this->_name;
		}

	    // --- OPERATIONS ---

	} /* end of class Table_Datas_Adress */