<?php

  /**
   * Interface des plugins
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Plugin
   * @subpackage Interface
   */

  /**
   * Interface des plugins
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Plugin
   * @subpackage Interface
   */
  interface Plugin_Interface
  {
      /**
       * Execute le plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed resultat du plugin
       */
    public function execute();

      /**
       * Modifie les params du plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  array params Tableau des paramtres
       * @return mixed resultat du plugin
       */
    function setParams($params);

      /**
       * Returne les params du plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed resultat du plugin
       */
    function getParams();
  }