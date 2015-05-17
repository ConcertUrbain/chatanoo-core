<?php

  class ConnectionController extends Service_Controller_Abstract
  {
    protected $_serviceName = "Service_Connection";
    
    protected $_notifyName = "connection";
    protected $_notifyActions = array();
  }