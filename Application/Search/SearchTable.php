<?php

	class Search_SearchTable
	{
		public $id;
		public $type;
		public $voClass;
		public $tableClass;
		public $searchFields;
		public $resultAssoc;

		public function __construct($searchTableArray = null)
		{
			if($searchTableArray)
			{
				foreach($searchTableArray as $key=>$value)
				{
					switch($key)
					{
						case 'searchField':
							if(!array_key_exists(0, $value))
									$value = array($value);

							$this->searchFields = array();
							foreach($value as $key=>$searchField)
								array_push($this->searchFields, new Search_SearchField($searchField));
							break;

						case 'resultAssoc':
							if(!$value['assoc'])
								break;

							$this->resultAssoc = array();
							foreach($value['assoc'] as $key=>$assoc)
								array_push($this->resultAssoc, new Search_Assoc($assoc));
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