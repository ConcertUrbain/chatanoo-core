<?php

  class QueriesController extends Service_Controller_Abstract
  {
    protected $_serviceName = "Service_Queries";
    
    protected $_notifyName = "queries";
    protected $_notifyActions = array(
      "addQuery",
      "setQuery",
      "deleteQuery",
      "addItemIntoQuery",
      "removeItemFromQuery",
      "addMediaIntoQuery",
      "removeMediaFromQuery",
      "addMetaIntoVo",
      "removeMetaFromVo",
      "setUserOfVo",
      "validateVo",
      "addDataIntoVo",
      "removeDataFromVo"
    );
  }