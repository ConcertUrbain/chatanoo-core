<?php

	class Search_Assoc
	{

		public $searchTable;
		public $assocTableClass;
		public $assocFields;

		public function __construct($assocArray = null)
		{
			if($assocArray)
			{
				foreach($assocArray as $key=>$value)
				{
					switch($key)
					{
						case 'assocField':
							if(!array_key_exists(0, $value))
									$value = array($value);

							$this->assocFields = array();
							foreach($value as $key=>$assocField)
								array_push($this->assocFields, new Search_AssocField($assocField));
							break;

						default:
							$this->$key = $value;
							break;
					}
				}
			}
		}

		public function __set($name, $value) {}
		public function __get($name) {}

	}