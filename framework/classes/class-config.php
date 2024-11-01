<?php

	namespace WPJump{


	class Config{
	
		private static $_instance = null;
		
		public $values;		
				
	   	private function __construct(){}


	    public static function getInstance(){
	        if (self::$_instance == null) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
		
		//Getters
	    function __get($key){
	        return $this->values[$key];
	    }

	    //Setters   
	    function __set($key, $value){
	        $this->values[$key]=$value;
	    }
	
	
	}
}

?>