<?php

  class Plugin_Abstract implements Plugin_Interface
  {
    protected $_params;

      /**
       * Execute le plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed resultat du plugin
       */
    public function execute()
    {

    }

      /**
       * Modifie les params du plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  array params Tableau des paramtres
       * @return mixed resultat du plugin
       */
    public function setParams($params)
    {
      $this->_params = $params;
    }

      /**
       * Returne les params du plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed resultat du plugin
       */
    public function getParams()
    {
      return $this->_params;
    }

  }