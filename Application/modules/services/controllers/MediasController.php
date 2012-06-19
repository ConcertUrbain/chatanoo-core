<?php

	class MediasController extends Service_Controller_Abstract
	{
		protected $_serviceName = "Service_Medias";
		
		protected $_notifyName = "medias";
		protected $_notifyActions = array(
			"addMedia",
			"setMedia",
			"deleteMedia",
			"setUserOfMedia",
			"addMetaIntoMedia",
			"removeMetaFromMedia",
			"validateMedia",
			"addDataIntoMedia",
			"removeDataFromMedia"
		);
	}