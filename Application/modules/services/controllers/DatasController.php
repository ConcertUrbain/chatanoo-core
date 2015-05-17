<?php

  class DatasController extends Service_Controller_Abstract
  {
    protected $_serviceName = "Service_Datas";
    
    protected $_notifyName = "datas";
    protected $_notifyActions = array(
      "addData",
      "setData",
      "deleteData"
    );
  }