<?php

	class ItemsController extends Service_Controller_Abstract
	{
		protected $_serviceName = "Service_Items";
		
		protected $_notifyName = "items";
		protected $_notifyActions = array(
			"addItem",
			"setItem",
			"deleteItem",
			"addCommentIntoItem",
			"addCommentIntoItemPatch",
			"removeCommentFromItem",
			"addMediaIntoItem",
			"removeMediaFromItem",
			"validateVo",
			"addMetaIntoVo",
			"removeMetaFromVo",
			"setUserOfVo",
			"addDataIntoVo",
			"removeDataFromVo"
		);
	}