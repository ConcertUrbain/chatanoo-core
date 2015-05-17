<?php

  /**
   * Permet d'appeler des plugins
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   */

  /**
   * Classes d'abstraction des services
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Abstract.php');


  /**
   * Permet d'appeler des plugins
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   */
  class Service_Plugins extends Service_Abstract
  {
    private $_plugins;

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed
       */
    public function __construct()
    {
      foreach(Zend_Registry::get('config')->plugins->plugin as $plugin)
        $this->_plugins[$plugin->name] = $plugin->class;
    }

      /**
       * Appel le plugin
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  string name Nom du plugins dŽfini dans le fichier config.xml
       * @param  array params Paramtres ˆ passer au plugin
       * @return mixed
       */
    public function call($name, $params)
    {
      if(!array_key_exists($name, $this->_plugins))
        throw new Exception('Plugin ' + $name + ' does not exist. Check the configuration file.');

      $plugin = new $this->_plugins[$name];

      if(!method_exists($plugin, 'execute'))
        throw new Exception('Plugin must have setParams method.');
      $plugin->setParams($params);

      if(!method_exists($plugin, 'execute'))
        throw new Exception('Plugin must have execute method.');

      return $plugin->execute();
    }

  }