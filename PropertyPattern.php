<?php

class PropertyPattern extends Pattern{
	
	
	
	public function __construct(){
		$id      = '\w+';
		$value   = '.*' ;
		$pattern = "/(". $id .")\s*=\s*(['\"])\s*(".  $value  .")\s*\g{2}/Uxs";
		
		$this->pattern = $pattern;
		
	}
	
}

?>