<?php
/*
	$pattern = "/<([\w]+)([^>]*?) (([\s]*\/>)| (>((([^<]*?|<\!\-\-.*?\-\->)| (?R))*)<\/\\1[\s]*>))/xsm";
	b(?>m|(?R))*e
 * */


class GlobalsPattern extends Pattern {
	
	public function __construct(){
		$begin_mark = "\{";
		$end_mark   = "\}";
		$var        = "\w+";
		$globals    = "\@(".$var.")";
		$pattern    = "/".$begin_mark."\s*".$globals."\s*".$end_mark."/";
		
		$this->pattern = $pattern;
		$this->map = array('match','global');
	}
	
}//end class

?>