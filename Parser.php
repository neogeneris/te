/**
 * Generic Parser
 * @autor: neo.generis@gmail.com
 */

<?php

class Parser{
	
	private $patterns = array();
	
	public function addPattern(Pattern $pattern){
		$this->patterns[] = $pattern;
	}
	
    public function parse($text){
		foreach($this->patterns as $pattern){
			$text = $pattern->replace($text);
			
		}
		return $text;
	} 
	
}
?>
