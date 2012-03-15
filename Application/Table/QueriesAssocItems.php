<?php

	/**
	 * Class for SQL table interface.
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Table
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Class for SQL table interface.
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Table
	 */
	class Table_QueriesAssocItems
	    extends Zend_Db_Table_Abstract
	{
	    // --- ASSOCIATIONS ---


	    // --- ATTRIBUTES ---

	    /**
	     * Table Name
	     *
	     * @access protected
	     * @var string
	     */
	    protected $_name = 'queries_assoc_items';

	    /**
	     * The primary key column or columns.
	     * A compound key should be declared as an array.
	     * You may declare a single-column primary key
	     * as a string.
	     *
	     * @access protected
	     * @var mixed
	     */
	    protected $_primary = array('queries_id', 'items_id');

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
	    	'queries' => array(
	    		'columns'		=> 'queries_id',
	    		'refTableClass'	=> 'Table_Queries',
	    		'refColumns'	=> 'id'
	    	),
	    	'items' => array(
	    		'columns'		=> 'items_id',
	    		'refTableClass'	=> 'Table_Items',
	    		'refColumns'	=> 'id'
	    	)
	    );

	    // --- OPERATIONS ---

	} /* end of class Table_QueriesAssocItems */