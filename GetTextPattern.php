<?php


class GetTextPattern extends Pattern {
	
	public function __construct(){
		$begin_mark = "\{";
		$end_mark   = "\}";
		$pattern    = "/\_\((.*)\)/Um";
		
		$this->pattern = $pattern;
		$this->map = array('match','text');
	}
	
}//end class

?>