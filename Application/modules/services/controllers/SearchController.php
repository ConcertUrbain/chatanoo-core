<?php

  class SearchController extends Service_Controller_Abstract
  {
    protected $_serviceName = "Service_Search";
    
    protected $_notifyName = "search";
    protected $_notifyActions = array(
      "addMeta",
      "setMeta",
      "deleteMeta"
    );
  }