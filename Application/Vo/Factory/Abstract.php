<?php

	/**
	 * Class d'abstraction des factories
	 *
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Factory
	 */

	/* user defined includes */

	/* user defined constants */

	/**
	 * Class d'abstraction des factories
	 *
	 * @access public
	 * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	 * @package Vo
	 * @subpackage Factory
	 */
	abstract class Vo_Factory_Abstract
	{
		/** Crée et retourne un Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string type
	     * @param  mixed vo
	     * @return Vo_Abstract
	     */
	    abstract public function factory($type, $vo = array());

	    /**
	     * Crée et retourne un tableau de Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string type
	     * @param  Zend_Db_Table_Rowset_Abstract $rowset
	     * @return array
	     */
	    public function rowsetToVoArray($type, Zend_Db_Table_Rowset_Abstract $rowset)
	    {
	    	$array = array();
	    	foreach($rowset as $key=>$row)
	    	{
	    		array_push($array, $this->factory($type, $row));
	    	}
	    	return $array;
	    }

	    /**
	     * Crée et retourne un tableau de Value Object
	     *
	     * @access public
	     * @author Mathieu Desvé, <mathieu.desve@unflux.fr>
	     * @param  string type
	     * @param  array $rows
	     * @return array
	     */
	    public function rowsToVoArray($type, array $rows)
	    {
	    	$array = array();
	    	foreach($rows as $key=>$row)
	    	{
	    		array_push($array, $this->factory($type, $row));
	    	}
	    	return $array;
	    }

	}