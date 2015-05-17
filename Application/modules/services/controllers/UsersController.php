<?php

  class UsersController extends Service_Controller_Abstract
  {
    protected $_serviceName = "Service_Users";
    
    protected $_notifyName = "users";
    protected $_notifyActions = array(
      "addUser",
      "setUser",
      "deleteUser",
      "banUser",
      "addDataIntoVo",
      "removeDataFromVo"
    );
  }