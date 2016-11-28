<?php

abstract class  Pattern {
	
	public $pattern;
	public $action;
	public $map = array();
	
	public function replace($text){
		
		return preg_replace_callback($this->pattern,array($this,'callback'),$text);
	}//end replace
	
	public function  setAction(callable $action){
		$this->action = $action;
		return $this;
	}//end setAction
	
	public function matchAll($text){
		preg_match_all($this->pattern,$text,$matches, PREG_PATTERN_ORDER);
		array_unshift($matches, null);
		return  $this->mapping(call_user_func_array('array_map', $matches));
	}//matchAll
	
	//Aplica el mapa $this->map al arreglo matches del patron encontrado,combinando la clave de map con el valor de matches.
	protected function mapping(array $matches=array()){
		$full_matches=array(); //El numero de matches coincide con el numero de parentesis escritos en el patron regular.
		
		if(is_array($this->map) && count($this->map)>=count($matches)   ){
			foreach($this->map as $index=>$key){
				if(!array_key_exists($key,$full_matches) || isset($matches[$index])){
					$full_matches[$key]=isset($matches[$index])?$matches[$index]:null;
				}
			}
			return $full_matches;
		}
		return $matches;
	}
	//Intercepta el action para colocar como argumento los matches luego de aplicar el correspondiente mapping.
	protected function callback(array $matches){
			return isset($this->action)?call_user_func($this->action,$this->mapping($matches)):null;
	}
}
?>