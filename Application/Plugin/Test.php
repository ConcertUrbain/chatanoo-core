<?php

	class Plugin_Test extends Plugin_Abstract
	{
		public function execute()
		{
			$cs = new Service_Comments();
			$params = $this->getParams();
			return $cs->getCommentById($params[0]);
		}

	}