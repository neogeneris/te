<?php

class TEParser extends Parser{
	
	public function __construct($objectParent = null){
		
		$this->objectParent = & $objectParent; 

		//Variables globales
		$this->addPattern(
			(new GlobalsPattern())
			    ->setAction(array($this,'replaceGlobals'))
		);
		//GetText  - Traduce los literales
		$this->addPattern(
			(new GetTextPattern())
			    ->setAction(array($this,'replaceLiteral'))
		);
		//Bloques
		$this->addPattern(
			(new BlockPattern())
			    ->setAction(array($this,'createBlock'))
		);
			
	}
	
	
	
	
	public function replaceGlobals($matches){
		$global = $matches['global'];
		$user_defined_constants = get_defined_constants(true)['user'];
		if(isset($user_defined_constants[$global])){
			
		   return $user_defined_constants[$global];
		}   
		//else{ 
		//   return $this->getGlobal($global);
		//}
	}
	
	public function replaceLiteral($matches){
		$text = $matches['text'];
		if(method_exists($this,'t')){
			return $this->t(trim($text) );
		}
		return $text;
	}
	
	public function createBlock($matches){
		$properties=array();
		#print_r($matches);echo "<hr>";
		$objectClass = trim($matches['class']);  
		$id          = trim($matches['id']); 
		$argument    = $matches['argument']; 
		$text        = $matches['text'];
		$tag         = trim($matches['tag']);
		
		$propertyPattern = new PropertyPattern();
		
		$matches = $propertyPattern->matchAll($argument);
		if($matches)
			foreach($matches as $match){
				$property = $match[1];
				$value    = $match[3];
				$properties[$property] = $this->parse($value);
		}
		//Construye un block
		if($objectClass && $id){
			$self = & $this;
			$objectParent = & $self;
			$objectRoot   = isset($this->objectRoot)?$this->objectRoot:$self;
			$mediators    = isset($this->mediators)?$this->mediators:null;
			
			
			$builder = new BlockBuilder();
			
			$builder->createBlock($objectClass,$objectParent);
			$builder->buildObjectClass($objectClass);
			$builder->buildObjectId($id);
			$builder->buildObjectRoot($objectRoot);
			$builder->buildObjectParent($objectParent);
			$builder->buildMediator($mediators);
			//$builder->buildObjectChild($this);  //Bug 
			$builder->buildTextBlock($text);
			
			if($properties)
				foreach($properties as $property => $value){
					$builder->buildProperty($property,$value);
				}
				
			
			$this->$id = $builder->getBlock();
			$this->objectChilds[$id] = & $this->$id; //Paso la referencia del objeto a la lista de hijos.
			
			if(method_exists($this->$id,'initialize')){
				$this->$id->initialize();
			}
			
			if(method_exists($this->$id,'before')){
				$this->$id->before();
			}
			if(method_exists($this->$id,'show')){
				$result = $this->$id->show();
			}else{
				$result = $this->$id->parse($this->$id->block);
			}
			if(method_exists($this->$id,'after')){
				$result_after = $this->$id->after($result);
				$result = empty($result_after)?$result:$result_after; 
			}
			return $result;
		}
		
		
		
		//Llamar a un metodo o propiedad
		if(!$objectClass && $id){
			if(method_exists($this,$id)){                //Si el metodo existe dentro de este objeto
				#return $this->$id($properties,$text);    //Ejecuta el metodo y retorna el resultado, para ser reemplazado.
				$properties['block'] = $text;
				return $this->callMethod($id,$properties); //Ejecuta el metodo y retorna el resultado, para ser reemplazado.
			}elseif(isset($this->$id)){                  //Si exite un atributo del objeto bajo ese id
			    return $this->$id;                       //retorna el valor del atributo.
			}elseif(!empty($text)){                      //si no exite como metodo, ni atributo, pero contiene texto
				$this->$id = $this->parse($text);		 //lo agrego como un atributo en este objeto y parseo su contenido.		
			}
			
		}
		if($tag){
			return $this->callSubBlockTag($tag);
		}
		
		
	}//end createBlock
	
	protected function callSubBlockTag($tag){ 
		$vars = explode(".",$tag);   

		$ref= &$this; //referencia actual del objecto.
		for($i=0; $i<count($vars); $i++){
			if(isset($ref)){
				$ref=&$ref->$vars[$i];
			}else{
				return "";
			}
		}
		//Polimorfismo 
		if(is_scalar($ref)){
			return $ref;
		}elseif(is_array($ref)){
			return implode(",",$ref);
		}elseif(is_object($ref)){
			return json_encode($ref);
		}else{
			return "";
		}
		
	}//end method

	protected function callMethod($method,array $args=array()){
		
		$reflection = new ReflectionMethod($this, $method); 

        $pass = array(); 
        foreach($reflection->getParameters() as $param) 
        { 
			/* @var $param ReflectionParameter */ 
			if(isset($args[$param->getName()])) 
			{ 
				$pass[] = $args[$param->getName()]; 
			} 
			else 
			{ 
				$pass[] = $param->getDefaultValue(); 
			} 
        } 
		if($reflection->isPublic()){
			return $reflection->invokeArgs($this, $pass);
		}		
	}
	
}//end class

?>