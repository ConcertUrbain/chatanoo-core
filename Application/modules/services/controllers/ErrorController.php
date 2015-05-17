<?php

  class ErrorController extends Zend_Controller_Action
  {
      public function errorAction()
      {
      $error = $this->_getParam('error_handler');

      if($error->exception instanceof Zend_Controller_Exception)
      {
        $log = "notice";
        $this->getResponse()->setHttpResponseCode(404);
      }
      elseif($error->exception instanceof Zend_Db_Exception)
      {
        $log = "emerg";
        $this->getResponse()->setHttpResponseCode(503);
      }
      else
      {
        $log = "alert";
        $this->getResponse()->setHttpResponseCode(503);
      }

      $this->_response->clearBody();

      /*if(Zend_Registry::get('config') == true)
      {
        // On affiche l'erreur
      }

      Zend_Registry::get('log')->$log($error->exception);*/
      $this->view->message = $error->exception;
      }
  }