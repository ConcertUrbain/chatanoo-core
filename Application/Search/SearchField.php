<?php

  class Search_SearchField
  {
    public $name;
    public $type;
    public $pertinence;
    public $required;
    public $value;

    public function __construct($searchFieldArray = null)
    {
      if($searchFieldArray)
      {
        foreach($searchFieldArray as $key=>$value)
        {
          $this->$key = $value;
        }
      }
    }

    public function __set($name, $value) {
      if($name == 'val')
        $this->value = $value;
    }

    public function __get($name) {}

  }