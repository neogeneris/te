<?php
/*
	@
	\<\s*?(\w+)((?:\b(?:\'[^\']*\'|"[^"]*"|[^\>])*)?)\>
	((?:(?>[^\<]*)|(?R))*)
	\<\/\s*?\\1(?:\b[^\>]*)?\>
	|\<\s*(\w+)(\b(?:\'[^\']*\'|"[^"]*"|[^\>])*)?\/?\>
	@uxis  
  
	$pattern = "/<([\w]+)([^>]*?) (([\s]*\/>)| (>((([^<]*?|<\!\-\-.*?\-\->)| (?R))*)<\/\\1[\s]*>))/xsm";
	b(?>m|(?R))*e
 * */


class BlockPattern extends Pattern {

	
	public function __construct(){
		
		$class      = '[\w\_]+';
		$id         = '[\w\_]+';
		$var        = '[\w\.\_]+';  
		
		$beginmark  = '\{';
		$endmark    = '\}';
		$openmark   = '[\[\|]';
		$closemark  = '[\]\/\|]';
		
		$properties = '(?:\'[^\']*\'|"[^"]*"|[^}])*';
		$argument   = $openmark.'\s*(?:'.$properties.')?';
		$tag        = $beginmark.'\s*\$?('.$var.')\s*'.$endmark;
		
		
        /**
         * Patron regular recursivo por la derecha.  
         * En la forma: b(?: c ( (?>	m |(?R)	)+ )?e)?
         * Donde b : es el comienzo del patron 
         *		 c : un cierre opcional
         *       m : son los patrones que puede estar en el medio
         *       e : es el final del patrón.
         */
		
		$pattern = "/{\s*(?:($class)\s+)?($id)\s*($argument)?
		              (?:
					      [\s]*$closemark\s*\}
						  | 
						  (?:
						      }(
								(?>
									[^{]+ 
									|(?R)
								)+
							   )?{\s*$closemark\s*\\2[\s]*}
					      )
					  )|$tag/xsm";
		$this->pattern = $pattern;
		$this->map = array('match','class','id','argument','text','tag');
	}
}//end class

?>