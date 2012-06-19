<?php

	class CommentsController extends Service_Controller_Abstract
	{
		protected $_serviceName = "Service_Comments";
		
		protected $_notifyName = "comments";
		protected $_notifyActions = array(
			"addComment",
			"setComment",
			"setItemOfComment",
			"deleteComment",
			"setUserOfVo",
			"validateVo",
			"addDataIntoVo",
			"addVoteIntoItemPatch",
			"removeDataFromVo"
		);
	}