<?php

  class Search_AssocField
  {
    public $field;
    public $value;

    public function __construct($assocFieldArray = null)
    {
      if($assocFieldArray)
      {
        foreach($assocFieldArray as $key=>$value)
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